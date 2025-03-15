<?php

namespace App\Http\Requests\Api\Shop;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class ShopUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Получаем ID магазина из маршрута
        $shopId = $this->route('shop');

        Log::debug('Shop ID from route: ' . print_r($this->route('shop'), true));


        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('shops')->ignore($shopId),
            ],
            'url' => [
                'nullable',
                'url',
                Rule::unique('shops')->ignore($shopId),
            ],
        ];
    }
}
