<?php

namespace App\Enums;

enum TipoTelefono: string
{
    case Celular   = 'celular';
    case Fijo      = 'fijo';
    case Whatsapp  = 'whatsapp';

    public function label(): string
    {
        return match($this) {
            self::Celular  => 'Celular',
            self::Fijo     => 'Fijo',
            self::Whatsapp => 'WhatsApp',
        };
    }
}
