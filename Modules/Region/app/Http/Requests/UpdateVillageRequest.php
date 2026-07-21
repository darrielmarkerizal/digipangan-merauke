<?php

namespace Modules\Region\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVillageRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            // For update, we make fields optional if not provided, but still validate if provided
            'name' => 'required|string|max:100', 'region_id' => 'required|exists:regions,id', 'is_active' => 'boolean'
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
