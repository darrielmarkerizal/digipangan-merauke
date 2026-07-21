<?php

namespace Modules\Region\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVillageRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:100', 'region_id' => 'required|exists:regions,id', 'is_active' => 'boolean'
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
