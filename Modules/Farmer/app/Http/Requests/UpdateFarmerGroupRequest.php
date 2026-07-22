<?php

namespace Modules\Farmer\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFarmerGroupRequest extends FormRequest
{
    public function rules(): array
    {
        $id = $this->route('farmer_group');

        return [
            'name' => ['required', 'string', 'max:150', Rule::unique('farmer_groups', 'name')->ignore($id)],
            'region_id' => ['required', 'exists:regions,id'],
            'village_id' => ['nullable', 'exists:villages,id'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
