<?php

namespace App\Jobs;

use App\Mail\MerchantCredentialsMail;
use App\Models\Merchant;
use App\Models\SpecialOffer;
use App\Models\User;
use App\Models\WebhookReceipt;
use App\Services\FCM\PushNotify;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class ProcessSallaWebhook implements ShouldQueue
{
    use Queueable;

    public int $tries = 5;

    public array $backoff = [10, 60, 300, 900];

    public function __construct(public int $receiptId) {}

    public function handle(): void
    {
        $receipt = WebhookReceipt::findOrFail($this->receiptId);

        if ($receipt->processed_at) {
            return;
        }

        match ($receipt->event) {
            'app.store.authorize' => $this->authorizeMerchant($receipt),
            'specialoffer.created', 'specialoffer.updated' => $this->storeOffer($receipt, app(PushNotify::class)),
            default => throw new RuntimeException("Unsupported Salla event: {$receipt->event}"),
        };

        $receipt->update([
            'processed_at' => now(),
            'failed_at' => null,
            'error' => null,
        ]);
    }

    public function failed(Throwable $exception): void
    {
        WebhookReceipt::whereKey($this->receiptId)->update([
            'failed_at' => now(),
            'error' => Str::limit($exception->getMessage(), 2000, ''),
        ]);
    }

    private function authorizeMerchant(WebhookReceipt $receipt): void
    {
        $payload = $receipt->payload;
        $tokenData = $payload['data'];

        $response = Http::acceptJson()
            ->withToken($tokenData['access_token'])
            ->retry(3, 250)
            ->get(config('services.salla.user_info_url'))
            ->throw()
            ->json('data');

        $merchantData = $response['merchant'] ?? [];
        $sallaId = (int) ($merchantData['id'] ?? $payload['merchant']);
        $merchant = Merchant::firstOrNew(['salla_id' => $sallaId]);

        if (! $merchant->exists) {
            $merchant->public_key = (string) Str::uuid();
        }

        $merchant->fill([
            'name' => $merchantData['name'] ?? null,
            'username' => $merchantData['username'] ?? null,
            'email' => $response['email'] ?? null,
            'mobile' => $response['mobile'] ?? null,
            'avatar' => $merchantData['avatar'] ?? null,
            'domain' => $merchantData['domain'] ?? null,
            'plan' => $merchantData['plan'] ?? null,
            'commercial_number' => $merchantData['commercial_number'] ?? null,
            'tax_number' => $merchantData['tax_number'] ?? null,
            'access_token' => $tokenData['access_token'],
            'refresh_token' => $tokenData['refresh_token'] ?? null,
            'token_type' => $tokenData['token_type'] ?? 'bearer',
            'scopes' => isset($tokenData['scope'])
                ? preg_split('/\s+/', trim($tokenData['scope']))
                : null,
            'token_expires_at' => isset($tokenData['expires'])
                ? Carbon::createFromTimestamp((int) $tokenData['expires'])
                : null,
            'installed_at' => $this->parseDate($payload['created_at'] ?? null),
        ])->save();

        $email = $response['email'] ?? null;

        if ($email && ! $merchant->user()->exists()) {
            $temporaryPassword = Str::password(20);
            User::create([
                'merchant_id' => $merchant->id,
                'name' => $merchant->name ?: $merchant->username ?: 'Merchant',
                'email' => $email,
                'password' => $temporaryPassword,
                'password_must_change' => true,
            ]);

            Mail::to($email)->queue(new MerchantCredentialsMail($email, $temporaryPassword));
        } elseif ($merchant->user()->exists()) {
            $merchant->user()->update([
                'name' => $merchant->name ?: $merchant->username ?: 'Merchant',
                'email' => $email ?: $merchant->user->email,
            ]);
        }
    }

    private function storeOffer(WebhookReceipt $receipt, PushNotify $pushNotify): void
    {
        $payload = $receipt->payload;
        $data = $payload['data'];
        $merchant = Merchant::where('salla_id', $payload['merchant'])->firstOrFail();

        $offer = SpecialOffer::updateOrCreate(
            [
                'merchant_id' => $merchant->id,
                'salla_id' => $data['id'],
            ],
            [
                'name' => $data['name'] ?? 'عرض جديد',
                'message' => $data['message'] ?? null,
                'expiry_date' => $this->parseDate($data['expiry_date'] ?? null),
                'offer_type' => $data['offer_type'] ?? null,
                'status' => $data['status'] ?? null,
                'buy' => $data['buy'] ?? null,
                'get' => $data['get'] ?? null,
                'raw_data' => $data,
                'salla_created_at' => $this->parseDate($data['created_at'] ?? null),
                'salla_updated_at' => $this->parseDate($data['updated_at'] ?? null),
            ],
        );

        if ($receipt->event !== 'specialoffer.created' || $offer->notification_sent_at) {
            return;
        }

        $pushNotify->sendToTopic(
            $merchant->notificationTopic(),
            'عرض جديد: '.$offer->name,
            $offer->message ?: 'اكتشف العرض الجديد الآن',
            [
                'type' => 'special_offer',
                'offer_id' => $offer->salla_id,
                'store_key' => $merchant->public_key,
                'event' => $receipt->event,
            ],
        );

        $offer->update(['notification_sent_at' => now()]);
    }

    private function parseDate(mixed $value): ?Carbon
    {
        if ($value === null || $value === '') {
            return null;
        }

        return Carbon::parse($value);
    }
}
