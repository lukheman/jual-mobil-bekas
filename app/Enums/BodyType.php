<?php

namespace App\Enums;

enum BodyType: string
{
    case Mpv = 'mpv';
    case Suv = 'suv';
    case Sedan = 'sedan';
    case Hatchback = 'hatchback';
    case Pickup = 'pickup';
    case Van = 'van';
    case Coupe = 'coupe';
    case Wagon = 'wagon';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Mpv => 'MPV',
            self::Suv => 'SUV',
            self::Sedan => 'Sedan',
            self::Hatchback => 'Hatchback',
            self::Pickup => 'Pickup',
            self::Van => 'Van',
            self::Coupe => 'Coupe',
            self::Wagon => 'Wagon',
            self::Other => 'Lainnya',
        };
    }
}
