<?php

namespace App\Http\Requests\Guest;

use App\Enums\BodyType;
use App\Enums\Transmission;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SellCarRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'brand' => ['required', 'string', 'max:80'],
            'model' => ['required', 'string', 'max:120'],
            'year' => ['required', 'integer', 'min:1900', 'max:'.(now()->year + 1)],
            'transmission' => ['nullable', Rule::enum(Transmission::class)],
            'body_type' => ['nullable', Rule::enum(BodyType::class)],
            'mileage' => ['nullable', 'integer', 'min:0', 'max:2000000'],
            'expected_price' => ['nullable', 'integer', 'min:0', 'max:100000000000'],
            'condition' => ['nullable', 'string', 'max:2000'],
            'consent' => ['accepted'],
        ];
    }

    /**
     * Get custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama wajib diisi.',
            'phone.required' => 'Nomor WhatsApp wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'brand.required' => 'Merek mobil wajib diisi.',
            'model.required' => 'Model mobil wajib diisi.',
            'year.required' => 'Tahun mobil wajib diisi.',
            'year.min' => 'Tahun mobil tidak valid.',
            'year.max' => 'Tahun mobil tidak valid.',
            'mileage.integer' => 'Jarak tempuh harus berupa angka.',
            'expected_price.integer' => 'Harga harus berupa angka.',
            'condition.max' => 'Deskripsi kondisi maksimal 2000 karakter.',
            'consent.accepted' => 'Kamu perlu menyetujui untuk dihubungi melalui WhatsApp.',
        ];
    }
}
