<?php

namespace Modules\Farmer\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommodityRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:80'
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
