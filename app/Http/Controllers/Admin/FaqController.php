<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FaqRequest;
use App\Models\Faq;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FaqController extends Controller
{
    /**
     * Display the FAQ list.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));

        $faqs = Faq::query()
            ->when($search !== '', fn ($query) => $query->where('question', 'like', "%{$search}%"))
            ->orderBy('sort_order')
            ->orderBy('id')
            ->paginate(20)
            ->appends($request->query());

        return view('admin.faqs.index', [
            'faqs' => $faqs,
            'search' => $search,
            'editingFaq' => $request->filled('edit')
                ? Faq::findOrFail($request->integer('edit'))
                : null,
            'deletingFaq' => $request->filled('delete')
                ? Faq::findOrFail($request->integer('delete'))
                : null,
            'showCreateModal' => $request->boolean('create'),
        ]);
    }

    /**
     * Store a new FAQ.
     */
    public function store(FaqRequest $request): RedirectResponse
    {
        Faq::create($request->validated());

        return redirect()
            ->route('admin.faqs')
            ->with('success', 'FAQ berhasil ditambahkan.');
    }

    /**
     * Update a FAQ.
     */
    public function update(FaqRequest $request, Faq $faq): RedirectResponse
    {
        $faq->update($request->validated());

        return redirect()
            ->route('admin.faqs')
            ->with('success', 'FAQ berhasil diperbarui.');
    }

    /**
     * Delete a FAQ.
     */
    public function destroy(Faq $faq): RedirectResponse
    {
        $faq->delete();

        return redirect()
            ->route('admin.faqs')
            ->with('success', 'FAQ berhasil dihapus.');
    }
}
