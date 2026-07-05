<?php

namespace App\Enums;

enum EstadoOrdenCompra: string
{
    case Recibida   = 'recibida';
    case EnProceso  = 'en_proceso';
    case Despachada = 'despachada';
    case Entregada  = 'entregada';
    case Anulada    = 'anulada';

    public function label(): string
    {
        return match($this) {
            self::Recibida   => 'Recibida',
            self::EnProceso  => 'En proceso',
            self::Despachada => 'Despachada',
            self::Entregada  => 'Entregada',
            self::Anulada    => 'Anulada',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Recibida   => 'bg-blue-100 text-blue-700',
            self::EnProceso  => 'bg-amber-100 text-amber-700',
            self::Despachada => 'bg-purple-100 text-purple-700',
            self::Entregada  => 'bg-green-100 text-green-700',
            self::Anulada    => 'bg-red-100 text-red-700',
        };
    }
}
