<?php

namespace Modules\Region\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateVillageRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        if ($this->user()?->isDistrictAdmin()) {
            $this->merge(['region_id' => $this->user()->getAssignedRegionId()]);
        }
    }

    public function rules(): array
    {
        $id = $this->route('id');

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
