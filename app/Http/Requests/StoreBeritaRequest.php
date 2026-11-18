<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreBeritaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
         return [
            'konten'   => 'required|string|max:5000',
            'kategori' => 'nullable|string|max:100',
            'sumber'   => 'nullable|string|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'konten.required' => 'Isi berita wajib diisi.',
            'konten.max'      => 'Isi berita maksimal 5000 karakter.',
        ];
    }
}
