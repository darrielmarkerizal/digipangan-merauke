<?php

namespace Modules\Region\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreVillageRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => [
                'required', 'string', 'max:100',
                Rule::unique('villages', 'name')->where(
                    fn ($query) => $query->where('region_id', $this->input('region_id'))
                ),
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
