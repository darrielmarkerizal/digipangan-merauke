<?php

namespace Modules\User\Http\Requests;

use App\Enums\UserRole;
use App\Http\Requests\BaseFormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;
use Modules\Farmer\Http\Requests\Concerns\ValidatesFarmerLocation;

class StoreUserRequest extends BaseFormRequest
{
    use ValidatesFarmerLocation;

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'is_active' => ['sometimes', 'boolean'],
            'roles' => ['sometimes', 'array', 'max:1'],
            'roles.*' => ['string', 'distinct', Rule::in(UserRole::values())],
            'region_id' => [
                'nullable',
                Rule::requiredIf(fn () => $this->hasRole(UserRole::DistrictAdmin->value) || $this->hasRole(UserRole::Farmer->value)),
                Rule::exists('regions', 'id'),
            ],
            'phone' => [
                'nullable',
                'string',
                'max:20',
                Rule::requiredIf(fn () => $this->hasRole(UserRole::Farmer->value)),
            ],
            'village_id' => ['nullable', 'integer', 'exists:villages,id'],
            'farmer_group_id' => ['nullable', 'integer', 'exists:farmer_groups,id'],
            'land_area_ha' => ['nullable', 'numeric', 'min:0'],
            'commodities' => ['nullable', 'array'],
            'commodities.*' => ['integer', 'exists:commodities,id'],
            'avatar_uuid' => ['sometimes', 'string', 'uuid'],
        ];
    }

    private function hasRole(string $role): bool
    {
        return in_array($role, (array) $this->input('roles', []), true);
    }

    public function withValidator(Validator $validator): void
    {
        $this->validateFarmerLocationConsistency($validator);
    }
}
