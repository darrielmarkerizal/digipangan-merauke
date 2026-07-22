<?php

namespace Modules\Page\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSiteSettingsRequest extends FormRequest
{
    /**
     * Allowed setting keys and their per-type validation rules.
     */
    private const FIELD_RULES = [
        'about_background' => ['nullable', 'string'],
        'about_purpose' => ['nullable', 'string'],
        'admin_contact_name' => ['nullable', 'string', 'max:150'],
        'admin_contact_phone' => ['nullable', 'string', 'max:20'],
        'admin_contact_email' => ['nullable', 'email', 'max:150'],
    ];

    public function rules(): array
    {
        $rules = [];

        foreach (self::FIELD_RULES as $key => $fieldRules) {
            $rules[$key] = array_merge(['sometimes'], $fieldRules);
        }

        return $rules;
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $unknown = array_diff(array_keys($this->all()), array_keys(self::FIELD_RULES));

            foreach ($unknown as $key) {
                $validator->errors()->add($key, "Pengaturan '{$key}' tidak dikenal.");
            }
        });
    }

    public function authorize(): bool
    {
        return true;
    }
}
