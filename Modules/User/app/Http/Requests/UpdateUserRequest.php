<?php

namespace Modules\User\Http\Requests;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:100'],
            'email' => [
                'sometimes',
                'email',
                'max:150',
                Rule::unique('users', 'email')->ignore($this->route('user')),
            ],
            'password' => ['sometimes', 'nullable', 'string', 'min:8', 'confirmed'],
            'is_active' => ['sometimes', 'boolean'],
            'roles' => ['sometimes', 'array'],
            'roles.*' => ['string', Rule::exists('roles', 'name')],
            'region_id' => [
                'nullable',
                Rule::requiredIf(fn () => in_array('admin_distrik', (array) $this->input('roles', []))),
                Rule::exists('regions', 'id'),
            ],
            'avatar_uuid' => ['sometimes', 'nullable', 'string', 'uuid'],
        ];
    }
}
