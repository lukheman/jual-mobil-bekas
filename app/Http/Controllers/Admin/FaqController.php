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
        $status = (string) $request->query('status', '');
        $sort = (string) $request->query('sort', 'order_asc');

        $status = in_array($status, ['published', 'draft'], true) ? $status : '';

        $sortOptions = [
            'order_asc' => 'Urutan tampil',
            'order_desc' => 'Urutan terbalik',
            'newest' => 'Terbaru diperbarui',
            'question_asc' => 'Pertanyaan A–Z',
        ];

        if (! array_key_exists($sort, $sortOptions)) {
            $sort = 'order_asc';
        }

        $faqs = Faq::query()
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('question', 'like', "%{$search}%")
                        ->orWhere('answer', 'like', "%{$search}%");
                });
            })
            ->when($status === 'published', fn ($query) => $query->where('is_published', true))
            ->when($status === 'draft', fn ($query) => $query->where('is_published', false));

        $faqs = match ($sort) {
            'order_desc' => $faqs->orderByDesc('sort_order')->orderByDesc('id'),
            'newest' => $faqs->orderByDesc('updated_at')->orderBy('sort_order'),
            'question_asc' => $faqs->orderBy('question'),
            default => $faqs->orderBy('sort_order')->orderBy('id'),
        };

        $faqs = $faqs->paginate(20)->appends($request->query());

        $totalFaqs = Faq::count();
        $publishedFaqs = Faq::where('is_published', true)->count();

        return view('admin.faqs.index', [
            'faqs' => $faqs,
            'search' => $search,
            'status' => $status,
            'sort' => $sort,
            'sortOptions' => $sortOptions,
            'stats' => [
                'total' => $totalFaqs,
                'published' => $publishedFaqs,
                'draft' => $totalFaqs - $publishedFaqs,
                'recent' => Faq::where('updated_at', '>=', now()->subDays(7))->count(),
            ],
            'nextSortOrder' => (int) Faq::max('sort_order') + 1,
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
            ->route('admin.faqs', $this->returnQuery($request))
            ->with('success', 'FAQ berhasil ditambahkan.');
    }

    /**
     * Update a FAQ.
     */
    public function update(FaqRequest $request, Faq $faq): RedirectResponse
    {
        $faq->update($request->validated());

        return redirect()
            ->route('admin.faqs', $this->returnQuery($request))
            ->with('success', 'FAQ berhasil diperbarui.');
    }

    /**
     * Toggle the published state of a FAQ.
     */
    public function togglePublish(Request $request, Faq $faq): RedirectResponse
    {
        $faq->update(['is_published' => ! $faq->is_published]);

        return redirect()
            ->route('admin.faqs', $this->returnQuery($request))
            ->with('success', $faq->is_published
                ? 'FAQ berhasil diterbitkan.'
                : 'FAQ berhasil dijadikan draf.');
    }

    /**
     * Delete a FAQ.
     */
    public function destroy(Request $request, Faq $faq): RedirectResponse
    {
        $faq->delete();

        return redirect()
            ->route('admin.faqs', $this->returnQuery($request))
            ->with('success', 'FAQ berhasil dihapus.');
    }

    /**
     * Preserve the list state after a write action.
     *
     * @return array<string, string>
     */
    private function returnQuery(Request $request): array
    {
        return array_filter([
            'q' => $request->input('return_q'),
            'status' => $request->input('return_status'),
            'sort' => $request->input('return_sort'),
            'page' => $request->input('return_page'),
        ], fn ($value) => $value !== null && $value !== '');
    }
}
