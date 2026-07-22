<?php

namespace Modules\Product\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductCategoryRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:50', Rule::unique('product_categories', 'name')],
            'sort_order' => ['integer', 'min:0'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
