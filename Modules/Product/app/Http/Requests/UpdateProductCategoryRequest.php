<?php

namespace Modules\Product\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductCategoryRequest extends FormRequest
{
    public function rules(): array
    {
        $id = $this->route('product_category');

        return [
            'name' => ['required', 'string', 'max:50', Rule::unique('product_categories', 'name')->ignore($id)],
            'sort_order' => ['integer', 'min:0'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
