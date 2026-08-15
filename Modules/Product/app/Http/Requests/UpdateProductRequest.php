<?php

namespace Modules\Product\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'product_category_id' => ['required', 'integer', 'exists:product_categories,id'],
            'unit_id' => ['required', 'integer', 'exists:units,id'],
            'farmer_id' => ['required', 'integer', 'exists:farmers,id'],
            'region_id' => ['nullable', 'integer', 'exists:regions,id'],
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'weight_value' => ['nullable', 'numeric', 'min:0'],
            'stock_available' => ['boolean'],
            'is_featured' => ['boolean'],
            'is_region_featured' => ['boolean'],
            'is_active' => ['boolean'],
            'photos' => ['nullable', 'array'],
            'photos.*' => ['string'],
            'retained_photos' => ['nullable', 'array'],
            'retained_photos.*' => ['integer'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
