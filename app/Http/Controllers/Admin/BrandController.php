<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BrandRequest;
use App\Models\Brand;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class BrandController extends Controller
{
    /**
     * Display the brand list.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $coverage = (string) $request->query('coverage', '');
        $sort = (string) $request->query('sort', 'name_asc');

        $coverage = in_array($coverage, ['with_models', 'without_models', 'with_cars'], true)
            ? $coverage
            : '';

        $sortOptions = [
            'name_asc' => 'Nama A–Z',
            'name_desc' => 'Nama Z–A',
            'models_desc' => 'Model terbanyak',
            'cars_desc' => 'Mobil terbanyak',
        ];

        if (! array_key_exists($sort, $sortOptions)) {
            $sort = 'name_asc';
        }

        $brands = Brand::query()
            ->withCount(['carModels', 'cars'])
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%");
                });
            })
            ->when($coverage === 'with_models', fn ($query) => $query->has('carModels'))
            ->when($coverage === 'without_models', fn ($query) => $query->doesntHave('carModels'))
            ->when($coverage === 'with_cars', fn ($query) => $query->has('cars'));

        $brands = match ($sort) {
            'name_desc' => $brands->orderByDesc('name'),
            'models_desc' => $brands->orderByDesc('car_models_count')->orderBy('name'),
            'cars_desc' => $brands->orderByDesc('cars_count')->orderBy('name'),
            default => $brands->orderBy('name'),
        };

        $brands = $brands->paginate(15)->appends($request->query());

        $totalBrands = Brand::count();
        $brandsWithModels = Brand::has('carModels')->count();

        return view('admin.brands.index', [
            'brands' => $brands,
            'search' => $search,
            'coverage' => $coverage,
            'sort' => $sort,
            'sortOptions' => $sortOptions,
            'stats' => [
                'total' => $totalBrands,
                'with_models' => $brandsWithModels,
                'without_models' => $totalBrands - $brandsWithModels,
                'with_cars' => Brand::has('cars')->count(),
            ],
            'editingBrand' => $request->filled('edit')
                ? Brand::withCount(['carModels', 'cars'])->findOrFail($request->integer('edit'))
                : null,
            'deletingBrand' => $request->filled('delete')
                ? Brand::withCount(['carModels', 'cars'])->findOrFail($request->integer('delete'))
                : null,
            'showCreateModal' => $request->boolean('create'),
        ]);
    }

    /**
     * Store a new brand.
     */
    public function store(BrandRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        Brand::create([
            'name' => $validated['name'],
            'logo' => $request->file('logo')?->store('brands', 'public'),
        ]);

        return redirect()
            ->route('admin.brands', $this->returnQuery($request))
            ->with('success', 'Merek berhasil ditambahkan.');
    }

    /**
     * Update a brand.
     */
    public function update(BrandRequest $request, Brand $brand): RedirectResponse
    {
        $validated = $request->validated();
        $logo = $brand->logo;

        if ($request->hasFile('logo')) {
            Storage::disk('public')->delete($brand->logo);
            $logo = $request->file('logo')->store('brands', 'public');
        }

        $brand->update([
            'name' => $validated['name'],
            'logo' => $logo,
        ]);

        return redirect()
            ->route('admin.brands', $this->returnQuery($request))
            ->with('success', 'Merek berhasil diperbarui.');
    }

    /**
     * Delete a brand.
     */
    public function destroy(Request $request, Brand $brand): RedirectResponse
    {
        if ($brand->carModels()->exists()) {
            return back()->with('error', 'Merek masih memiliki model atau mobil. Hapus data terkait terlebih dahulu.');
        }

        Storage::disk('public')->delete($brand->logo);
        $brand->delete();

        return redirect()
            ->route('admin.brands', $this->returnQuery($request))
            ->with('success', 'Merek berhasil dihapus.');
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
