<?php

namespace App\Http\Requests\Api\Product;

use Illuminate\Foundation\Http\FormRequest;

class ProductStoreRequest extends FormRequest
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
        return [
            'shop_id' => 'required|exists:shops,id',
            'product_link_id' => 'required|unique:products,product_link_id|exists:product_links,id',
            'name' => 'required|string|max:255',
            'voltage' => 'nullable|string|max:50',
            'capacity' => 'nullable|string|max:50',
            'cca' => 'nullable|string|max:50',
            'polarity' => 'nullable|string|max:50',
            'terminal_type' => 'nullable|string|max:50',
            'bottom_fixation' => 'nullable|boolean',
            'size_standard' => 'nullable|string|max:100',
            'technology' => 'nullable|string|max:100',
            'dimensions' => 'nullable|string|max:100',
            'origin' => 'nullable|string|max:100',
            'brand' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'serviceable' => 'nullable|string|max:100',
        ];
    }
}
