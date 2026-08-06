<?php

namespace Modules\Farmer\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttachFarmerToGroupRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'farmer_id' => ['required', 'integer', 'exists:farmers,id'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
