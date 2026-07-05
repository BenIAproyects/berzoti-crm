<?php

namespace App\Enums;

enum FuenteCliente: string
{
    case Lima      = 'lima';
    case Provincia = 'provincia';
    case Otro      = 'otro';

    public function label(): string
    {
        return match($this) {
            self::Lima      => 'Lima',
            self::Provincia => 'Provincia',
            self::Otro      => 'Otro',
        };
    }
}
