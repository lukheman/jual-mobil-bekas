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

        $carModels = CarModel::query()
            ->with('brand')
            ->withCount('cars')
            ->when($search !== '', fn ($query) => $query->where('name', 'like', "%{$search}%"))
            ->when($brandId > 0, fn ($query) => $query->where('brand_id', $brandId))
            ->orderBy('name')
            ->paginate(15)
            ->appends($request->query());

        return view('admin.car-models.index', [
            'carModels' => $carModels,
            'brands' => Brand::query()->orderBy('name')->get(),
            'search' => $search,
            'brandId' => $brandId,
            'editingCarModel' => $request->filled('edit')
                ? CarModel::findOrFail($request->integer('edit'))
                : null,
            'deletingCarModel' => $request->filled('delete')
                ? CarModel::findOrFail($request->integer('delete'))
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
            ->route('admin.car-models')
            ->with('success', 'Model mobil berhasil ditambahkan.');
    }

    /**
     * Update a car model.
     */
    public function update(CarModelRequest $request, CarModel $carModel): RedirectResponse
    {
        $carModel->update($request->validated());

        return redirect()
            ->route('admin.car-models')
            ->with('success', 'Model mobil berhasil diperbarui.');
    }

    /**
     * Delete a car model.
     */
    public function destroy(CarModel $carModel): RedirectResponse
    {
        if ($carModel->cars()->withTrashed()->exists()) {
            return back()->with('error', 'Model masih digunakan oleh mobil. Pindahkan atau hapus mobil tersebut terlebih dahulu.');
        }

        $carModel->delete();

        return redirect()
            ->route('admin.car-models')
            ->with('success', 'Model mobil berhasil dihapus.');
    }
}
