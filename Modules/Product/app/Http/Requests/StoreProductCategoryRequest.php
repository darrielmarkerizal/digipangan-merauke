<?php

namespace Modules\Product\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductCategoryRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:50', 'sort_order' => 'integer|min:0'
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
