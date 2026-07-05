<?php

namespace App\Enums;

enum EstadoGuiaRemision: string
{
    case Pendiente  = 'pendiente';
    case EnTransito = 'en_transito';
    case Entregada  = 'entregada';
    case Observada  = 'observada';
    case Anulada    = 'anulada';

    public function label(): string
    {
        return match($this) {
            self::Pendiente  => 'Pendiente',
            self::EnTransito => 'En tránsito',
            self::Entregada  => 'Entregada',
            self::Observada  => 'Observada',
            self::Anulada    => 'Anulada',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Pendiente  => 'bg-amber-100 text-amber-700',
            self::EnTransito => 'bg-blue-100 text-blue-700',
            self::Entregada  => 'bg-green-100 text-green-700',
            self::Observada  => 'bg-orange-100 text-orange-700',
            self::Anulada    => 'bg-gray-100 text-gray-500',
        };
    }
}
