<?php

namespace Modules\Product\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFarmerProductRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'product_category_id' => ['required', 'integer', 'exists:product_categories,id'],
            'unit_id' => ['required', 'integer', 'exists:units,id'],
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'weight_value' => ['nullable', 'numeric', 'min:0'],
            'stock_available' => ['boolean'],
            'is_active' => ['boolean'],
            'photos' => ['nullable', 'array'],
            'photos.*' => ['string'],
        ];
    }

    public function authorize(): bool
    {
        return $this->user()?->hasRole('farmer') ?? false;
    }
}
