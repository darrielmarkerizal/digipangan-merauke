<?php

namespace Modules\Farmer\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFarmerGroupRequest extends FormRequest
{
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
}
