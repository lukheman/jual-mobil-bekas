@php
    $indexQuery = array_filter([
        'q' => $search,
        'page' => request('page'),
    ], fn ($value) => $value !== null && $value !== '');

    $closeUrl = route('admin.faqs', $indexQuery);
@endphp

<x-layouts.app title="FAQ - RodaTa Admin">
    <x-layout.page-header title="FAQ" subtitle="Kelola pertanyaan yang sering diajukan pembeli">
        <x-slot:actions>
            <x-ui.button variant="primary" icon="fas fa-plus" :href="route('admin.faqs', $indexQuery + ['create' => 1])">
                Tambah FAQ
            </x-ui.button>
        </x-slot:actions>
    </x-layout.page-header>

    <x-layout.modern-card>
        <div class="d-flex justify-content-between align-items-center mb-4 gap-3">
            <h5 class="mb-0 fw-semibold text-body">Daftar FAQ</h5>
            <form method="GET" action="{{ route('admin.faqs') }}" style="max-width: 300px; width: 100%;">
                <div class="input-group">
                    <input type="text" name="q" value="{{ $search }}" class="form-control" placeholder="Cari pertanyaan...">
                    <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-modern">
                <thead>
                    <tr>
                        <th style="width: 70px;">Urutan</th>
                        <th>Pertanyaan</th>
                        <th>Status</th>
                        <th style="width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($faqs as $faq)
                        <tr>
                            <td class="text-muted">{{ $faq->sort_order }}</td>
                            <td>
                                <div class="fw-semibold text-body">{{ $faq->question }}</div>
                                <small class="text-muted">{{ \Illuminate\Support\Str::limit($faq->answer, 110) }}</small>
                            </td>
                            <td>
                                @if ($faq->is_published)
                                    <x-ui.badge variant="success">Terbit</x-ui.badge>
                                @else
                                    <x-ui.badge variant="warning">Draf</x-ui.badge>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <x-ui.btn-edit
                                        :href="route('admin.faqs', $indexQuery + ['edit' => $faq->id])"
                                        tooltip="Edit FAQ"
                                        icon-only
                                    />
                                    <x-ui.btn-delete
                                        :href="route('admin.faqs', $indexQuery + ['delete' => $faq->id])"
                                        tooltip="Hapus FAQ"
                                        icon-only
                                    />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4">
                                <x-ui.empty-state
                                    icon="fas fa-circle-question"
                                    title="Belum ada FAQ"
                                    description="Tambahkan pertanyaan untuk membantu pembeli."
                                    action-label="Tambah FAQ"
                                    :action-href="route('admin.faqs', ['create' => 1])"
                                />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($faqs->hasPages())
            <div class="d-flex justify-content-end mt-4">
                {{ $faqs->links() }}
            </div>
        @endif
    </x-layout.modern-card>

    @if ($showCreateModal && ! $editingFaq && ! $deletingFaq)
        <x-layout.modal-form
            title="Tambah FAQ"
            :action="route('admin.faqs.store')"
            :close-url="$closeUrl"
            submit-label="Simpan FAQ"
        >
            <x-form.input
                name="question"
                label="Pertanyaan"
                :value="old('question')"
                placeholder="Apakah bisa test drive?"
                required
                :error="$errors->first('question')"
            />

            <x-form.textarea
                name="answer"
                label="Jawaban"
                :value="old('answer')"
                placeholder="Tulis jawaban..."
                :rows="4"
                required
                :error="$errors->first('answer')"
            />

            <x-form.input
                type="number"
                name="sort_order"
                label="Urutan"
                :value="old('sort_order', 0)"
                min="0"
                hint="Angka kecil tampil lebih dulu."
                :error="$errors->first('sort_order')"
            />

            <x-form.toggle
                name="is_published"
                label="Terbitkan FAQ"
                :checked="old('is_published', true)"
            />
        </x-layout.modal-form>
    @endif

    @if ($editingFaq)
        <x-layout.modal-form
            title="Edit FAQ"
            :action="route('admin.faqs.update', $editingFaq)"
            method="PUT"
            :close-url="$closeUrl"
            submit-label="Simpan Perubahan"
        >
            <x-form.input
                name="question"
                label="Pertanyaan"
                :value="old('question', $editingFaq->question)"
                placeholder="Apakah bisa test drive?"
                required
                :error="$errors->first('question')"
            />

            <x-form.textarea
                name="answer"
                label="Jawaban"
                :value="old('answer', $editingFaq->answer)"
                placeholder="Tulis jawaban..."
                :rows="4"
                required
                :error="$errors->first('answer')"
            />

            <x-form.input
                type="number"
                name="sort_order"
                label="Urutan"
                :value="old('sort_order', $editingFaq->sort_order)"
                min="0"
                hint="Angka kecil tampil lebih dulu."
                :error="$errors->first('sort_order')"
            />

            <x-form.toggle
                name="is_published"
                label="Terbitkan FAQ"
                :checked="old('is_published', $editingFaq->is_published)"
            />
        </x-layout.modal-form>
    @endif

    @if ($deletingFaq && ! $editingFaq)
        <x-ui.confirm-modal
            :show="true"
            title="Hapus FAQ"
            :message="'Hapus FAQ \''.$deletingFaq->question.'\'? Tindakan ini tidak dapat dibatalkan.'"
            confirm-text="Hapus FAQ"
            :action="route('admin.faqs.destroy', $deletingFaq)"
            method="DELETE"
            :cancel-url="$closeUrl"
            variant="danger"
        />
    @endif
</x-layouts.app>
