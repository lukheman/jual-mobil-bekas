<?php

namespace App\Models;

use App\Enums\BodyType;
use App\Enums\CarStatus;
use App\Enums\FuelType;
use App\Enums\Transmission;
use App\Models\Concerns\HasSlug;
use Database\Factories\CarFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Car extends Model
{
    /** @use HasFactory<CarFactory> */
    use HasFactory, HasSlug, SoftDeletes;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'car_model_id',
        'variant',
        'slug',
        'year',
        'price',
        'mileage',
        'transmission',
        'fuel_type',
        'engine_capacity',
        'body_type',
        'color',
        'owner_count',
        'plate_area',
        'tax_expiry',
        'document_completeness',
        'condition',
        'description',
        'status',
        'is_featured',
        'views_count',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'price' => 'integer',
            'mileage' => 'integer',
            'transmission' => Transmission::class,
            'fuel_type' => FuelType::class,
            'engine_capacity' => 'integer',
            'body_type' => BodyType::class,
            'owner_count' => 'integer',
            'tax_expiry' => 'date',
            'status' => CarStatus::class,
            'is_featured' => 'boolean',
            'views_count' => 'integer',
            'deleted_at' => 'datetime',
        ];
    }

    public function carModel(): BelongsTo
    {
        return $this->belongsTo(CarModel::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(CarPhoto::class)->orderBy('sort_order');
    }

    public function primaryPhoto(): HasOne
    {
        return $this->hasOne(CarPhoto::class)
            ->where('is_primary', true)
            ->orderBy('sort_order');
    }

    public function features(): BelongsToMany
    {
        return $this->belongsToMany(Feature::class);
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    public function clickLogs(): HasMany
    {
        return $this->hasMany(ClickLog::class);
    }

    /**
     * Get the primary photo URL, if available.
     */
    public function imageUrl(): ?string
    {
        return $this->primaryPhoto?->url();
    }

    public function scopeAvailable(Builder $query): void
    {
        $query->where('status', CarStatus::Available);
    }

    public function scopeFeatured(Builder $query): void
    {
        $query->where('is_featured', true);
    }

    public function slugSource(): string
    {
        $this->loadMissing('carModel.brand');

        return implode(' ', array_filter([
            $this->carModel?->brand?->name,
            $this->carModel?->name,
            $this->year,
            $this->variant,
        ]));
    }
}
