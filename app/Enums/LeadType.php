<?php

namespace App\Enums;

enum LeadType: string
{
    case Inquiry = 'inquiry';
    case TestDrive = 'test_drive';

    public function label(): string
    {
        return match ($this) {
            self::Inquiry => 'Tanya',
            self::TestDrive => 'Test Drive',
        };
    }
}
