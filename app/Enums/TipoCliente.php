<?php

namespace App\Enums;

enum TipoCliente: string
{
    case Corporacion = 'corporacion';
    case Comercializadora = 'comercializadora';
    case Distribuidor = 'distribuidor';
    case Minorista = 'minorista';
    case Otro = 'otro';

    public function label(): string
    {
        return match($this) {
            self::Corporacion => 'Corporación',
            self::Comercializadora => 'Comercializadora',
            self::Distribuidor => 'Distribuidor',
            self::Minorista => 'Minorista',
            self::Otro => 'Otro',
        };
    }
}
