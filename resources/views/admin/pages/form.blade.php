@php
    $editing = $page->exists;
    $action = $editing ? route('admin.pages.update', $page) : route('admin.pages.store');
@endphp

<x-layouts.app :title="($editing ? 'Edit Halaman' : 'Tambah Halaman').' - RodaTa Admin'">
    <x-layout.page-header
        :title="$editing ? 'Edit Halaman' : 'Tambah Halaman'"
        subtitle="Kelola konten halaman statis dan metadata SEO-nya"
    >
        <x-slot:actions>
            <x-ui.button variant="secondary" icon="fas fa-arrow-left" :href="route('admin.pages')">
                Kembali
            </x-ui.button>
        </x-slot:actions>
    </x-layout.page-header>

    <form method="POST" action="{{ $action }}">
        @csrf
        @if ($editing)
            @method('PUT')
        @endif

        <div class="row g-4">
            <div class="col-12 col-xl-8">
                <x-layout.modern-card class="mb-4">
                    <h5 class="preview-title">Konten Halaman</h5>

                    <x-form.input
                        name="title"
                        label="Judul"
                        :value="old('title', $page->title)"
                        placeholder="Tentang Kami"
                        required
                        :error="$errors->first('title')"
                    />

                    <x-form.input
                        name="slug"
                        label="Slug"
                        :value="old('slug', $page->slug)"
                        placeholder="tentang-kami"
                        hint="Kosongkan untuk membuat slug otomatis dari judul."
                        :error="$errors->first('slug')"
                    />

                    <x-form.textarea
                        name="content"
                        label="Konten"
                        :value="old('content', $page->content)"
                        placeholder="Tulis konten halaman..."
                        :rows="16"
                        required
                        hint="Mendukung HTML sederhana."
                        :error="$errors->first('content')"
                    />
                </x-layout.modern-card>
            </div>

            <div class="col-12 col-xl-4">
                <x-layout.modern-card class="mb-4">
                    <h5 class="preview-title">Publikasi</h5>

                    <x-form.toggle
                        name="is_published"
                        label="Terbitkan halaman"
                        :checked="old('is_published', $page->is_published)"
                    />

                    @if ($editing)
                        <div class="mt-3 p-3 rounded" style="background: var(--bg-tertiary);">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted small">Dibuat</span>
                                <span class="small">{{ $page->created_at?->format('d M Y H:i') }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted small">Diperbarui</span>
                                <span class="small">{{ $page->updated_at?->format('d M Y H:i') }}</span>
                            </div>
                        </div>
                    @endif
                </x-layout.modern-card>

                <x-layout.modern-card class="mb-4">
                    <h5 class="preview-title">SEO</h5>

                    <x-form.input
                        name="meta_title"
                        label="Meta Title"
                        :value="old('meta_title', $page->meta_title)"
                        placeholder="Tentang MobilTa"
                        :error="$errors->first('meta_title')"
                    />

                    <x-form.textarea
                        name="meta_description"
                        label="Meta Description"
                        :value="old('meta_description', $page->meta_description)"
                        placeholder="Deskripsi singkat halaman..."
                        :rows="4"
                        maxlength="320"
                        :show-count="true"
                        :error="$errors->first('meta_description')"
                    />
                </x-layout.modern-card>

                <x-layout.modern-card>
                    <div class="d-grid gap-2">
                        <x-ui.button type="submit" variant="primary" icon="fas fa-save">
                            {{ $editing ? 'Simpan Perubahan' : 'Simpan Halaman' }}
                        </x-ui.button>
                        <x-ui.button variant="secondary" :href="route('admin.pages')">Batal</x-ui.button>
                    </div>
                </x-layout.modern-card>
            </div>
        </div>
    </form>
</x-layouts.app>
