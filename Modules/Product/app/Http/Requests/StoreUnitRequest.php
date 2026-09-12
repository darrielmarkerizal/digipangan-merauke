<?php

namespace Modules\Product\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUnitRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:30', Rule::unique('units', 'name')],
            'symbol' => ['required', 'string', 'max:10', Rule::unique('units', 'symbol')],
            'is_active' => ['boolean'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama satuan wajib diisi.',
            'name.string' => 'Nama satuan harus berupa teks.',
            'name.max' => 'Nama satuan maksimal :max karakter.',
            'name.unique' => 'Nama satuan sudah digunakan.',
            'symbol.required' => 'Simbol satuan wajib diisi.',
            'symbol.string' => 'Simbol satuan harus berupa teks.',
            'symbol.max' => 'Simbol satuan maksimal :max karakter.',
            'symbol.unique' => 'Simbol satuan sudah digunakan.',
            'is_active.boolean' => 'Status aktif harus berupa pilihan ya atau tidak.',
        ];
    }
}
