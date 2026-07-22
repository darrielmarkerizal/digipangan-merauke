<?php

namespace Modules\Farmer\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCommodityRequest extends FormRequest
{
    public function rules(): array
    {
        $id = $this->route('commodity');

        return [
            'name' => ['required', 'string', 'max:80', Rule::unique('commodities', 'name')->ignore($id)],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
