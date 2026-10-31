<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BodyType;
use App\Enums\CarStatus;
use App\Enums\FuelType;
use App\Enums\Transmission;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCarRequest;
use App\Http\Requests\Admin\UpdateCarRequest;
use App\Models\Brand;
use App\Models\Car;
use App\Models\CarModel;
use App\Models\CarPhoto;
use App\Models\Feature;
use App\Services\CarPhotoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CarController extends Controller
{
    public function __construct(private readonly CarPhotoService $photoService)
    {
        //
    }

    /**
     * Display the car inventory.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $status = (string) $request->query('status', '');
        $brand = (string) $request->query('brand', '');
        $showArchived = $request->boolean('arsip');

        $cars = Car::query()
            ->with(['carModel.brand', 'primaryPhoto'])
            ->withCount('photos')
            ->when($showArchived, fn ($query) => $query->onlyTrashed())
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('slug', 'like', "%{$search}%")
                        ->orWhere('variant', 'like', "%{$search}%")
                        ->orWhereHas('carModel', fn ($query) => $query->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('carModel.brand', fn ($query) => $query->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->when($brand !== '', fn ($query) => $query->whereHas('carModel.brand', fn ($query) => $query->where('slug', $brand)))
            ->latest()
            ->paginate(10)
            ->appends($request->query());

        $statusCounts = Car::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('admin.cars.index', [
            'cars' => $cars,
            'search' => $search,
            'status' => $status,
            'brand' => $brand,
            'showArchived' => $showArchived,
            'archivedCount' => Car::onlyTrashed()->count(),
            'stats' => [
                'total' => (int) $statusCounts->sum(),
                'available' => (int) ($statusCounts[CarStatus::Available->value] ?? 0),
                'reserved' => (int) ($statusCounts[CarStatus::Reserved->value] ?? 0),
                'sold' => (int) ($statusCounts[CarStatus::Sold->value] ?? 0),
            ],
            'deletingCar' => $request->filled('delete')
                ? Car::withTrashed()->findOrFail($request->integer('delete'))
                : null,
            'forceDeletingCar' => $request->filled('force')
                ? Car::withTrashed()->findOrFail($request->integer('force'))
                : null,
            'brands' => Brand::query()->orderBy('name')->get(),
            'statuses' => CarStatus::cases(),
        ]);
    }

    /**
     * Show the form for creating a car.
     */
    public function create(): View
    {
        return view('admin.cars.form', [
            'car' => new Car,
            'carModels' => $this->carModelOptions(),
            'features' => Feature::query()->orderBy('name')->get(),
            'transmissions' => Transmission::cases(),
            'fuelTypes' => FuelType::cases(),
            'bodyTypes' => BodyType::cases(),
            'statuses' => CarStatus::cases(),
        ]);
    }

    /**
     * Store a newly created car.
     */
    public function store(StoreCarRequest $request): RedirectResponse
    {
        $car = DB::transaction(function () use ($request): Car {
            $car = Car::create($this->carAttributes($request));
            $car->features()->sync($request->input('features', []));
            $this->storePhotos($car, $request->file('photos', []));

            return $car;
        });

        return redirect()
            ->route('admin.cars.edit', $car)
            ->with('success', 'Mobil berhasil ditambahkan.');
    }

    /**
     * Show the form for editing a car.
     */
    public function edit(Car $car): View
    {
        $car->load(['carModel.brand', 'photos', 'features']);

        return view('admin.cars.form', [
            'car' => $car,
            'carModels' => $this->carModelOptions(),
            'features' => Feature::query()->orderBy('name')->get(),
            'transmissions' => Transmission::cases(),
            'fuelTypes' => FuelType::cases(),
            'bodyTypes' => BodyType::cases(),
            'statuses' => CarStatus::cases(),
        ]);
    }

    /**
     * Update the specified car.
     */
    public function update(UpdateCarRequest $request, Car $car): RedirectResponse
    {
        DB::transaction(function () use ($request, $car): void {
            $car->update($this->carAttributes($request));
            $car->features()->sync($request->input('features', []));
            $this->storePhotos($car, $request->file('photos', []));
        });

        return redirect()
            ->route('admin.cars.edit', $car)
            ->with('success', 'Mobil berhasil diperbarui.');
    }

    /**
     * Archive the specified car.
     */
    public function destroy(Car $car): RedirectResponse
    {
        $car->delete();

        return redirect()
            ->route('admin.cars')
            ->with('success', 'Mobil berhasil diarsipkan.');
    }

    /**
     * Restore an archived car.
     */
    public function restore(int $car): RedirectResponse
    {
        Car::withTrashed()->findOrFail($car)->restore();

        return redirect()
            ->route('admin.cars', ['arsip' => 1])
            ->with('success', 'Mobil berhasil dipulihkan.');
    }

    /**
     * Permanently delete an archived car and its photos.
     */
    public function forceDestroy(int $car): RedirectResponse
    {
        $car = Car::withTrashed()->findOrFail($car);

        foreach ($car->photos as $photo) {
            $this->photoService->delete($photo->path);
        }

        $car->forceDelete();

        return redirect()
            ->route('admin.cars', ['arsip' => 1])
            ->with('success', 'Mobil berhasil dihapus permanen.');
    }

    /**
     * Quickly change a car's status.
     */
    public function updateStatus(Request $request, Car $car): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:'.implode(',', array_column(CarStatus::cases(), 'value'))],
        ]);

        $car->update(['status' => $validated['status']]);

        return back()->with('success', 'Status mobil berhasil diubah.');
    }

    /**
     * Toggle a car's featured flag.
     */
    public function toggleFeatured(Car $car): RedirectResponse
    {
        $car->update(['is_featured' => ! $car->is_featured]);

        return back()->with('success', $car->is_featured
            ? 'Mobil ditandai sebagai unggulan.'
            : 'Mobil tidak lagi unggulan.');
    }

    /**
     * Upload additional photos for a car.
     */
    public function uploadPhotos(Request $request, Car $car): RedirectResponse
    {
        $request->validate([
            'photos' => ['required', 'array', 'max:12'],
            'photos.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ], [
            'photos.required' => 'Pilih minimal satu foto.',
            'photos.*.image' => 'Setiap foto harus berupa gambar.',
            'photos.*.max' => 'Ukuran setiap foto maksimal 4MB.',
        ]);

        $this->storePhotos($car, $request->file('photos', []));

        // Upload dari panel Edit Mobil menjadi sampul unit terbaru.
        $latestPhoto = $car->photos()->latest('id')->first();
        if ($latestPhoto) {
            $car->photos()->update(['is_primary' => false]);
            $latestPhoto->update(['is_primary' => true]);
        }

        return back()->with('success', 'Foto berhasil diunggah.');
    }

    /**
     * Delete a car photo.
     */
    public function destroyPhoto(Car $car, CarPhoto $photo): RedirectResponse
    {
        abort_unless($photo->car_id === $car->id, 404);

        $this->photoService->delete($photo->path);
        $wasPrimary = $photo->is_primary;
        $photo->delete();

        if ($wasPrimary) {
            $car->photos()->first()?->update(['is_primary' => true]);
        }

        return back()->with('success', 'Foto berhasil dihapus.');
    }

    /**
     * Mark a photo as the primary photo.
     */
    public function setPrimaryPhoto(Car $car, CarPhoto $photo): RedirectResponse
    {
        abort_unless($photo->car_id === $car->id, 404);

        DB::transaction(function () use ($car, $photo): void {
            $car->photos()->update(['is_primary' => false]);
            $photo->update(['is_primary' => true]);
        });

        return back()->with('success', 'Foto utama berhasil diubah.');
    }

    /**
     * Persist a new photo order.
     */
    public function reorderPhotos(Request $request, Car $car): RedirectResponse
    {
        $validated = $request->validate([
            'photo_ids' => ['required', 'array'],
            'photo_ids.*' => ['integer', 'exists:car_photos,id'],
        ]);

        DB::transaction(function () use ($car, $validated): void {
            foreach ($validated['photo_ids'] as $index => $photoId) {
                CarPhoto::query()
                    ->where('car_id', $car->id)
                    ->whereKey($photoId)
                    ->update(['sort_order' => $index]);
            }
        });

        return back()->with('success', 'Urutan foto berhasil disimpan.');
    }

    /**
     * Build validated car attributes.
     *
     * @return array<string, mixed>
     */
    private function carAttributes(StoreCarRequest $request): array
    {
        return [
            'car_model_id' => $request->integer('car_model_id'),
            'variant' => $request->input('variant'),
            'year' => $request->integer('year'),
            'price' => $request->integer('price'),
            'mileage' => $request->integer('mileage'),
            'transmission' => $request->input('transmission'),
            'fuel_type' => $request->input('fuel_type'),
            'engine_capacity' => $request->input('engine_capacity') ?: null,
            'body_type' => $request->input('body_type'),
            'color' => $request->input('color'),
            'owner_count' => $request->integer('owner_count'),
            'plate_area' => $request->input('plate_area'),
            'tax_expiry' => $request->input('tax_expiry') ?: null,
            'document_completeness' => $request->input('document_completeness'),
            'condition' => $request->input('condition'),
            'description' => $request->input('description'),
            'status' => $request->input('status'),
            'is_featured' => $request->boolean('is_featured'),
        ];
    }

    /**
     * Store uploaded photos and mark the first one as primary when needed.
     *
     * @param  array<int, UploadedFile>  $photos
     */
    private function storePhotos(Car $car, array $photos): void
    {
        if ($photos === []) {
            return;
        }

        $photoService = $this->photoService;
        // Foto upload terbaru menjadi foto utama agar langsung tampil di landing page.
        $car->photos()->update(['is_primary' => false]);
        $hasPrimary = false;
        $sortOrder = (int) $car->photos()->max('sort_order');

        foreach ($photos as $photo) {
            $car->photos()->create([
                'path' => $photoService->store($photo),
                'sort_order' => ++$sortOrder,
                'is_primary' => ! $hasPrimary,
            ]);

            $hasPrimary = true;
        }
    }

    /**
     * Get car model options grouped by brand.
     *
     * @return array<int, string>
     */
    private function carModelOptions(): array
    {
        return CarModel::query()
            ->with('brand')
            ->get()
            ->sortBy(fn (CarModel $carModel) => $carModel->brand?->name.' '.$carModel->name)
            ->mapWithKeys(fn (CarModel $carModel) => [
                $carModel->id => ($carModel->brand?->name ?? '-').' — '.$carModel->name,
            ])
            ->all();
    }

}
