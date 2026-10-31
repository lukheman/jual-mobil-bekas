<x-layouts.app title="Profile - RodaTa Admin">
    @php
        $user = Auth::user();
        $showPasswordSection = $errors->has('current_password') || $errors->has('password');
        $roleLabel = $user->role?->label() ?? 'Administrator';

        $months = [
            1 => 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
            'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des',
        ];

        $formatDate = static function (?\Illuminate\Support\Carbon $date) use ($months): string {
            if (! $date) {
                return '—';
            }

            return $date->format('d').' '.$months[(int) $date->format('n')].' '.$date->format('Y');
        };

        $updatedLabel = static function (?\Illuminate\Support\Carbon $date): string {
            if (! $date) {
                return '—';
            }

            $minutes = max(0, (int) $date->diffInMinutes(now()));

            if ($minutes < 1) {
                return 'Baru saja';
            }

            if ($minutes < 60) {
                return $minutes.' menit lalu';
            }

            $hours = intdiv($minutes, 60);

            if ($hours < 24) {
                return $hours.' jam lalu';
            }

            $days = intdiv($hours, 24);

            if ($days < 7) {
                return $days.' hari lalu';
            }

            return $date->format('d/m/Y');
        };
    @endphp

    @push('styles')
        <style>
            [x-cloak] {
                display: none !important;
            }

            /* Tombol mengikuti palet tema */
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

            /* Tata letak */
            .profile-grid {
                display: grid;
                grid-template-columns: 330px minmax(0, 1fr);
                gap: 1.25rem;
                align-items: start;
            }

            .profile-main {
                display: flex;
                flex-direction: column;
                gap: 1.25rem;
            }

            .profile-card {
                scroll-margin-top: calc(var(--topbar-height) + 1.25rem);
            }

            .profile-card:hover,
            .profile-summary:hover {
                transform: none;
            }

            /* Ringkasan akun */
            .profile-summary {
                position: sticky;
                top: calc(var(--topbar-height) + 1.25rem);
                padding: 1.6rem 1.35rem 1.25rem;
            }

            .profile-identity {
                display: flex;
                flex-direction: column;
                align-items: center;
                text-align: center;
            }

            .profile-avatar-large {
                display: grid;
                place-items: center;
                width: 124px;
                height: 124px;
                border-radius: 50%;
                overflow: hidden;
                background: rgba(29, 91, 66, 0.1);
                color: var(--bs-primary);
                font-size: 2.5rem;
                font-weight: 700;
                letter-spacing: 0.02em;
                text-decoration: none;
                border: 4px solid var(--bg-secondary);
                box-shadow: 0 0 0 1px var(--border-color), 0 12px 26px rgba(23, 53, 43, 0.14);
                transition: box-shadow 0.2s ease, transform 0.2s ease;
            }

            .profile-avatar-large:hover {
                color: var(--bs-primary);
                transform: translateY(-2px);
                box-shadow: 0 0 0 1px var(--bs-primary), 0 14px 28px rgba(23, 53, 43, 0.18);
            }

            .profile-avatar-large img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            .profile-identity h4 {
                margin: 1rem 0 0.2rem;
                color: var(--text-primary);
                font-size: 1.15rem;
                font-weight: 700;
                overflow-wrap: anywhere;
            }

            .profile-identity p {
                margin: 0;
                color: var(--text-muted);
                font-size: 0.82rem;
                overflow-wrap: anywhere;
            }

            .profile-badges {
                display: flex;
                flex-wrap: wrap;
                justify-content: center;
                gap: 0.4rem;
                margin-top: 0.85rem;
            }

            .profile-shortcuts {
                display: flex;
                flex-direction: column;
                gap: 0.2rem;
                margin: 1.35rem 0 0;
                padding-top: 1.1rem;
                border-top: 1px solid var(--border-light);
            }

            .profile-shortcuts a {
                display: flex;
                align-items: center;
                gap: 0.65rem;
                padding: 0.55rem 0.6rem;
                border-radius: 8px;
                color: var(--text-secondary);
                font-size: 0.82rem;
                font-weight: 600;
                text-decoration: none;
                transition: background 0.2s ease, color 0.2s ease;
            }

            .profile-shortcuts a > i:first-child {
                width: 16px;
                color: var(--bs-primary);
                font-size: 0.78rem;
                text-align: center;
            }

            .profile-shortcuts a > i:last-child {
                margin-left: auto;
                color: var(--text-muted);
                font-size: 0.62rem;
            }

            .profile-shortcuts a:hover {
                background: var(--hover-bg);
                color: var(--bs-primary);
            }

            .profile-meta {
                margin: 1.1rem 0 0;
                padding-top: 0.85rem;
                border-top: 1px solid var(--border-light);
            }

            .profile-meta div {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 0.75rem;
                padding: 0.42rem 0;
            }

            .profile-meta dt {
                color: var(--text-muted);
                font-size: 0.72rem;
                font-weight: 500;
            }

            .profile-meta dd {
                margin: 0;
                color: var(--text-primary);
                font-size: 0.76rem;
                font-weight: 600;
                text-align: right;
            }

            /* Kepala bagian */
            .profile-section-head {
                display: flex;
                align-items: flex-start;
                justify-content: space-between;
                gap: 1rem;
                margin-bottom: 1.25rem;
            }

            .profile-section-title {
                display: flex;
                align-items: flex-start;
                gap: 0.75rem;
                min-width: 0;
            }

            .profile-section-icon {
                display: grid;
                place-items: center;
                width: 40px;
                height: 40px;
                flex-shrink: 0;
                border-radius: 10px;
                background: rgba(29, 91, 66, 0.1);
                color: var(--bs-primary);
                font-size: 0.95rem;
            }

            .profile-section-icon.is-warning {
                background: rgba(189, 138, 56, 0.14);
                color: var(--bs-warning);
            }

            .profile-section-icon.is-info {
                background: rgba(79, 130, 144, 0.14);
                color: var(--bs-info);
            }

            .profile-section-title h5 {
                margin: 0 0 0.15rem;
                color: var(--text-primary);
                font-size: 1rem;
                font-weight: 700;
            }

            .profile-section-title p {
                margin: 0;
                color: var(--text-muted);
                font-size: 0.76rem;
            }

            .profile-section-state {
                display: inline-flex;
                align-items: center;
                gap: 0.35rem;
                padding: 0.28rem 0.58rem;
                border-radius: 999px;
                font-size: 0.68rem;
                font-weight: 700;
                white-space: nowrap;
            }

            .profile-section-state.is-on {
                background: rgba(77, 138, 99, 0.14);
                color: var(--bs-success);
            }

            .profile-section-state.is-off {
                background: var(--bg-tertiary);
                color: var(--text-muted);
            }

            /* Unggah avatar */
            .profile-file-input {
                position: absolute;
                width: 1px;
                height: 1px;
                padding: 0;
                margin: -1px;
                overflow: hidden;
                clip: rect(0, 0, 0, 0);
                white-space: nowrap;
                border: 0;
            }

            .profile-dropzone {
                display: flex;
                align-items: center;
                gap: 1rem;
                width: 100%;
                padding: 1rem;
                border: 1.5px dashed var(--border-color);
                border-radius: 10px;
                background: var(--bg-secondary);
                cursor: pointer;
                transition: border-color 0.2s ease, background 0.2s ease;
            }

            .profile-dropzone:hover,
            .profile-dropzone.is-dragging {
                border-color: var(--bs-primary);
                background: var(--hover-bg);
            }

            .profile-file-input:focus-visible + .profile-dropzone {
                outline: 2px solid var(--bs-primary);
                outline-offset: 2px;
            }

            .profile-dropzone-preview {
                display: grid;
                place-items: center;
                width: 82px;
                height: 82px;
                flex-shrink: 0;
                border-radius: 50%;
                overflow: hidden;
                background: rgba(29, 91, 66, 0.1);
                color: var(--bs-primary);
                font-size: 1.6rem;
                font-weight: 700;
                border: 3px solid var(--bg-secondary);
                box-shadow: 0 0 0 1px var(--border-color);
            }

            .profile-dropzone-preview img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            .profile-dropzone-copy {
                display: flex;
                flex-direction: column;
                gap: 0.15rem;
                min-width: 0;
            }

            .profile-dropzone-copy strong {
                color: var(--text-primary);
                font-size: 0.84rem;
                font-weight: 700;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .profile-dropzone-hint {
                color: var(--text-muted);
                font-size: 0.72rem;
                line-height: 1.45;
            }

            .profile-dropzone-button {
                display: inline-flex;
                align-items: center;
                gap: 0.35rem;
                margin-top: 0.25rem;
                color: var(--bs-primary);
                font-size: 0.74rem;
                font-weight: 700;
            }

            .profile-avatar-actions {
                display: flex;
                flex-wrap: wrap;
                gap: 0.5rem;
                margin-top: 1rem;
            }

            .profile-avatar-delete {
                display: flex;
                align-items: center;
                flex-wrap: wrap;
                gap: 0.65rem;
                margin-top: 1rem;
                padding-top: 0.9rem;
                border-top: 1px solid var(--border-light);
            }

            .profile-danger-link {
                display: inline-flex;
                align-items: center;
                gap: 0.4rem;
                padding: 0;
                border: none;
                background: transparent;
                color: var(--bs-danger);
                font-size: 0.76rem;
                font-weight: 600;
            }

            .profile-danger-link:hover {
                text-decoration: underline;
            }

            .profile-delete-confirm {
                display: flex;
                align-items: center;
                flex-wrap: wrap;
                gap: 0.45rem;
                width: 100%;
                padding: 0.55rem 0.7rem;
                border: 1px solid rgba(185, 88, 76, 0.3);
                border-radius: 8px;
                background: rgba(185, 88, 76, 0.06);
            }

            .profile-delete-confirm > span {
                margin-right: auto;
                color: var(--bs-danger);
                font-size: 0.76rem;
                font-weight: 600;
            }

            .profile-delete-confirm form {
                display: inline-flex;
                margin: 0;
            }

            /* Form */
            .profile-form-footer {
                display: flex;
                align-items: center;
                justify-content: space-between;
                flex-wrap: wrap;
                gap: 0.75rem;
                margin-top: 1.5rem;
                padding-top: 1.1rem;
                border-top: 1px solid var(--border-light);
            }

            .profile-dirty {
                display: inline-flex;
                align-items: center;
                gap: 0.4rem;
                color: var(--bs-warning);
                font-size: 0.74rem;
                font-weight: 600;
            }

            .profile-dirty-dot {
                width: 7px;
                height: 7px;
                border-radius: 50%;
                background: var(--bs-warning);
                box-shadow: 0 0 0 3px rgba(189, 138, 56, 0.16);
            }

            .profile-inline-note {
                display: flex;
                align-items: flex-start;
                gap: 0.5rem;
                margin-top: 0.4rem;
                color: var(--text-muted);
                font-size: 0.74rem;
                line-height: 1.5;
            }

            .profile-inline-note i {
                margin-top: 0.15rem;
                color: var(--bs-info);
            }

            /* Keamanan */
            .profile-security-hint {
                display: flex;
                align-items: flex-start;
                gap: 0.6rem;
                margin: 0;
                padding: 0.85rem 0.95rem;
                border: 1px solid var(--border-light);
                border-radius: 9px;
                background: var(--bg-tertiary);
                color: var(--text-muted);
                font-size: 0.76rem;
                line-height: 1.55;
            }

            .profile-security-hint i {
                margin-top: 0.1rem;
                color: var(--bs-primary);
            }

            @media (max-width: 991.98px) {
                .profile-grid {
                    grid-template-columns: 1fr;
                }

                .profile-summary {
                    position: static;
                }

                .profile-identity {
                    flex-direction: row;
                    text-align: left;
                    gap: 1rem;
                }

                .profile-avatar-large {
                    width: 84px;
                    height: 84px;
                    font-size: 1.7rem;
                    flex-shrink: 0;
                }

                .profile-identity h4 {
                    margin-top: 0;
                }

                .profile-badges {
                    justify-content: flex-start;
                    margin-top: 0.5rem;
                }

                .profile-identity-copy {
                    min-width: 0;
                }

                .profile-meta div {
                    max-width: 420px;
                }
            }

            @media (max-width: 575.98px) {
                .profile-dropzone {
                    flex-direction: column;
                    align-items: flex-start;
                    text-align: left;
                }

                .profile-section-head {
                    flex-wrap: wrap;
                }

                .profile-form-footer {
                    flex-direction: column;
                    align-items: stretch;
                }

                .profile-form-footer .btn {
                    width: 100%;
                }
            }
        </style>
    @endpush

    <x-layout.page-header title="Profile" subtitle="Kelola informasi akun, foto, dan keamanan Anda">
        <x-slot:actions>
            <div class="d-flex align-items-center flex-wrap gap-2">
                <x-ui.badge variant="primary" icon="fas fa-user-shield">{{ $roleLabel }}</x-ui.badge>
                <x-ui.badge variant="{{ $user->email_verified_at ? 'success' : 'warning' }}" icon="{{ $user->email_verified_at ? 'fas fa-user-check' : 'fas fa-clock' }}">
                    {{ $user->email_verified_at ? 'Terverifikasi' : 'Belum Terverifikasi' }}
                </x-ui.badge>
            </div>
        </x-slot:actions>
    </x-layout.page-header>

    <div class="profile-grid">
        {{-- Ringkasan akun --}}
        <x-layout.modern-card class="profile-summary">
            <div class="profile-identity">
                <a href="#foto-profil" class="profile-avatar-large" title="Ubah foto profil">
                    @if ($user->hasAvatar())
                        <img src="{{ $user->avatarUrl() }}" alt="Foto profil {{ $user->name }}">
                    @else
                        {{ $user->initials() }}
                    @endif
                </a>

                <div class="profile-identity-copy">
                    <h4>{{ $user->name }}</h4>
                    <p>{{ $user->email }}</p>

                    <div class="profile-badges">
                        <x-ui.badge variant="primary" icon="fas fa-user-shield">{{ $roleLabel }}</x-ui.badge>
                        <x-ui.badge variant="{{ $user->email_verified_at ? 'success' : 'warning' }}" icon="{{ $user->email_verified_at ? 'fas fa-user-check' : 'fas fa-clock' }}">
                            {{ $user->email_verified_at ? 'Terverifikasi' : 'Belum Terverifikasi' }}
                        </x-ui.badge>
                    </div>
                </div>
            </div>

            <nav class="profile-shortcuts" aria-label="Navigasi bagian profil">
                <a href="#foto-profil">
                    <i class="fas fa-camera"></i>
                    Foto profil
                    <i class="fas fa-chevron-right"></i>
                </a>
                <a href="#informasi-profil">
                    <i class="fas fa-user-pen"></i>
                    Informasi profil
                    <i class="fas fa-chevron-right"></i>
                </a>
                <a href="#keamanan">
                    <i class="fas fa-shield-halved"></i>
                    Keamanan akun
                    <i class="fas fa-chevron-right"></i>
                </a>
            </nav>

            <dl class="profile-meta">
                <div>
                    <dt>ID Pengguna</dt>
                    <dd>#{{ str_pad((string) $user->id, 4, '0', STR_PAD_LEFT) }}</dd>
                </div>
                <div>
                    <dt>Bergabung</dt>
                    <dd>{{ $formatDate($user->created_at) }}</dd>
                </div>
                <div>
                    <dt>Terakhir diperbarui</dt>
                    <dd>{{ $updatedLabel($user->updated_at) }}</dd>
                </div>
            </dl>
        </x-layout.modern-card>

        <div class="profile-main">
            {{-- Foto profil --}}
            <x-layout.modern-card id="foto-profil" class="profile-card">
                <div class="profile-section-head">
                    <div class="profile-section-title">
                        <span class="profile-section-icon">
                            <i class="fas fa-camera"></i>
                        </span>
                        <div>
                            <h5>Foto Profil</h5>
                            <p>Tampil di topbar dan daftar pengguna admin.</p>
                        </div>
                    </div>
                    <span class="profile-section-state {{ $user->hasAvatar() ? 'is-on' : 'is-off' }}">
                        <i class="fas {{ $user->hasAvatar() ? 'fa-circle-check' : 'fa-circle-minus' }}"></i>
                        {{ $user->hasAvatar() ? 'Terpasang' : 'Belum ada' }}
                    </span>
                </div>

                <div x-data="avatarUploader(@js($user->avatarUrl()), @js($user->initials()))">
                    <form method="POST" action="{{ route('admin.profile.avatar') }}" enctype="multipart/form-data">
                        @csrf

                        <input type="file"
                               name="avatar"
                               id="avatar-upload"
                               x-ref="input"
                               class="profile-file-input"
                               accept="image/png,image/jpeg,image/webp,image/gif"
                               x-on:change="pick($event)">

                        <label for="avatar-upload"
                               class="profile-dropzone"
                               x-bind:class="{ 'is-dragging': dragging }"
                               x-on:dragover.prevent="dragging = true"
                               x-on:dragleave.prevent="dragging = false"
                               x-on:drop.prevent="drop($event)">
                            <span class="profile-dropzone-preview">
                                <template x-if="preview">
                                    <img :src="preview" alt="Pratinjau foto profil" x-on:error="preview = null">
                                </template>
                                <span x-show="!preview" x-text="initials"></span>
                            </span>

                            <span class="profile-dropzone-copy">
                                <strong x-text="filename ?? 'Pilih atau tarik foto ke sini'">Pilih atau tarik foto ke sini</strong>
                                <span class="profile-dropzone-hint"
                                      x-text="filename ? 'Siap disimpan. Tekan Simpan Foto.' : 'JPG, PNG, WEBP, atau GIF. Maksimal 2 MB.'">
                                    JPG, PNG, WEBP, atau GIF. Maksimal 2 MB.
                                </span>
                                <span class="profile-dropzone-button">
                                    <i class="fas fa-upload"></i>
                                    Pilih Foto
                                </span>
                            </span>
                        </label>

                        @error('avatar')
                            <div class="text-danger mt-2 small">{{ $message }}</div>
                        @enderror

                        <div class="profile-avatar-actions">
                            <x-ui.button type="submit" variant="primary" icon="fas fa-save" x-bind:disabled="! dirty">
                                Simpan Foto
                            </x-ui.button>
                            <x-ui.button type="button" variant="secondary" icon="fas fa-rotate-left"
                                         x-show="dirty" x-cloak x-on:click="reset()">
                                Batal
                            </x-ui.button>
                        </div>
                    </form>

                    @if ($user->hasAvatar())
                        <div class="profile-avatar-delete" x-data="{ confirming: false }">
                            <button type="button" class="profile-danger-link" x-show="! confirming" x-on:click="confirming = true">
                                <i class="fas fa-trash-alt"></i>
                                Hapus foto profil
                            </button>

                            <div class="profile-delete-confirm" x-show="confirming" x-cloak>
                                <span>Hapus foto profil saat ini?</span>
                                <form method="POST" action="{{ route('admin.profile.avatar.destroy') }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Ya, hapus</button>
                                </form>
                                <button type="button" class="btn btn-sm btn-secondary" x-on:click="confirming = false">
                                    Batal
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            </x-layout.modern-card>

            {{-- Informasi profil --}}
            <x-layout.modern-card id="informasi-profil" class="profile-card">
                <div class="profile-section-head">
                    <div class="profile-section-title">
                        <span class="profile-section-icon is-info">
                            <i class="fas fa-user-pen"></i>
                        </span>
                        <div>
                            <h5>Informasi Profile</h5>
                            <p>Nama dan email yang digunakan untuk masuk ke dashboard.</p>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.profile.update') }}" x-data="{ dirty: false }">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-6">
                            <x-form.input
                                id="name"
                                name="name"
                                label="Nama Lengkap"
                                icon="fas fa-user"
                                :value="old('name', $user->name)"
                                placeholder="Masukkan nama lengkap"
                                required
                                x-on:input="dirty = true"
                                :error="$errors->first('name')"
                            />
                        </div>

                        <div class="col-md-6">
                            <x-form.input
                                type="email"
                                id="email"
                                name="email"
                                label="Email"
                                icon="fas fa-envelope"
                                :value="old('email', $user->email)"
                                placeholder="Masukkan email"
                                :hint="$user->email_verified_at ? 'Email terverifikasi dan aktif.' : 'Email belum terverifikasi.'"
                                required
                                x-on:input="dirty = true"
                                :error="$errors->first('email')"
                            />
                        </div>
                    </div>

                    <div class="profile-inline-note">
                        <i class="fas fa-circle-info"></i>
                        <span>Perubahan email akan mengubah alamat yang dipakai untuk login berikutnya.</span>
                    </div>

                    <div class="profile-form-footer">
                        <span class="profile-dirty" x-show="dirty" x-cloak>
                            <span class="profile-dirty-dot"></span>
                            Perubahan belum disimpan
                        </span>
                        <span class="profile-dirty" x-show="! dirty" x-cloak>
                            <i class="fas fa-circle-check" style="color: var(--bs-success);"></i>
                            Data tersimpan
                        </span>

                        <x-ui.button type="submit" variant="primary" icon="fas fa-save">
                            Simpan Perubahan
                        </x-ui.button>
                    </div>
                </form>
            </x-layout.modern-card>

            {{-- Keamanan akun --}}
            <x-layout.modern-card id="keamanan" class="profile-card">
                <div class="profile-section-head">
                    <div class="profile-section-title">
                        <span class="profile-section-icon is-warning">
                            <i class="fas fa-lock"></i>
                        </span>
                        <div>
                            <h5>Keamanan Akun</h5>
                            <p>Perbarui password secara berkala untuk menjaga akun tetap aman.</p>
                        </div>
                    </div>

                    <button type="button"
                            id="password-toggle"
                            class="btn btn-modern btn-sm {{ $showPasswordSection ? 'btn-danger' : 'btn-secondary' }}"
                            onclick="togglePasswordSection()"
                            aria-expanded="{{ $showPasswordSection ? 'true' : 'false' }}">
                        <i id="password-toggle-icon" class="fas {{ $showPasswordSection ? 'fa-times' : 'fa-key' }} me-2"></i>
                        <span class="password-toggle-label">{{ $showPasswordSection ? 'Batal' : 'Ubah Password' }}</span>
                    </button>
                </div>

                <div id="password-section" class="{{ $showPasswordSection ? '' : 'd-none' }}">
                    <form method="POST" action="{{ route('admin.profile.password') }}">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-12">
                                <x-form.password
                                    id="current_password"
                                    name="current_password"
                                    label="Password Saat Ini"
                                    placeholder="Masukkan password saat ini"
                                    autocomplete="current-password"
                                    required
                                    :error="$errors->first('current_password')"
                                />
                            </div>

                            <div class="col-md-6">
                                <x-form.password
                                    id="password"
                                    name="password"
                                    label="Password Baru"
                                    placeholder="Minimal 8 karakter"
                                    autocomplete="new-password"
                                    required
                                    strength
                                    hint="Gunakan minimal 8 karakter dengan huruf dan angka."
                                    :error="$errors->first('password')"
                                />
                            </div>

                            <div class="col-md-6">
                                <x-form.password
                                    id="password_confirmation"
                                    name="password_confirmation"
                                    label="Konfirmasi Password Baru"
                                    placeholder="Ulangi password baru"
                                    autocomplete="new-password"
                                    required
                                />
                            </div>
                        </div>

                        <x-ui.alert variant="info" class="mt-3">
                            <i class="fas fa-shield-halved me-2"></i>
                            Password harus minimal 8 karakter. Kombinasi huruf besar, angka, dan simbol membuatnya lebih kuat.
                        </x-ui.alert>

                        <div class="profile-form-footer">
                            <span class="profile-inline-note m-0">
                                <i class="fas fa-lock"></i>
                                <span>Anda akan tetap masuk setelah password diperbarui.</span>
                            </span>

                            <x-ui.button type="submit" variant="warning" icon="fas fa-key">
                                Perbarui Password
                            </x-ui.button>
                        </div>
                    </form>
                </div>

                <p id="password-hint" class="profile-security-hint {{ $showPasswordSection ? 'd-none' : '' }}">
                    <i class="fas fa-shield-alt"></i>
                    <span>Klik tombol "Ubah Password" untuk memperbarui password Anda.</span>
                </p>
            </x-layout.modern-card>
        </div>
    </div>

    @push('scripts')
        <script>
            function avatarUploader(initialPreview, initials) {
                return {
                    preview: initialPreview || null,
                    initialPreview: initialPreview || null,
                    initials: initials,
                    filename: null,
                    dragging: false,
                    dirty: false,

                    pick(event) {
                        const file = event.target.files[0];

                        if (file) {
                            this.setFile(file);
                        }
                    },

                    drop(event) {
                        const file = event.dataTransfer.files[0];

                        if (!file) {
                            return;
                        }

                        if (! file.type.startsWith('image/')) {
                            return;
                        }

                        const transfer = new DataTransfer();
                        transfer.items.add(file);
                        this.$refs.input.files = transfer.files;

                        this.setFile(file);
                        this.dragging = false;
                    },

                    setFile(file) {
                        if (this.preview && this.preview !== this.initialPreview) {
                            URL.revokeObjectURL(this.preview);
                        }

                        this.preview = URL.createObjectURL(file);
                        this.filename = file.name;
                        this.dirty = true;
                    },

                    reset() {
                        if (this.preview && this.preview !== this.initialPreview) {
                            URL.revokeObjectURL(this.preview);
                        }

                        this.preview = this.initialPreview;
                        this.filename = null;
                        this.dirty = false;

                        if (this.$refs.input) {
                            this.$refs.input.value = '';
                        }
                    }
                };
            }

            function togglePasswordSection() {
                const section = document.getElementById('password-section');
                const hint = document.getElementById('password-hint');
                const button = document.getElementById('password-toggle');
                const icon = document.getElementById('password-toggle-icon');
                const label = button?.querySelector('.password-toggle-label');

                if (!section || !button) {
                    return;
                }

                const isHidden = section.classList.contains('d-none');

                section.classList.toggle('d-none', !isHidden);
                hint?.classList.toggle('d-none', isHidden);
                button.setAttribute('aria-expanded', isHidden ? 'true' : 'false');
                button.classList.toggle('btn-secondary', !isHidden);
                button.classList.toggle('btn-danger', isHidden);

                if (icon) {
                    icon.className = isHidden ? 'fas fa-times me-2' : 'fas fa-key me-2';
                }

                if (label) {
                    label.textContent = isHidden ? 'Batal' : 'Ubah Password';
                }

                if (isHidden) {
                    document.getElementById('current_password')?.focus();
                }
            }
        </script>
    @endpush
</x-layouts.app>
