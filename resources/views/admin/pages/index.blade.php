@php
    $indexQuery = array_filter([
        'q' => $search,
        'page' => request('page'),
    ], fn ($value) => $value !== null && $value !== '');

    $closeUrl = route('admin.pages', $indexQuery);
@endphp

<x-layouts.app title="Halaman - RodaTa Admin">
    <x-layout.page-header title="Halaman" subtitle="Kelola halaman statis seperti Tentang Kami dan Kebijakan Privasi">
        <x-slot:actions>
            <x-ui.button variant="primary" icon="fas fa-plus" :href="route('admin.pages.create')">
                Tambah Halaman
            </x-ui.button>
        </x-slot:actions>
    </x-layout.page-header>

    <x-layout.modern-card>
        <div class="d-flex justify-content-between align-items-center mb-4 gap-3">
            <h5 class="mb-0 fw-semibold text-body">Daftar Halaman</h5>
            <form method="GET" action="{{ route('admin.pages') }}" style="max-width: 300px; width: 100%;">
                <div class="input-group">
                    <input type="text" name="q" value="{{ $search }}" class="form-control" placeholder="Cari halaman...">
                    <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-modern">
                <thead>
                    <tr>
                        <th>Judul</th>
                        <th>Slug</th>
                        <th>Status</th>
                        <th>Diperbarui</th>
                        <th style="width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pages as $page)
                        <tr>
                            <td class="fw-semibold text-body">{{ $page->title }}</td>
                            <td class="text-muted">{{ $page->slug }}</td>
                            <td>
                                @if ($page->is_published)
                                    <x-ui.badge variant="success">Terbit</x-ui.badge>
                                @else
                                    <x-ui.badge variant="warning">Draf</x-ui.badge>
                                @endif
                            </td>
                            <td class="text-muted">{{ $page->updated_at?->format('d M Y H:i') }}</td>
                            <td>
                                <div class="d-flex gap-1">
                                    <x-ui.btn-edit :href="route('admin.pages.edit', $page)" tooltip="Edit halaman" icon-only />
                                    <x-ui.btn-delete
                                        :href="route('admin.pages', $indexQuery + ['delete' => $page->id])"
                                        tooltip="Hapus halaman"
                                        icon-only
                                    />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">
                                <x-ui.empty-state
                                    icon="fas fa-file-lines"
                                    title="Belum ada halaman"
                                    description="Tambahkan halaman statis untuk situs publik."
                                    action-label="Tambah Halaman"
                                    :action-href="route('admin.pages.create')"
                                />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($pages->hasPages())
            <div class="d-flex justify-content-end mt-4">
                {{ $pages->links() }}
            </div>
        @endif
    </x-layout.modern-card>

    @if ($deletingPage)
        <x-ui.confirm-modal
            :show="true"
            title="Hapus Halaman"
            :message="'Hapus halaman '.$deletingPage->title.'? Tindakan ini tidak dapat dibatalkan.'"
            confirm-text="Hapus Halaman"
            :action="route('admin.pages.destroy', $deletingPage)"
            method="DELETE"
            :cancel-url="$closeUrl"
            variant="danger"
        />
    @endif
</x-layouts.app>
