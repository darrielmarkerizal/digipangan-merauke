<?php

namespace Modules\User\Http\Requests;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:100'],
            'email' => [
                'sometimes',
                'email',
                'max:150',
                Rule::unique('users', 'email')->ignore($this->user()->id)->whereNull('deleted_at'),
            ],
            'password' => ['sometimes', 'nullable', 'string', 'min:8', 'confirmed'],
            'avatar_uuid' => ['sometimes', 'nullable', 'string', 'uuid'],
            'remove_avatar' => ['sometimes', 'boolean'],
        ];
    }
}
