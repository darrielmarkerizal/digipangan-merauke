<?php

namespace Modules\Page\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePartnerRequest extends FormRequest
{
    public function rules(): array
    {
        $id = $this->route('partner');

        return [
            'name' => ['required', 'string', 'max:150', Rule::unique('partners', 'name')->ignore($id)],
            'website_url' => ['nullable', 'url', 'max:255'],
            'description' => ['nullable', 'string', 'max:500'],
            'sort_order' => ['integer', 'min:0'],
            'is_active' => ['boolean'],
            'logo' => ['nullable', 'string'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
