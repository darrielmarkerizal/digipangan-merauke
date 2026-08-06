<?php

namespace Modules\Farmer\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Farmer\Models\FarmerGroup;

class StoreFarmerRegistrationRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->whereNull('deleted_at')],
            'phone' => ['required', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'region_id' => ['required', 'integer', 'exists:regions,id'],
            'village_id' => ['nullable', 'integer', 'exists:villages,id'],
            'farmer_group_id' => ['nullable', 'integer', 'exists:farmer_groups,id'],
            'land_area_ha' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function authorize(): bool
    {
        return true;
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
