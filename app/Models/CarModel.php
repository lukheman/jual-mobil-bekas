<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use Database\Factories\CarModelFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CarModel extends Model
{
    /** @use HasFactory<CarModelFactory> */
    use HasFactory, HasSlug;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'brand_id',
        'name',
        'slug',
    ];

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function cars(): HasMany
    {
        return $this->hasMany(Car::class);
    }

    public function slugSource(): string
    {
        return $this->name;
    }
}
