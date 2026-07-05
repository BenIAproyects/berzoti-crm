<?php

namespace App\Enums;

enum SegmentoCliente: string
{
    case VIP    = 'vip';
    case Normal = 'normal';
    case Otro   = 'otro';

    public function label(): string
    {
        return match($this) {
            self::VIP    => 'VIP',
            self::Normal => 'Normal',
            self::Otro   => 'Otro',
        };
    }
}
