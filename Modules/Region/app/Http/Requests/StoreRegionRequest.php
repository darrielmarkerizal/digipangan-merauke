<?php

namespace Modules\Region\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRegionRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:80'],
            'description' => ['nullable', 'string'],
            'agricultural_potential' => ['nullable', 'string'],
            'area_km2' => ['nullable', 'numeric', 'min:0'],
            'population' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
            'cover' => ['nullable', 'string'],
            'gallery' => ['nullable', 'array'],
            'gallery.*' => ['string'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
