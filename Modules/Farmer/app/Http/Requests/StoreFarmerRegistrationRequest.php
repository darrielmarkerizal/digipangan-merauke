<?php

namespace Modules\Farmer\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Farmer\Http\Requests\Concerns\ValidatesFarmerLocation;

class StoreFarmerRegistrationRequest extends FormRequest
{
    use ValidatesFarmerLocation;

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            // Plain unique (incl. soft-deleted rows) to match the physical
            // unique index on users.email; a partial index does not exist.
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')],
            'phone' => ['required', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'region_id' => ['required', 'integer', 'exists:regions,id'],
            'village_id' => ['nullable', 'integer', 'exists:villages,id'],
            'farmer_group_id' => ['nullable', 'integer', 'exists:farmer_groups,id'],
            'land_area_ha' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.max' => 'Nama lengkap maksimal 120 karakter.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'email.unique' => 'Alamat email ini sudah terdaftar. Silakan gunakan email lain atau masuk ke akun Anda.',
            'phone.required' => 'Nomor WhatsApp / telepon wajib diisi.',
            'phone.max' => 'Nomor telepon maksimal 20 karakter.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal terdiri dari 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'region_id.required' => 'Distrik / kawasan wajib dipilih.',
            'region_id.exists' => 'Distrik / kawasan yang dipilih tidak valid.',
            'village_id.exists' => 'Desa / kampung yang dipilih tidak valid.',
            'farmer_group_id.exists' => 'Kelompok tani yang dipilih tidak valid.',
            'land_area_ha.numeric' => 'Luas lahan harus berupa angka.',
            'land_area_ha.min' => 'Luas lahan tidak boleh kurang dari 0.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $this->validateFarmerLocationConsistency($validator);
    }
}
