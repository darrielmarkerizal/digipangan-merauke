<?php

namespace Modules\Farmer\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Farmer\Http\Requests\Concerns\ValidatesFarmerLocation;

class StoreFarmerGroupRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:150', Rule::unique('farmer_groups', 'name')],
            'region_id' => ['required', 'exists:regions,id'],
            'village_id' => ['nullable', 'exists:villages,id'],
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
