<?php

namespace App\Enums;

enum EstadoCampana: string
{
    case Borrador = 'borrador';
    case Activa = 'activa';
    case Cerrada = 'cerrada';
    case Archivada = 'archivada';

    public function label(): string
    {
        return match($this) {
            self::Borrador => 'Borrador',
            self::Activa => 'Activa',
            self::Cerrada => 'Cerrada',
            self::Archivada => 'Archivada',
        };
    }
}
