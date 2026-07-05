<?php

namespace App\Enums;

enum EstadoFactura: string
{
    case Pendiente          = 'pendiente';
    case ParcialmentePagada = 'parcialmente_pagada';
    case Pagada             = 'pagada';
    case Vencida            = 'vencida';
    case Anulada            = 'anulada';

    public function label(): string
    {
        return match($this) {
            self::Pendiente          => 'Pendiente',
            self::ParcialmentePagada => 'Parcialmente pagada',
            self::Pagada             => 'Pagada',
            self::Vencida            => 'Vencida',
            self::Anulada            => 'Anulada',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Pendiente          => 'bg-amber-100 text-amber-700',
            self::ParcialmentePagada => 'bg-blue-100 text-blue-700',
            self::Pagada             => 'bg-green-100 text-green-700',
            self::Vencida            => 'bg-red-100 text-red-700',
            self::Anulada            => 'bg-gray-100 text-gray-500',
        };
    }
}
