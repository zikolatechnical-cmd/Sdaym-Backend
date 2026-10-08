<?php

namespace App\SallaWebHooks;

use App\Mail\MerchantCredentialsMail;
use App\Models\User;
use App\Traits\HttpClientTrait;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AppStoreAuthorizeEvent extends EventHandlerManager
{
    use HttpClientTrait;

    public function process(): mixed
    {
        //             Mail::to("yousefalsaadany5@gmail.com")->send(new MerchantCredentialsMail("yousefalsaadany5@gmail.com", "123456789"));
        // return "done";
        $accessToken = 'Bearer '.request()->data['access_token'];
        $getMerchantData = $this->get('https://accounts.salla.sa/oauth2/user/info', ['Authorization' => $accessToken]);
        if ($getMerchantData->successful()) {
            $responseData = $getMerchantData->json()['data'];
            $merchant = $responseData['merchant'];
            // $password  = Str::random(16);
            $password = '123456789';
            $storeMerchant = User::firstOrCreate([
                'store_id' => $responseData['merchant'],
            ], [
                'name' => $merchant['name'],
                'username' => $merchant['username'],
                'store_id' => $merchant['id'],
                'hash_id' => md5($merchant['id']),
                'email' => $responseData['email'],
                'password' => $password,
                'mobile' => $responseData['mobile'],
                'avatar' => $merchant['avatar'],
                'domain' => $merchant['domain'],
                'plan' => $merchant['plan'],
                'commercial_number' => $merchant['commercial_number'],
                'tax_number' => $merchant['tax_number'],
                'access_token' => request()->data['access_token'],
                'refresh_token' => request()->data['refresh_token'],
                'token_type' => request()->data['token_type'],
            ]);

            return 'done';
        } else {
            return 'Error: '.$getMerchantData->json()['message'];
        }
    }
}
