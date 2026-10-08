<?php

namespace App\SallaWebHooks;

use App\Models\Merchant;
use App\Models\User;
use App\Traits\HttpClientTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AppStoreAuthorizeEvent extends EventHandlerManager
{
    use HttpClientTrait;

    public function process(): mixed
    {
        $accessToken = 'Bearer '.request()->data['access_token'];
        $getMerchantData = $this->get('https://accounts.salla.sa/oauth2/user/info', ['Authorization' => $accessToken]);

        if ($getMerchantData->successful()) {
            $responseData = $getMerchantData->json()['data'];
            $merchantValue = $responseData['merchant'];
            $merchantData = is_array($merchantValue) ? $merchantValue : [];
            $merchantSallaId = is_array($merchantValue)
                ? $merchantValue['id']
                : $merchantValue;
            $tokenData = request()->data;

            DB::transaction(function () use ($responseData, $merchantData, $merchantSallaId, $tokenData): void {
                $merchant = Merchant::firstOrNew([
                    'salla_id' => $merchantSallaId,
                ]);

                if (! $merchant->exists) {
                    $merchant->public_key = (string) Str::uuid();
                }

                $merchant->fill([
                    'name' => $merchantData['name'] ?? $responseData['name'] ?? null,
                    'username' => $merchantData['username'] ?? $responseData['username'] ?? null,
                    'email' => $responseData['email'] ?? null,
                    'mobile' => $responseData['mobile'] ?? null,
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
                    'installed_at' => request()->filled('created_at')
                        ? Carbon::parse(request()->input('created_at'))
                        : now(),
                ])->save();

                $user = User::firstOrNew(['merchant_id' => $merchant->id]);
                $user->name = $merchant->name ?: $merchant->username ?: 'Merchant';
                $user->email = $responseData['email'];

                if (! $user->exists) {
                    $user->password = '123456789';
                    $user->password_must_change = true;
                }

                $user->save();
            });

            return 'done';
        }

        return 'Error: '.($getMerchantData->json()['message'] ?? 'Unable to fetch merchant data');
    }
}
