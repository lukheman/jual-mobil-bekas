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
        $coverage = (string) $request->query('coverage', '');
        $sort = (string) $request->query('sort', 'name_asc');

        $coverage = in_array($coverage, ['used', 'unused'], true) ? $coverage : '';

        $sortOptions = [
            'name_asc' => 'Nama A–Z',
            'name_desc' => 'Nama Z–A',
            'cars_desc' => 'Paling banyak dipakai',
            'cars_asc' => 'Paling sedikit dipakai',
        ];

        if (! array_key_exists($sort, $sortOptions)) {
            $sort = 'name_asc';
        }

        $features = Feature::query()
            ->withCount('cars')
            ->when($search !== '', fn ($query) => $query->where('name', 'like', "%{$search}%"))
            ->when($coverage === 'used', fn ($query) => $query->has('cars'))
            ->when($coverage === 'unused', fn ($query) => $query->doesntHave('cars'));

        $features = match ($sort) {
            'name_desc' => $features->orderByDesc('name'),
            'cars_desc' => $features->orderByDesc('cars_count')->orderBy('name'),
            'cars_asc' => $features->orderBy('cars_count')->orderBy('name'),
            default => $features->orderBy('name'),
        };

        $features = $features->paginate(20)->appends($request->query());

        $usageSummary = Feature::query()->withCount('cars')->get(['id']);
        $totalFeatures = $usageSummary->count();
        $usedFeatures = $usageSummary->filter(fn (Feature $feature) => $feature->cars_count > 0)->count();

        return view('admin.features.index', [
            'features' => $features,
            'search' => $search,
            'coverage' => $coverage,
            'sort' => $sort,
            'sortOptions' => $sortOptions,
            'stats' => [
                'total' => $totalFeatures,
                'used' => $usedFeatures,
                'unused' => $totalFeatures - $usedFeatures,
                'assignments' => (int) $usageSummary->sum('cars_count'),
            ],
            'maxUsage' => max(1, (int) $usageSummary->max('cars_count')),
            'editingFeature' => $request->filled('edit')
                ? Feature::withCount('cars')->findOrFail($request->integer('edit'))
                : null,
            'deletingFeature' => $request->filled('delete')
                ? Feature::withCount('cars')->findOrFail($request->integer('delete'))
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
            ->route('admin.features', $this->returnQuery($request))
            ->with('success', 'Fitur berhasil ditambahkan.');
    }

    /**
     * Update a feature.
     */
    public function update(FeatureRequest $request, Feature $feature): RedirectResponse
    {
        $feature->update($request->validated());

        return redirect()
            ->route('admin.features', $this->returnQuery($request))
            ->with('success', 'Fitur berhasil diperbarui.');
    }

    /**
     * Delete a feature.
     */
    public function destroy(Request $request, Feature $feature): RedirectResponse
    {
        $feature->delete();

        return redirect()
            ->route('admin.features', $this->returnQuery($request))
            ->with('success', 'Fitur berhasil dihapus.');
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
            'coverage' => $request->input('return_coverage'),
            'sort' => $request->input('return_sort'),
            'page' => $request->input('return_page'),
        ], fn ($value) => $value !== null && $value !== '');
    }
}
