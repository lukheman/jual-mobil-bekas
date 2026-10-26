<?php

namespace App\Enums;

enum ClickType: string
{
    case Whatsapp = 'whatsapp';
    case Phone = 'phone';

    public function label(): string
    {
        return match ($this) {
            self::Whatsapp => 'WhatsApp',
            self::Phone => 'Telepon',
        };
    }
}
