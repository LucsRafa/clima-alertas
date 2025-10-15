<?php

namespace App\Http\Requests\Alert;

use Illuminate\Foundation\Http\FormRequest;

class StoreAlertRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'city_id' => ['required', 'exists:cities,id'],
            'temp_min' => ['nullable', 'numeric'],
            'temp_max' => ['nullable', 'numeric'],
            'rain' => ['nullable', 'boolean'],
            'notify_at' => ['required', 'date'],
            'channel' => ['required', 'in:email,telegram'],
            'telegram_chat_id' => ['nullable', 'string'],
            'active' => ['sometimes', 'boolean'],
        ];
    }
}

