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
            'group' => 'identitas',
        ],
        'whatsapp_number' => [
            'label' => 'Nomor WhatsApp',
            'type' => 'text',
            'placeholder' => '6281234567890',
            'hint' => 'Gunakan format internasional tanpa tanda +, misalnya 6281234567890.',
            'group' => 'kontak',
        ],
        'phone_number' => [
            'label' => 'Nomor Telepon',
            'type' => 'text',
            'placeholder' => '081234567890',
            'hint' => 'Nomor yang bisa dihubungi langsung dari ponsel.',
            'group' => 'kontak',
        ],
        'email' => [
            'label' => 'Email Showroom',
            'type' => 'email',
            'placeholder' => 'halo@showroom.com',
            'group' => 'kontak',
        ],
        'address' => [
            'label' => 'Alamat Showroom',
            'type' => 'textarea',
            'placeholder' => 'Jl. Ahmad Yani No. 88, Samarinda',
            'group' => 'lokasi',
        ],
        'opening_hours' => [
            'label' => 'Jam Buka',
            'type' => 'text',
            'placeholder' => 'Senin - Sabtu, 08.00 - 17.00 WITA',
            'group' => 'lokasi',
        ],
        'map_embed' => [
            'label' => 'URL Google Maps Embed',
            'type' => 'textarea',
            'placeholder' => 'https://www.google.com/maps/embed?pb=...',
            'hint' => 'Tempel URL embed peta dari Google Maps.',
            'group' => 'lokasi',
        ],
        'hero_title' => [
            'label' => 'Judul Banner Beranda',
            'type' => 'text',
            'placeholder' => 'Temukan mobil yang tepat untukmu.',
            'group' => 'beranda',
        ],
        'hero_subtitle' => [
            'label' => 'Subjudul Banner Beranda',
            'type' => 'textarea',
            'placeholder' => 'Mobil berkualitas, harga transparan, dan proses yang sederhana.',
            'group' => 'beranda',
        ],
        'facebook_url' => [
            'label' => 'Facebook',
            'type' => 'url',
            'placeholder' => 'https://facebook.com/namashowroom',
            'icon' => 'fab fa-facebook-f',
            'group' => 'sosial',
        ],
        'instagram_url' => [
            'label' => 'Instagram',
            'type' => 'url',
            'placeholder' => 'https://instagram.com/namashowroom',
            'icon' => 'fab fa-instagram',
            'group' => 'sosial',
        ],
        'tiktok_url' => [
            'label' => 'TikTok',
            'type' => 'url',
            'placeholder' => 'https://tiktok.com/@namashowroom',
            'icon' => 'fab fa-tiktok',
            'group' => 'sosial',
        ],
        'youtube_url' => [
            'label' => 'YouTube',
            'type' => 'url',
            'placeholder' => 'https://youtube.com/@namashowroom',
            'icon' => 'fab fa-youtube',
            'group' => 'sosial',
        ],
        'twitter_url' => [
            'label' => 'X / Twitter',
            'type' => 'url',
            'placeholder' => 'https://x.com/namashowroom',
            'icon' => 'fab fa-x-twitter',
            'group' => 'sosial',
        ],
    ],
];
