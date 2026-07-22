<?php

namespace Modules\Post\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Post\Models\Post;

class StorePostRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'post_category_id' => ['required', 'integer', 'exists:post_categories,id'],
            'title' => ['required', 'string', 'max:200'],
            'body' => ['required', 'string'],
            'status' => ['nullable', Rule::in([Post::STATUS_DRAFT, Post::STATUS_PUBLISHED])],
            'published_at' => ['nullable', 'date'],
            'cover' => ['nullable', 'string'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
