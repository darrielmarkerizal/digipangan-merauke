<?php

namespace Modules\Product\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUnitRequest extends FormRequest
{
    public function rules(): array
    {
        $id = $this->route('unit');

        return [
            'name' => ['required', 'string', 'max:30', Rule::unique('units', 'name')->ignore($id)],
            'symbol' => ['required', 'string', 'max:10', Rule::unique('units', 'symbol')->ignore($id)],
            'is_active' => ['boolean'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
