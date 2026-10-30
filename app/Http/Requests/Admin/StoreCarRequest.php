<?php

namespace App\Http\Requests\Admin;

use App\Enums\BodyType;
use App\Enums\CarStatus;
use App\Enums\FuelType;
use App\Enums\Transmission;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCarRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $price = $this->input('price');

        $this->merge([
            'is_featured' => $this->boolean('is_featured'),
            'price' => is_string($price) ? preg_replace('/\D+/', '', $price) : $price,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'car_model_id' => ['required', 'integer', 'exists:car_models,id'],
            'variant' => ['nullable', 'string', 'max:100'],
            'year' => ['required', 'integer', 'between:1990,'.(date('Y') + 1)],
            'price' => ['required', 'integer', 'min:0', 'max:100000000000'],
            'mileage' => ['required', 'integer', 'min:0', 'max:2000000'],
            'transmission' => ['required', Rule::enum(Transmission::class)],
            'fuel_type' => ['required', Rule::enum(FuelType::class)],
            'engine_capacity' => ['nullable', 'integer', 'between:500,10000'],
            'body_type' => ['required', Rule::enum(BodyType::class)],
            'color' => ['nullable', 'string', 'max:50'],
            'owner_count' => ['required', 'integer', 'between:1,20'],
            'plate_area' => ['nullable', 'string', 'max:10'],
            'tax_expiry' => ['nullable', 'date'],
            'document_completeness' => ['nullable', 'string', 'max:50'],
            'condition' => ['nullable', 'string', 'max:2000'],
            'description' => ['nullable', 'string', 'max:10000'],
            'city' => ['required', 'string', 'max:100'],
            'status' => ['required', Rule::enum(CarStatus::class)],
            'is_featured' => ['boolean'],
            'features' => ['nullable', 'array'],
            'features.*' => ['integer', 'exists:features,id'],
            'photos' => ['nullable', 'array', 'max:12'],
            'photos.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
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
            'car_model_id.required' => 'Model mobil wajib dipilih.',
            'car_model_id.exists' => 'Model mobil yang dipilih tidak valid.',
            'year.required' => 'Tahun wajib diisi.',
            'year.between' => 'Tahun harus berada di antara 1990 dan tahun depan.',
            'price.required' => 'Harga wajib diisi.',
            'price.integer' => 'Harga harus berupa angka.',
            'mileage.required' => 'Kilometer wajib diisi.',
            'transmission.required' => 'Transmisi wajib dipilih.',
            'fuel_type.required' => 'Bahan bakar wajib dipilih.',
            'body_type.required' => 'Tipe bodi wajib dipilih.',
            'city.required' => 'Kota wajib diisi.',
            'status.required' => 'Status wajib dipilih.',
            'photos.*.image' => 'Setiap foto harus berupa gambar.',
            'photos.*.mimes' => 'Foto harus berformat JPG, PNG, atau WebP.',
            'photos.*.max' => 'Ukuran setiap foto maksimal 4MB.',
        ];
    }
}
