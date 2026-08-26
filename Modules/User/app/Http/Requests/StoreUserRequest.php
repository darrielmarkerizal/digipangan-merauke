<?php

namespace Modules\User\Http\Requests;

use App\Enums\UserRole;
use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends BaseFormRequest
{
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
                Rule::requiredIf(fn () => in_array(UserRole::DistrictAdmin->value, (array) $this->input('roles', []), true)),
                Rule::exists('regions', 'id'),
            ],
            'avatar_uuid' => ['sometimes', 'string', 'uuid'],
        ];
    }
}
