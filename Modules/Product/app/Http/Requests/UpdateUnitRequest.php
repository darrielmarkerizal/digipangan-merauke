<?php

namespace Modules\Product\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUnitRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            // For update, we make fields optional if not provided, but still validate if provided
            'name' => 'required|string|max:30', 'symbol' => 'required|string|max:10', 'is_active' => 'boolean'
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
