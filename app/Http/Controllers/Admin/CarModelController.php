<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CarModelRequest;
use App\Models\Brand;
use App\Models\CarModel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CarModelController extends Controller
{
    /**
     * Display the car model list.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $brandId = $request->integer('brand');
        $coverage = (string) $request->query('coverage', '');
        $sort = (string) $request->query('sort', 'name_asc');

        $coverage = in_array($coverage, ['with_cars', 'without_cars'], true) ? $coverage : '';

        $sortOptions = [
            'name_asc' => 'Nama A–Z',
            'name_desc' => 'Nama Z–A',
            'brand_asc' => 'Merek A–Z',
            'cars_desc' => 'Mobil terbanyak',
        ];

        if (! array_key_exists($sort, $sortOptions)) {
            $sort = 'name_asc';
        }

        $carModels = CarModel::query()
            ->with('brand')
            ->withCount('cars')
            ->withCount(['cars as all_cars_count' => fn ($query) => $query->withTrashed()])
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%")
                        ->orWhereHas('brand', fn ($query) => $query->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($brandId > 0, fn ($query) => $query->where('brand_id', $brandId))
            ->when($coverage === 'with_cars', fn ($query) => $query->has('cars'))
            ->when($coverage === 'without_cars', fn ($query) => $query->doesntHave('cars'));

        $carModels = match ($sort) {
            'name_desc' => $carModels->orderByDesc('name'),
            'brand_asc' => $carModels
                ->orderBy(Brand::query()->select('name')->whereColumn('brands.id', 'car_models.brand_id'))
                ->orderBy('name'),
            'cars_desc' => $carModels->orderByDesc('cars_count')->orderBy('name'),
            default => $carModels->orderBy('name'),
        };

        $carModels = $carModels->paginate(15)->appends($request->query());

        $totalModels = CarModel::count();
        $modelsWithCars = CarModel::has('cars')->count();

        return view('admin.car-models.index', [
            'carModels' => $carModels,
            'brands' => Brand::query()->withCount('carModels')->orderBy('name')->get(),
            'search' => $search,
            'brandId' => $brandId,
            'coverage' => $coverage,
            'sort' => $sort,
            'sortOptions' => $sortOptions,
            'stats' => [
                'total' => $totalModels,
                'with_cars' => $modelsWithCars,
                'without_cars' => $totalModels - $modelsWithCars,
                'brands_covered' => Brand::has('carModels')->count(),
                'brands_total' => Brand::count(),
            ],
            'editingCarModel' => $request->filled('edit')
                ? CarModel::withCount(['cars', 'cars as all_cars_count' => fn ($query) => $query->withTrashed()])->findOrFail($request->integer('edit'))
                : null,
            'deletingCarModel' => $request->filled('delete')
                ? CarModel::withCount(['cars', 'cars as all_cars_count' => fn ($query) => $query->withTrashed()])->findOrFail($request->integer('delete'))
                : null,
            'showCreateModal' => $request->boolean('create'),
        ]);
    }

    /**
     * Store a new car model.
     */
    public function store(CarModelRequest $request): RedirectResponse
    {
        CarModel::create($request->validated());

        return redirect()
            ->route('admin.car-models', $this->returnQuery($request))
            ->with('success', 'Model mobil berhasil ditambahkan.');
    }

    /**
     * Update a car model.
     */
    public function update(CarModelRequest $request, CarModel $carModel): RedirectResponse
    {
        $carModel->update($request->validated());

        return redirect()
            ->route('admin.car-models', $this->returnQuery($request))
            ->with('success', 'Model mobil berhasil diperbarui.');
    }

    /**
     * Delete a car model.
     */
    public function destroy(Request $request, CarModel $carModel): RedirectResponse
    {
        if ($carModel->cars()->withTrashed()->exists()) {
            return back()->with('error', 'Model masih digunakan oleh mobil. Pindahkan atau hapus mobil tersebut terlebih dahulu.');
        }

        $carModel->delete();

        return redirect()
            ->route('admin.car-models', $this->returnQuery($request))
            ->with('success', 'Model mobil berhasil dihapus.');
    }

    /**
     * Preserve the list state after a write action.
     *
     * @return array<string, string|int>
     */
    private function returnQuery(Request $request): array
    {
        return array_filter([
            'q' => $request->input('return_q'),
            'brand' => $request->input('return_brand'),
            'coverage' => $request->input('return_coverage'),
            'sort' => $request->input('return_sort'),
            'page' => $request->input('return_page'),
        ], fn ($value) => $value !== null && $value !== '');
    }
}
