<?php

namespace Modules\Farmer\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCommodityRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:80', Rule::unique('commodities', 'name')],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
