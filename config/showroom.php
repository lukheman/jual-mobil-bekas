<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Site Settings
    |--------------------------------------------------------------------------
    |
    | Fields managed from the admin settings page. The key is stored in the
    | settings table and can be read with Setting::query()->pluck(...).
    |
    */
    'settings' => [
        'site_name' => [
            'label' => 'Nama Showroom',
            'type' => 'text',
            'placeholder' => 'MobilTa',
            'hint' => 'Nama yang tampil pada judul halaman dan identitas situs.',
        ],
        'whatsapp_number' => [
            'label' => 'Nomor WhatsApp',
            'type' => 'text',
            'placeholder' => '6281234567890',
            'hint' => 'Gunakan format internasional tanpa tanda +, misalnya 6281234567890.',
        ],
        'phone_number' => [
            'label' => 'Nomor Telepon',
            'type' => 'text',
            'placeholder' => '081234567890',
            'hint' => 'Nomor yang bisa dihubungi langsung dari ponsel.',
        ],
        'email' => [
            'label' => 'Email Showroom',
            'type' => 'email',
            'placeholder' => 'halo@showroom.com',
        ],
        'address' => [
            'label' => 'Alamat Showroom',
            'type' => 'textarea',
            'placeholder' => 'Jl. Ahmad Yani No. 88, Samarinda',
        ],
        'opening_hours' => [
            'label' => 'Jam Buka',
            'type' => 'text',
            'placeholder' => 'Senin - Sabtu, 08.00 - 17.00 WITA',
        ],
        'map_embed' => [
            'label' => 'URL Google Maps Embed',
            'type' => 'textarea',
            'placeholder' => 'https://www.google.com/maps/embed?pb=...',
            'hint' => 'Tempel URL embed peta dari Google Maps.',
        ],
        'hero_title' => [
            'label' => 'Judul Banner Beranda',
            'type' => 'text',
            'placeholder' => 'Temukan mobil yang tepat untukmu.',
        ],
        'hero_subtitle' => [
            'label' => 'Subjudul Banner Beranda',
            'type' => 'textarea',
            'placeholder' => 'Mobil berkualitas, harga transparan, dan proses yang sederhana.',
        ],
    ],
];
