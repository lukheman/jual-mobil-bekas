@php
    $indexQuery = array_filter([
        'q' => $search,
        'page' => request('page'),
    ], fn ($value) => $value !== null && $value !== '');

    $closeUrl = route('admin.features', $indexQuery);
@endphp

<x-layouts.app title="Fitur - RodaTa Admin">
    <x-layout.page-header title="Fitur" subtitle="Kelola daftar fitur yang bisa dipilih pada unit">
        <x-slot:actions>
            <x-ui.button variant="primary" icon="fas fa-plus" :href="route('admin.features', $indexQuery + ['create' => 1])">
                Tambah Fitur
            </x-ui.button>
        </x-slot:actions>
    </x-layout.page-header>

    <x-layout.modern-card>
        <div class="d-flex justify-content-between align-items-center mb-4 gap-3">
            <h5 class="mb-0 fw-semibold text-body">Daftar Fitur</h5>
            <form method="GET" action="{{ route('admin.features') }}" style="max-width: 300px; width: 100%;">
                <div class="input-group">
                    <input type="text" name="q" value="{{ $search }}" class="form-control" placeholder="Cari fitur...">
                    <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-modern">
                <thead>
                    <tr>
                        <th>Fitur</th>
                        <th>Digunakan pada</th>
                        <th style="width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($features as $feature)
                        <tr>
                            <td class="fw-semibold text-body">{{ $feature->name }}</td>
                            <td class="text-secondary">{{ $feature->cars_count }} mobil</td>
                            <td>
                                <div class="d-flex gap-1">
                                    <x-ui.btn-edit
                                        :href="route('admin.features', $indexQuery + ['edit' => $feature->id])"
                                        tooltip="Edit fitur"
                                        icon-only
                                    />
                                    <x-ui.btn-delete
                                        :href="route('admin.features', $indexQuery + ['delete' => $feature->id])"
                                        tooltip="Hapus fitur"
                                        icon-only
                                    />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center py-4">
                                <x-ui.empty-state
                                    icon="fas fa-list-check"
                                    title="Belum ada fitur"
                                    description="Tambahkan fitur untuk memperkaya informasi unit."
                                    action-label="Tambah Fitur"
                                    :action-href="route('admin.features', ['create' => 1])"
                                />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($features->hasPages())
            <div class="d-flex justify-content-end mt-4">
                {{ $features->links() }}
            </div>
        @endif
    </x-layout.modern-card>

    @if ($showCreateModal && ! $editingFeature && ! $deletingFeature)
        <x-layout.modal-form
            title="Tambah Fitur"
            :action="route('admin.features.store')"
            :close-url="$closeUrl"
            submit-label="Simpan Fitur"
        >
            <x-form.input
                name="name"
                label="Nama Fitur"
                :value="old('name')"
                placeholder="Bebas banjir"
                required
                :error="$errors->first('name')"
            />
        </x-layout.modal-form>
    @endif

    @if ($editingFeature)
        <x-layout.modal-form
            title="Edit Fitur"
            :action="route('admin.features.update', $editingFeature)"
            method="PUT"
            :close-url="$closeUrl"
            submit-label="Simpan Perubahan"
        >
            <x-form.input
                name="name"
                label="Nama Fitur"
                :value="old('name', $editingFeature->name)"
                placeholder="Bebas banjir"
                required
                :error="$errors->first('name')"
            />
        </x-layout.modal-form>
    @endif

    @if ($deletingFeature && ! $editingFeature)
        <x-ui.confirm-modal
            :show="true"
            title="Hapus Fitur"
            :message="'Hapus fitur '.$deletingFeature->name.'? Fitur akan dilepas dari semua mobil yang memakainya.'"
            confirm-text="Hapus Fitur"
            :action="route('admin.features.destroy', $deletingFeature)"
            method="DELETE"
            :cancel-url="$closeUrl"
            variant="danger"
        />
    @endif
</x-layouts.app>
