@php
    $setting = fn (string $key) => old('settings.'.$key, $settings[$key] ?? '');

    $groups = [
        'identitas' => [
            'label' => 'Identitas Showroom',
            'description' => 'Nama utama yang tampil sebagai identitas situs.',
            'icon' => 'fas fa-store',
        ],
        'kontak' => [
            'label' => 'Kontak',
            'description' => 'Nomor dan email yang digunakan calon pembeli untuk menghubungi showroom.',
            'icon' => 'fas fa-address-book',
        ],
        'lokasi' => [
            'label' => 'Lokasi & Jam Operasional',
            'description' => 'Alamat, jam buka, dan peta lokasi showroom.',
            'icon' => 'fas fa-location-dot',
        ],
        'beranda' => [
            'label' => 'Konten Beranda',
            'description' => 'Teks utama yang tampil pada banner halaman depan.',
            'icon' => 'fas fa-image',
        ],
        'sosial' => [
            'label' => 'Media Sosial',
            'description' => 'Tautan akun media sosial yang ditampilkan pada footer situs.',
            'icon' => 'fas fa-share-nodes',
        ],
    ];

    $groupedFields = collect($fields)->groupBy(fn (array $field) => $field['group'] ?? 'identitas');

    $previewSocials = [
        'facebook_url' => ['icon' => 'fab fa-facebook-f', 'label' => 'Facebook'],
        'instagram_url' => ['icon' => 'fab fa-instagram', 'label' => 'Instagram'],
        'tiktok_url' => ['icon' => 'fab fa-tiktok', 'label' => 'TikTok'],
        'youtube_url' => ['icon' => 'fab fa-youtube', 'label' => 'YouTube'],
        'twitter_url' => ['icon' => 'fab fa-x-twitter', 'label' => 'X / Twitter'],
    ];
@endphp

@push('styles')
    <style>
        /* Tombol mengikuti palet tema, sama seperti halaman admin lain */
        .btn-primary {
            --bs-btn-color: #fff;
            --bs-btn-bg: var(--bs-primary);
            --bs-btn-border-color: var(--bs-primary);
            --bs-btn-hover-color: #fff;
            --bs-btn-hover-bg: #164934;
            --bs-btn-hover-border-color: #164934;
            --bs-btn-focus-shadow-rgb: 29, 91, 66;
            --bs-btn-active-color: #fff;
            --bs-btn-active-bg: #164934;
            --bs-btn-active-border-color: #164934;
            --bs-btn-disabled-bg: var(--bs-primary);
            --bs-btn-disabled-border-color: var(--bs-primary);
        }

        .btn-secondary {
            --bs-btn-color: var(--text-primary);
            --bs-btn-bg: var(--bg-tertiary);
            --bs-btn-border-color: var(--border-color);
            --bs-btn-hover-color: var(--text-primary);
            --bs-btn-hover-bg: var(--hover-bg);
            --bs-btn-hover-border-color: var(--border-color);
            --bs-btn-focus-shadow-rgb: 29, 91, 66;
            --bs-btn-active-color: var(--text-primary);
            --bs-btn-active-bg: var(--hover-bg);
            --bs-btn-active-border-color: var(--border-color);
            --bs-btn-disabled-color: var(--text-muted);
            --bs-btn-disabled-bg: var(--bg-tertiary);
            --bs-btn-disabled-border-color: var(--border-color);
        }

        .settings-page .input-group-text {
            background-color: var(--bg-tertiary) !important;
            border-color: var(--border-color);
            color: var(--text-secondary);
        }

        .settings-card-header {
            display: flex;
            align-items: flex-start;
            gap: .9rem;
            padding-bottom: 1.1rem;
            margin-bottom: 1.25rem;
            border-bottom: 1px solid var(--border-light);
        }

        .settings-card-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 auto;
            width: 2.75rem;
            height: 2.75rem;
            border-radius: 10px;
            background: rgba(29, 91, 66, .1);
            color: var(--bs-primary);
            font-size: 1rem;
        }

        .settings-card-title {
            margin: 0 0 .2rem;
            color: var(--text-primary);
            font-size: 1.05rem;
            font-weight: 600;
        }

        .settings-card-desc {
            margin: 0;
            color: var(--text-muted);
            font-size: .82rem;
            line-height: 1.5;
        }

        .settings-sidebar {
            position: sticky;
            top: calc(var(--topbar-height, 70px) + 1.25rem);
        }

        .settings-preview {
            position: relative;
            overflow: hidden;
            padding: 1.35rem;
            border: 1px solid var(--border-light);
            border-radius: 10px;
            background: linear-gradient(135deg, rgba(29, 91, 66, .1), transparent 65%);
        }

        .settings-preview-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            color: var(--bs-primary);
            font-size: .7rem;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .settings-preview-name {
            margin: .55rem 0 .3rem;
            color: var(--text-primary);
            font-size: 1.3rem;
            font-weight: 700;
            line-height: 1.25;
        }

        .settings-preview-address {
            margin: 0 0 1rem;
            color: var(--text-secondary);
            font-size: .84rem;
            line-height: 1.6;
        }

        .settings-preview-contact {
            display: flex;
            flex-direction: column;
            gap: .55rem;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .settings-preview-contact li {
            display: flex;
            align-items: flex-start;
            gap: .6rem;
            color: var(--text-secondary);
            font-size: .82rem;
            line-height: 1.45;
            overflow-wrap: anywhere;
        }

        .settings-preview-contact i {
            width: 1rem;
            margin-top: .15rem;
            color: var(--bs-primary);
            text-align: center;
        }

        .settings-preview-social {
            display: flex;
            flex-wrap: wrap;
            gap: .45rem;
            margin-top: 1.1rem;
            padding-top: 1rem;
            border-top: 1px dashed var(--border-color);
        }

        .settings-preview-social a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 2rem;
            height: 2rem;
            border-radius: 8px;
            border: 1px solid var(--border-color);
            color: var(--text-secondary);
            text-decoration: none;
            transition: all .2s ease;
        }

        .settings-preview-social a:hover {
            border-color: var(--bs-primary);
            background: var(--bs-primary);
            color: #fff;
            transform: translateY(-1px);
        }

        .settings-tips {
            display: flex;
            flex-direction: column;
            gap: .8rem;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .settings-tips li {
            display: flex;
            gap: .65rem;
            color: var(--text-secondary);
            font-size: .82rem;
            line-height: 1.55;
        }

        .settings-tips i {
            margin-top: .2rem;
            color: var(--bs-primary);
        }

        .settings-savebar {
            position: sticky;
            bottom: 0;
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-top: 1.5rem;
            padding: .9rem 1.25rem;
            border: 1px solid var(--border-color);
            border-radius: 10px;
            background: color-mix(in srgb, var(--bg-secondary) 92%, transparent);
            box-shadow: 0 -6px 24px rgba(23, 53, 43, .1);
            backdrop-filter: blur(8px);
        }

        .settings-savebar-status {
            display: flex;
            align-items: center;
            gap: .5rem;
            color: var(--text-muted);
            font-size: .82rem;
            font-weight: 500;
        }

        .settings-savebar-status i {
            color: var(--bs-success);
        }

        .settings-savebar.is-dirty .settings-savebar-status {
            color: var(--bs-warning);
        }

        .settings-savebar.is-dirty .settings-savebar-status i {
            color: var(--bs-warning);
        }

        @media (max-width: 1199.98px) {
            .settings-sidebar {
                position: static;
            }
        }

        @media (max-width: 575.98px) {
            .settings-savebar {
                align-items: stretch;
                flex-direction: column;
            }

            .settings-savebar .d-flex {
                justify-content: flex-end;
            }
        }
    </style>
@endpush

<x-layouts.app title="Pengaturan Situs - RodaTa Admin">
    <div class="settings-page">
        <x-layout.page-header title="Pengaturan Situs" subtitle="Kelola identitas, kontak, dan tautan media sosial showroom">
            <x-slot:actions>
                <x-ui.button variant="secondary" icon="fas fa-arrow-left" :href="route('dashboard')">
                    Dashboard
                </x-ui.button>
            </x-slot:actions>
        </x-layout.page-header>

        <form method="POST" action="{{ route('admin.settings.update') }}" id="settings-form">
            @csrf
            @method('PUT')

            <div class="row g-4">
                <div class="col-12 col-xl-8">
                    @foreach ($groups as $groupKey => $group)
                        @php $groupFields = $groupedFields->get($groupKey, collect()); @endphp

                        @if ($groupFields->isNotEmpty())
                            <x-layout.modern-card class="mb-4">
                                <div class="settings-card-header">
                                    <span class="settings-card-icon"><i class="{{ $group['icon'] }}"></i></span>
                                    <div>
                                        <h5 class="settings-card-title">{{ $group['label'] }}</h5>
                                        <p class="settings-card-desc">{{ $group['description'] }}</p>
                                    </div>
                                </div>

                                <div class="row g-3">
                                    @foreach ($groupFields as $key => $field)
                                        <div class="col-12 {{ $field['type'] === 'textarea' ? '' : 'col-md-6' }}">
                                            @if ($field['type'] === 'textarea')
                                                <x-form.textarea
                                                    :name="'settings['.$key.']'"
                                                    :label="$field['label']"
                                                    :value="$setting($key)"
                                                    :placeholder="$field['placeholder'] ?? ''"
                                                    :rows="$key === 'map_embed' ? 3 : 2"
                                                    :hint="$field['hint'] ?? null"
                                                    :error="$errors->first('settings.'.$key)"
                                                    :data-preview="$key"
                                                />
                                            @else
                                                <x-form.input
                                                    :type="$field['type'] ?? 'text'"
                                                    :name="'settings['.$key.']'"
                                                    :label="$field['label']"
                                                    :value="$setting($key)"
                                                    :placeholder="$field['placeholder'] ?? ''"
                                                    :hint="$field['hint'] ?? null"
                                                    :error="$errors->first('settings.'.$key)"
                                                    :icon="$field['icon'] ?? null"
                                                    :data-preview="$key"
                                                />
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </x-layout.modern-card>
                        @endif
                    @endforeach
                </div>

                <div class="col-12 col-xl-4">
                    <div class="settings-sidebar">
                        <x-layout.modern-card class="mb-4">
                            <div class="settings-preview">
                                <span class="settings-preview-eyebrow">
                                    <i class="fas fa-eye"></i> Pratinjau
                                </span>
                                <h5 class="settings-preview-name" data-preview-text="site_name">
                                    {{ $setting('site_name') ?: 'Nama Showroom' }}
                                </h5>
                                <p class="settings-preview-address" data-preview-text="address">
                                    {{ $setting('address') ?: 'Alamat showroom akan tampil di sini.' }}
                                </p>

                                <ul class="settings-preview-contact">
                                    <li>
                                        <i class="fas fa-phone"></i>
                                        <span data-preview-text="phone_number">{{ $setting('phone_number') ?: 'Nomor telepon belum diisi' }}</span>
                                    </li>
                                    <li>
                                        <i class="fas fa-envelope"></i>
                                        <span data-preview-text="email">{{ $setting('email') ?: 'Email belum diisi' }}</span>
                                    </li>
                                    <li>
                                        <i class="fas fa-clock"></i>
                                        <span data-preview-text="opening_hours">{{ $setting('opening_hours') ?: 'Jam buka belum diisi' }}</span>
                                    </li>
                                </ul>

                                <div class="settings-preview-social">
                                    @foreach ($previewSocials as $socialKey => $social)
                                        <a
                                            href="{{ $setting($socialKey) ?: '#' }}"
                                            data-social-key="{{ $socialKey }}"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            aria-label="{{ $social['label'] }}"
                                            style="{{ $setting($socialKey) ? '' : 'display: none;' }}"
                                        >
                                            <i class="{{ $social['icon'] }}"></i>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </x-layout.modern-card>

                        <x-layout.modern-card>
                            <h5 class="preview-title">Tips Pengisian</h5>
                            <ul class="settings-tips">
                                <li>
                                    <i class="fas fa-circle-info"></i>
                                    <span>Nomor WhatsApp sebaiknya diawali kode negara, misalnya <code>62812...</code>.</span>
                                </li>
                                <li>
                                    <i class="fas fa-link"></i>
                                    <span>Media sosial harus berupa URL lengkap yang diawali <code>https://</code>.</span>
                                </li>
                                <li>
                                    <i class="fas fa-map-location-dot"></i>
                                    <span>Peta memerlukan URL embed dari Google Maps, bukan tautan biasa.</span>
                                </li>
                                <li>
                                    <i class="fas fa-eraser"></i>
                                    <span>Kosongkan kolom jika informasi tersebut belum tersedia.</span>
                                </li>
                            </ul>
                        </x-layout.modern-card>
                    </div>
                </div>
            </div>

            <div class="settings-savebar {{ $errors->any() ? 'is-dirty' : '' }}" id="settings-savebar">
                <div class="settings-savebar-status">
                    <i class="fas {{ $errors->any() ? 'fa-circle-exclamation' : 'fa-circle-check' }}" id="settings-save-icon"></i>
                    <span id="settings-save-status">{{ $errors->any() ? 'Periksa kembali kolom yang ditandai' : 'Semua perubahan tersimpan' }}</span>
                </div>
                <div class="d-flex gap-2">
                    <x-ui.button variant="secondary" :href="route('dashboard')">Batal</x-ui.button>
                    <x-ui.button type="submit" variant="primary" icon="fas fa-save" id="settings-submit">Simpan Pengaturan</x-ui.button>
                </div>
            </div>
        </form>
    </div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('settings-form');
            if (!form) return;

            const savebar = document.getElementById('settings-savebar');
            const status = document.getElementById('settings-save-status');
            const statusIcon = document.getElementById('settings-save-icon');
            const submit = document.getElementById('settings-submit');
            let dirty = false;

            const placeholders = {
                site_name: 'Nama Showroom',
                address: 'Alamat showroom akan tampil di sini.',
                phone_number: 'Nomor telepon belum diisi',
                email: 'Email belum diisi',
                opening_hours: 'Jam buka belum diisi',
            };

            const previewTargets = document.querySelectorAll('[data-preview-text]');

            function fieldValue(key) {
                const input = form.querySelector('[name="settings[' + key + ']"]');
                return input ? input.value.trim() : '';
            }

            function syncPreview() {
                previewTargets.forEach(function (target) {
                    const key = target.dataset.previewText;
                    const value = fieldValue(key);
                    target.textContent = value || placeholders[key] || '—';
                });

                document.querySelectorAll('[data-social-key]').forEach(function (link) {
                    const value = fieldValue(link.dataset.socialKey);
                    link.style.display = value ? '' : 'none';

                    if (value) {
                        link.href = value;
                    }
                });
            }

            function markDirty() {
                if (dirty) return;
                dirty = true;
                savebar.classList.add('is-dirty');
                status.textContent = 'Ada perubahan yang belum disimpan';
                statusIcon.className = 'fas fa-circle-exclamation';
            }

            form.addEventListener('input', function () {
                markDirty();
                syncPreview();
            });

            form.addEventListener('submit', function () {
                dirty = false;
                submit.disabled = true;
                submit.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...';
            });

            window.addEventListener('beforeunload', function (event) {
                if (!dirty) return;
                event.preventDefault();
                event.returnValue = '';
            });

            syncPreview();
        });
    </script>
@endpush
</x-layouts.app>
