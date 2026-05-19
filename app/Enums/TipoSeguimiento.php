<?php

namespace App\Enums;

enum TipoSeguimiento: string
{
    case Llamada = 'llamada';
    case Correo = 'correo';
    case Reunion = 'reunion';
    case WhatsApp = 'whatsapp';
    case Visita = 'visita';
    case Cotizacion = 'cotizacion';
    case Observacion = 'observacion';

    public function label(): string
    {
        return match($this) {
            self::Llamada => 'Llamada',
            self::Correo => 'Correo',
            self::Reunion => 'Reunión',
            self::WhatsApp => 'WhatsApp',
            self::Visita => 'Visita',
            self::Cotizacion => 'Cotización',
            self::Observacion => 'Observación interna',
        };
    }
}
