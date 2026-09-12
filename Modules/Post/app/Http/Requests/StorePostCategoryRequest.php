<?php

namespace Modules\Post\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePostCategoryRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:50', Rule::unique('post_categories', 'name')],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama kategori berita wajib diisi.',
            'name.string' => 'Nama kategori berita harus berupa teks.',
            'name.max' => 'Nama kategori berita maksimal :max karakter.',
            'name.unique' => 'Nama kategori berita sudah digunakan.',
        ];
    }
}
