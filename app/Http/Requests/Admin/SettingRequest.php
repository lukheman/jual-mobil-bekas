<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SettingRequest extends FormRequest
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
            'settings' => ['required', 'array'],
            'settings.*' => ['nullable', 'string', 'max:5000'],
            'settings.whatsapp_number' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\-\s]+$/'],
            'settings.phone_number' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\-\s]+$/'],
            'settings.facebook_url' => ['nullable', 'url', 'max:255'],
            'settings.instagram_url' => ['nullable', 'url', 'max:255'],
            'settings.tiktok_url' => ['nullable', 'url', 'max:255'],
            'settings.youtube_url' => ['nullable', 'url', 'max:255'],
            'settings.twitter_url' => ['nullable', 'url', 'max:255'],
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
            'settings.required' => 'Data pengaturan tidak valid.',
            'settings.whatsapp_number.regex' => 'Nomor WhatsApp hanya boleh berisi angka, spasi, tanda + dan -.',
            'settings.phone_number.regex' => 'Nomor telepon hanya boleh berisi angka, spasi, tanda + dan -.',
            'settings.facebook_url.url' => 'Tautan Facebook harus berupa URL lengkap, misalnya https://facebook.com/namashowroom.',
            'settings.instagram_url.url' => 'Tautan Instagram harus berupa URL lengkap, misalnya https://instagram.com/namashowroom.',
            'settings.tiktok_url.url' => 'Tautan TikTok harus berupa URL lengkap, misalnya https://tiktok.com/@namashowroom.',
            'settings.youtube_url.url' => 'Tautan YouTube harus berupa URL lengkap, misalnya https://youtube.com/@namashowroom.',
            'settings.twitter_url.url' => 'Tautan X / Twitter harus berupa URL lengkap, misalnya https://x.com/namashowroom.',
        ];
    }
}
