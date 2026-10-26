<?php

namespace App\Enums;

enum LeadStatus: string
{
    case New = 'new';
    case Contacted = 'contacted';
    case TestDrive = 'test_drive';
    case Deal = 'deal';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::New => 'Baru',
            self::Contacted => 'Dihubungi',
            self::TestDrive => 'Test Drive',
            self::Deal => 'Deal',
            self::Cancelled => 'Batal',
        };
    }

    public function isClosed(): bool
    {
        return in_array($this, [self::Deal, self::Cancelled], true);
    }
}
