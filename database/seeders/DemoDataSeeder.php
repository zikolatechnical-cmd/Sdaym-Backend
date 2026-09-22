<?php

namespace Database\Seeders;

use App\Models\Merchant;
use App\Models\SpecialOffer;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public const LOGIN_EMAIL = 'test@example.com';

    public const LOGIN_PASSWORD = '12345678';

    public const STORE_KEY = '123e4567-e89b-12d3-a456-426614174000';

    public function run(): void
    {
        $merchant = Merchant::updateOrCreate(
            ['salla_id' => 123456789],
            [
                'public_key' => self::STORE_KEY,
                'name' => 'Test Store',
                'username' => 'test_store',
                'email' => self::LOGIN_EMAIL,
                'mobile' => '0500000000',
                'domain' => 'test-store.example.com',
                'plan' => 'basic',
                'access_token' => 'test-access-token',
                'refresh_token' => 'test-refresh-token',
                'token_type' => 'bearer',
                'scopes' => ['offline_access'],
                'token_expires_at' => now()->addMonth(),
                'installed_at' => now(),
            ],
        );

        User::updateOrCreate(
            ['email' => self::LOGIN_EMAIL],
            [
                'merchant_id' => $merchant->id,
                'name' => 'Test User',
                'password' => self::LOGIN_PASSWORD,
                'email_verified_at' => now(),
                'password_must_change' => false,
            ],
        );

        $offers = [
            [
                'salla_id' => 1001,
                'name' => 'خصم 20%',
                'message' => 'خصم بسيط للتجربة',
                'expiry_date' => now()->addMonth(),
                'offer_type' => 'discount',
                'status' => 'active',
                'buy' => null,
                'get' => null,
            ],
            [
                'salla_id' => 1002,
                'name' => 'اشترِ واحدًا واحصل على واحد',
                'message' => 'عرض تجريبي بسيط',
                'expiry_date' => now()->addWeeks(2),
                'offer_type' => 'buy_x_get_y',
                'status' => 'active',
                'buy' => ['quantity' => 1],
                'get' => ['quantity' => 1],
            ],
        ];

        foreach ($offers as $offerData) {
            SpecialOffer::updateOrCreate(
                [
                    'merchant_id' => $merchant->id,
                    'salla_id' => $offerData['salla_id'],
                ],
                [
                    ...$offerData,
                    'raw_data' => $offerData,
                    'salla_created_at' => now(),
                    'salla_updated_at' => now(),
                ],
            );
        }
    }
}
