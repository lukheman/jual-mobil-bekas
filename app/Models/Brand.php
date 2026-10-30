<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use Database\Factories\BrandFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Facades\Storage;

class Brand extends Model
{
    /** @use HasFactory<BrandFactory> */
    use HasFactory, HasSlug;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'logo',
    ];

    public function carModels(): HasMany
    {
        return $this->hasMany(CarModel::class);
    }

    public function cars(): HasManyThrough
    {
        return $this->hasManyThrough(Car::class, CarModel::class);
    }

    public function slugSource(): string
    {
        return $this->name;
    }

    /**
     * Resolve the public URL for the brand logo.
     */
    public function logoUrl(): ?string
    {
        if (blank($this->logo)) {
            return null;
        }

        $disk = Storage::disk('public');

        return $disk->exists($this->logo)
            ? $disk->url($this->logo)
            : asset('assets/images/'.$this->logo);
    }
}
