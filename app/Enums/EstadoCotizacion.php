<?php

namespace App\Enums;

enum EstadoCotizacion: string
{
    case Borrador = 'borrador';
    case Enviada = 'enviada';
    case Aprobada = 'aprobada';
    case Rechazada = 'rechazada';
    case Vencida = 'vencida';

    public function label(): string
    {
        return match($this) {
            self::Borrador  => 'Borrador',
            self::Enviada   => 'Enviada',
            self::Aprobada  => 'Aprobada',
            self::Rechazada => 'Rechazada',
            self::Vencida   => 'Vencida',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Borrador  => 'bg-gray-100 text-gray-600',
            self::Enviada   => 'bg-blue-100 text-blue-700',
            self::Aprobada  => 'bg-green-100 text-green-700',
            self::Rechazada => 'bg-red-100 text-red-700',
            self::Vencida   => 'bg-orange-100 text-orange-700',
        };
    }
}
