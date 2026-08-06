<?php

namespace Modules\Farmer\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Modules\Farmer\Models\FarmerGroup;

class UpdateFarmerProfileRequest extends FormRequest
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
            'commodities' => ['nullable', 'array'],
            'commodities.*' => ['integer', 'exists:commodities,id'],
            'photo' => ['nullable', 'string'],
        ];
    }

    public function authorize(): bool
    {
        return $this->user()?->hasRole('farmer') ?? false;
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if (! $this->filled('farmer_group_id')) {
                return;
            }

            $group = FarmerGroup::find($this->input('farmer_group_id'));

            if ($group && (int) $group->region_id !== (int) $this->input('region_id')) {
                $validator->errors()->add('farmer_group_id', 'Kelompok tani yang dipilih tidak berada di wilayah yang sama.');
            }
        });
    }
}
