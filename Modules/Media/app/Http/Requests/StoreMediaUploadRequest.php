<?php

namespace Modules\Media\Http\Requests;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreMediaUploadRequest extends BaseFormRequest
{
    public function rules(): array
    {
        // Every media collection in the app (avatar, farmer photo, product
        // photos, region cover/gallery, partner logo, post cover) stores
        // raster images. Restrict to safe raster types and cap the size so an
        // authenticated user (including self-registered farmers) cannot upload
        // SVG/HTML payloads or exhaust disk with oversized files.
        return [
            'file' => ['required', 'file', 'image', 'mimes:jpeg,jpg,png,webp,gif', 'max:8192'],
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'Tidak ada berkas yang diunggah.',
            'file.image' => 'Berkas harus berupa gambar.',
            'file.mimes' => 'Format gambar harus JPG, PNG, WEBP, atau GIF.',
            'file.max' => 'Ukuran gambar maksimal 8 MB.',
        ];
    }

    /**
     * This endpoint is only ever hit via XHR (axios), so always answer with a
     * JSON 422 rather than a redirect, regardless of the request's Accept header.
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => $validator->errors()->first('file') ?: 'Berkas tidak valid.',
            'errors' => $validator->errors(),
        ], 422));
    }
}
