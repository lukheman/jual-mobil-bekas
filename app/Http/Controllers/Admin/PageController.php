<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PageRequest;
use App\Models\Page;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PageController extends Controller
{
    /**
     * Display the page list.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));

        $pages = Page::query()
            ->when($search !== '', fn ($query) => $query->where('title', 'like', "%{$search}%"))
            ->latest()
            ->paginate(15)
            ->appends($request->query());

        return view('admin.pages.index', [
            'pages' => $pages,
            'search' => $search,
            'deletingPage' => $request->filled('delete')
                ? Page::findOrFail($request->integer('delete'))
                : null,
        ]);
    }

    /**
     * Show the form for creating a page.
     */
    public function create(): View
    {
        return view('admin.pages.form', [
            'page' => new Page(['is_published' => true]),
        ]);
    }

    /**
     * Store a new page.
     */
    public function store(PageRequest $request): RedirectResponse
    {
        $page = Page::create($request->validated());

        return redirect()
            ->route('admin.pages')
            ->with('success', 'Halaman berhasil ditambahkan.');
    }

    /**
     * Show the form for editing a page.
     */
    public function edit(Page $page): View
    {
        return view('admin.pages.form', [
            'page' => $page,
        ]);
    }

    /**
     * Update a page.
     */
    public function update(PageRequest $request, Page $page): RedirectResponse
    {
        $page->update($request->validated());

        return redirect()
            ->route('admin.pages')
            ->with('success', 'Halaman berhasil diperbarui.');
    }

    /**
     * Delete a page.
     */
    public function destroy(Page $page): RedirectResponse
    {
        $page->delete();

        return redirect()
            ->route('admin.pages')
            ->with('success', 'Halaman berhasil dihapus.');
    }
}
