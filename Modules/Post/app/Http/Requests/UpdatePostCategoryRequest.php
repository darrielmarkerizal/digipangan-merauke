<?php

namespace Modules\Post\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePostCategoryRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            // For update, we make fields optional if not provided, but still validate if provided
            'name' => 'required|string|max:50'
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
