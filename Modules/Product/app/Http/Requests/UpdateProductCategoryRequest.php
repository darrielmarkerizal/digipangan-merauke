<?php

namespace Modules\Product\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductCategoryRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            // For update, we make fields optional if not provided, but still validate if provided
            'name' => 'required|string|max:50', 'sort_order' => 'integer|min:0'
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
