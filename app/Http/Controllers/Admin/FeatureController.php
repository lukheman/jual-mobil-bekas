<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FeatureRequest;
use App\Models\Feature;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FeatureController extends Controller
{
    /**
     * Display the feature list.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));

        $features = Feature::query()
            ->withCount('cars')
            ->when($search !== '', fn ($query) => $query->where('name', 'like', "%{$search}%"))
            ->orderBy('name')
            ->paginate(20)
            ->appends($request->query());

        return view('admin.features.index', [
            'features' => $features,
            'search' => $search,
            'editingFeature' => $request->filled('edit')
                ? Feature::findOrFail($request->integer('edit'))
                : null,
            'deletingFeature' => $request->filled('delete')
                ? Feature::findOrFail($request->integer('delete'))
                : null,
            'showCreateModal' => $request->boolean('create'),
        ]);
    }

    /**
     * Store a new feature.
     */
    public function store(FeatureRequest $request): RedirectResponse
    {
        Feature::create($request->validated());

        return redirect()
            ->route('admin.features')
            ->with('success', 'Fitur berhasil ditambahkan.');
    }

    /**
     * Update a feature.
     */
    public function update(FeatureRequest $request, Feature $feature): RedirectResponse
    {
        $feature->update($request->validated());

        return redirect()
            ->route('admin.features')
            ->with('success', 'Fitur berhasil diperbarui.');
    }

    /**
     * Delete a feature.
     */
    public function destroy(Feature $feature): RedirectResponse
    {
        $feature->delete();

        return redirect()
            ->route('admin.features')
            ->with('success', 'Fitur berhasil dihapus.');
    }
}
