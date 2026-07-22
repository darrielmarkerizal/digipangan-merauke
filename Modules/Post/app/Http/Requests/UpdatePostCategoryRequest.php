<?php

namespace Modules\Post\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePostCategoryRequest extends FormRequest
{
    public function rules(): array
    {
        $id = $this->route('post_category');

        return [
            'name' => ['required', 'string', 'max:50', Rule::unique('post_categories', 'name')->ignore($id)],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
