<?php

namespace App\Models;

use Database\Factories\CarPhotoFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CarPhoto extends Model
{
    /** @use HasFactory<CarPhotoFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'car_id',
        'path',
        'sort_order',
        'is_primary',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_primary' => 'boolean',
        ];
    }

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }
}
