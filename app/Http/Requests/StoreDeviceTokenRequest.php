<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDeviceTokenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'store_key' => ['required', 'uuid'],
            'fcm_token' => ['required', 'string', 'max:4096'],
            'platform' => ['nullable', Rule::in(['android', 'ios', 'web'])],
        ];
    }
}
