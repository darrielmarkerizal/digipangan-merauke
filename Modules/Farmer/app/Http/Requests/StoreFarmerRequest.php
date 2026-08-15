<?php

namespace Modules\Farmer\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Modules\Farmer\Http\Requests\Concerns\ValidatesFarmerLocation;

class StoreFarmerRequest extends FormRequest
{
    use ValidatesFarmerLocation;

    protected function prepareForValidation(): void
    {
        if ($this->user()?->isDistrictAdmin() && ! $this->filled('region_id')) {
            $this->merge([
                'region_id' => $this->user()->getAssignedRegionId(),
            ]);
        }
    }

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

    public function withValidator(Validator $validator): void
    {
        $this->validateFarmerLocationConsistency($validator);
    }
}
