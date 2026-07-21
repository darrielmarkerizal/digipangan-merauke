<?php

namespace Modules\Product\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUnitRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:30', 'symbol' => 'required|string|max:10', 'is_active' => 'boolean'
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
