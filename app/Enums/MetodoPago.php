<?php

namespace App\Enums;

enum MetodoPago: string
{
    case Efectivo      = 'efectivo';
    case Transferencia = 'transferencia';
    case Deposito      = 'deposito';
    case Cheque        = 'cheque';
    case Tarjeta       = 'tarjeta';
    case Otro          = 'otro';

    public function label(): string
    {
        return match($this) {
            self::Efectivo      => 'Efectivo',
            self::Transferencia => 'Transferencia bancaria',
            self::Deposito      => 'Depósito',
            self::Cheque        => 'Cheque',
            self::Tarjeta       => 'Tarjeta',
            self::Otro          => 'Otro',
        };
    }

    public function requiereBanco(): bool
    {
        return in_array($this, [self::Transferencia, self::Deposito, self::Cheque]);
    }
}
