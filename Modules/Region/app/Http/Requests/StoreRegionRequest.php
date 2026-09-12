<?php

namespace Modules\Region\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRegionRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:80', Rule::unique('regions', 'name')],
            'description' => ['nullable', 'string'],
            'agricultural_potential' => ['nullable', 'string'],
            'area_km2' => ['nullable', 'numeric', 'min:0'],
            'population' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
            'cover' => ['nullable', 'uuid'],
            'gallery' => ['nullable', 'array'],
            'gallery.*' => ['uuid'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
