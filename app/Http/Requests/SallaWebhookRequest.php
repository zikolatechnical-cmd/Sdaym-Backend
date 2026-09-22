<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SallaWebhookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'event' => ['required', 'string', Rule::in([
                'app.store.authorize',
                'specialoffer.created',
                'specialoffer.updated',
            ])],
            'merchant' => ['required', 'integer'],
            'created_at' => ['nullable', 'string'],
            'data' => ['required', 'array'],
        ];
    }
}
