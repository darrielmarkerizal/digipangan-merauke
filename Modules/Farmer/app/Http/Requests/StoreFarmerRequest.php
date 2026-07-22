<?php

namespace Modules\Farmer\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFarmerRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'region_id' => ['required', 'integer', 'exists:regions,id'],
            'village_id' => ['nullable', 'integer', 'exists:villages,id'],
            'farmer_group_id' => ['nullable', 'integer', 'exists:farmer_groups,id'],
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:20'],
            'land_area_ha' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['boolean'],
            'commodities' => ['nullable', 'array'],
            'commodities.*' => ['integer', 'exists:commodities,id'],
            'photo' => ['nullable', 'string'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
