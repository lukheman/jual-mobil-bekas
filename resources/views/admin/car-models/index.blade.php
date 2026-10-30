@php
    $indexQuery = array_filter([
        'q' => $search,
        'brand' => $brandId ?: null,
        'page' => request('page'),
    ], fn ($value) => $value !== null && $value !== '');

    $closeUrl = route('admin.car-models', $indexQuery);
    $brandOptions = $brands->pluck('name', 'id')->all();
@endphp

<x-layouts.app title="Model Mobil - RodaTa Admin">
    <x-layout.page-header title="Model Mobil" subtitle="Kelola model yang terhubung dengan merek">
        <x-slot:actions>
            <x-ui.button variant="primary" icon="fas fa-plus" :href="route('admin.car-models', $indexQuery + ['create' => 1])">
                Tambah Model
            </x-ui.button>
        </x-slot:actions>
    </x-layout.page-header>

    <x-layout.modern-card>
        <form method="GET" action="{{ route('admin.car-models') }}" class="row g-2 align-items-end mb-4">
            <div class="col-12 col-md-6">
                <label for="q" class="form-label">Cari Model</label>
                <input type="text" id="q" name="q" value="{{ $search }}" class="form-control" placeholder="Nama model...">
            </div>
            <div class="col-8 col-md-3">
                <label for="brand" class="form-label">Merek</label>
                <select id="brand" name="brand" class="form-select">
                    <option value="">Semua merek</option>
                    @foreach ($brands as $brandOption)
                        <option value="{{ $brandOption->id }}" @selected($brandId === $brandOption->id)>
                            {{ $brandOption->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-4 col-md-3 d-flex gap-2">
                <x-ui.button type="submit" variant="primary" icon="fas fa-search" class="flex-grow-1">Filter</x-ui.button>
                <x-ui.button variant="secondary" :href="route('admin.car-models')">Reset</x-ui.button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-modern">
                <thead>
                    <tr>
                        <th>Model</th>
                        <th>Merek</th>
                        <th>Slug</th>
                        <th>Mobil</th>
                        <th style="width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($carModels as $carModel)
                        <tr>
                            <td class="fw-semibold text-body">{{ $carModel->name }}</td>
                            <td>
                                <x-ui.badge variant="primary" icon="fas fa-trademark">
                                    {{ $carModel->brand?->name }}
                                </x-ui.badge>
                            </td>
                            <td class="text-muted">{{ $carModel->slug }}</td>
                            <td class="text-secondary">{{ $carModel->cars_count }}</td>
                            <td>
                                <div class="d-flex gap-1">
                                    <x-ui.btn-edit
                                        :href="route('admin.car-models', $indexQuery + ['edit' => $carModel->id])"
                                        tooltip="Edit model"
                                        icon-only
                                    />
                                    <x-ui.btn-delete
                                        :href="route('admin.car-models', $indexQuery + ['delete' => $carModel->id])"
                                        tooltip="Hapus model"
                                        icon-only
                                    />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">
                                <x-ui.empty-state
                                    icon="fas fa-car"
                                    title="Belum ada model"
                                    description="Tambahkan model untuk merek yang sudah ada."
                                    action-label="Tambah Model"
                                    :action-href="route('admin.car-models', ['create' => 1])"
                                />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($carModels->hasPages())
            <div class="d-flex justify-content-end mt-4">
                {{ $carModels->links() }}
            </div>
        @endif
    </x-layout.modern-card>

    @if ($showCreateModal && ! $editingCarModel && ! $deletingCarModel)
        <x-layout.modal-form
            title="Tambah Model"
            :action="route('admin.car-models.store')"
            :close-url="$closeUrl"
            submit-label="Simpan Model"
        >
            <x-form.select
                name="brand_id"
                label="Merek"
                :options="$brandOptions"
                :selected="old('brand_id')"
                placeholder="Pilih merek"
                required
                :error="$errors->first('brand_id')"
            />

            <x-form.input
                name="name"
                label="Nama Model"
                :value="old('name')"
                placeholder="Avanza"
                required
                :error="$errors->first('name')"
            />
        </x-layout.modal-form>
    @endif

    @if ($editingCarModel)
        <x-layout.modal-form
            title="Edit Model"
            :action="route('admin.car-models.update', $editingCarModel)"
            method="PUT"
            :close-url="$closeUrl"
            submit-label="Simpan Perubahan"
        >
            <x-form.select
                name="brand_id"
                label="Merek"
                :options="$brandOptions"
                :selected="old('brand_id', $editingCarModel->brand_id)"
                placeholder="Pilih merek"
                required
                :error="$errors->first('brand_id')"
            />

            <x-form.input
                name="name"
                label="Nama Model"
                :value="old('name', $editingCarModel->name)"
                placeholder="Avanza"
                required
                :error="$errors->first('name')"
            />
        </x-layout.modal-form>
    @endif

    @if ($deletingCarModel && ! $editingCarModel)
        <x-ui.confirm-modal
            :show="true"
            title="Hapus Model"
            :message="'Hapus model '.$deletingCarModel->name.'? Model yang masih digunakan mobil tidak dapat dihapus.'"
            confirm-text="Hapus Model"
            :action="route('admin.car-models.destroy', $deletingCarModel)"
            method="DELETE"
            :cancel-url="$closeUrl"
            variant="danger"
        />
    @endif
</x-layouts.app>
