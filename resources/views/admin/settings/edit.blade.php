@php
    $setting = fn (string $key) => old('settings.'.$key, $settings[$key] ?? '');
@endphp

<x-layouts.app title="Pengaturan Situs - RodaTa Admin">
    <x-layout.page-header title="Pengaturan Situs" subtitle="Ubah informasi kontak dan konten utama tanpa mengubah kode">
        <x-slot:actions>
            <x-ui.button variant="secondary" icon="fas fa-arrow-left" :href="route('dashboard')">
                Dashboard
            </x-ui.button>
        </x-slot:actions>
    </x-layout.page-header>

    <form method="POST" action="{{ route('admin.settings.update') }}">
        @csrf
        @method('PUT')

        <div class="row g-4">
            <div class="col-12 col-xl-8">
                <x-layout.modern-card class="mb-4">
                    <h5 class="preview-title">Identitas & Kontak</h5>

                    <div class="row g-3">
                        @foreach ($fields as $key => $field)
                            <div class="col-12 {{ in_array($field['type'], ['textarea'], true) ? '' : 'col-md-6' }}">
                                @if ($field['type'] === 'textarea')
                                    <x-form.textarea
                                        :name="'settings['.$key.']'"
                                        :label="$field['label']"
                                        :value="$setting($key)"
                                        :placeholder="$field['placeholder'] ?? ''"
                                        :rows="$key === 'map_embed' ? 3 : 2"
                                        :hint="$field['hint'] ?? null"
                                        :error="$errors->first('settings.'.$key)"
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
                                    />
                                @endif
                            </div>
                        @endforeach
                    </div>
                </x-layout.modern-card>

                <x-layout.modern-card>
                    <div class="d-flex justify-content-end gap-2">
                        <x-ui.button variant="secondary" :href="route('dashboard')">Batal</x-ui.button>
                        <x-ui.button type="submit" variant="primary" icon="fas fa-save">Simpan Pengaturan</x-ui.button>
                    </div>
                </x-layout.modern-card>
            </div>

            <div class="col-12 col-xl-4">
                <x-layout.modern-card>
                    <h5 class="preview-title">Catatan</h5>
                    <ul class="text-muted small ps-3 mb-0 d-flex flex-column gap-2">
                        <li>Nomor WhatsApp sebaiknya diawali kode negara, misalnya <code>62812...</code>.</li>
                        <li>Perubahan langsung tampil di halaman publik yang menggunakan data ini.</li>
                        <li>Peta memerlukan URL embed dari Google Maps, bukan tautan biasa.</li>
                        <li>Kosongkan kolom jika informasi tersebut belum tersedia.</li>
                    </ul>
                </x-layout.modern-card>
            </div>
        </div>
    </form>
</x-layouts.app>
