<?php

namespace App\Console\Commands;

use App\Models\Merchant;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class RefreshTokenCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:refresh-token-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh Salla OAuth tokens before they expire';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $merchant = Merchant::query()
            ->whereNotNull('refresh_token')
            ->where('token_expires_at', '<=', now()->addDays(4))
            ->orWhereNull('token_expires_at')
            ->where('installed_at', '<=', now()->subDays(10))
            ->first();

        $response = Http::asForm()->acceptJson()->timeout(30)->post(config('services.salla.token_url'), [
            'grant_type' => 'refresh_token',
            'refresh_token' => $merchant->refresh_token,
            'client_id' => config('services.salla.client_id'),
            'client_secret' => config('services.salla.client_secret'),
        ]);

        $response->throw();

        $data = $response->json();

        $merchant->update([
            'access_token' => $data['access_token'],
            'refresh_token' => $data['refresh_token'],
            'token_type' => $data['token_type'] ?? $merchant->token_type,
            'token_expires_at' => isset($data['expires'])
                ? Carbon::createFromTimestamp((int) $data['expires'])
                : now()->addSeconds((int) ($data['expires_in'] ?? 1209600)),
        ]);
    }
}
