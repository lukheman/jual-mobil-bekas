<?php

namespace App\Enums;

enum CarStatus: string
{
    case Available = 'available';
    case Reserved = 'reserved';
    case Sold = 'sold';

    public function label(): string
    {
        return match ($this) {
            self::Available => 'Tersedia',
            self::Reserved => 'Dipesan',
            self::Sold => 'Terjual',
        };
    }

    public function isContactable(): bool
    {
        return $this !== self::Sold;
    }
}
