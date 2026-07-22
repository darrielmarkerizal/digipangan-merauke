<?php

namespace Modules\Region\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateVillageRequest extends FormRequest
{
    public function rules(): array
    {
        $id = $this->route('village');

        return [
            'name' => [
                'required', 'string', 'max:100',
                Rule::unique('villages', 'name')
                    ->where(fn ($query) => $query->where('region_id', $this->input('region_id')))
                    ->ignore($id),
            ],
            'region_id' => ['required', 'exists:regions,id'],
            'is_active' => ['boolean'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
