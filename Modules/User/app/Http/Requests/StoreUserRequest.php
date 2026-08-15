<?php

namespace Modules\User\Http\Requests;

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
            'roles' => ['sometimes', 'array'],
            'roles.*' => ['string', Rule::exists('roles', 'name')],
            'region_id' => [
                'nullable',
                Rule::requiredIf(fn () => in_array('admin_distrik', (array) $this->input('roles', []))),
                Rule::exists('regions', 'id'),
            ],
            'avatar_uuid' => ['sometimes', 'string', 'uuid'],
        ];
    }
}
