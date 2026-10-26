<?php

namespace App\Models;

use App\Enums\LeadStatus;
use App\Enums\LeadType;
use Database\Factories\LeadFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lead extends Model
{
    /** @use HasFactory<LeadFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'car_id',
        'name',
        'phone',
        'email',
        'type',
        'preferred_schedule',
        'message',
        'status',
        'notes',
        'consent_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => LeadType::class,
            'preferred_schedule' => 'datetime',
            'status' => LeadStatus::class,
            'consent_at' => 'datetime',
        ];
    }

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }
}
