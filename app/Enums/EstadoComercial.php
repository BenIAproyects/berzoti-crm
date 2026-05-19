<?php

namespace App\Enums;

enum EstadoComercial: string
{
    case Nuevo = 'nuevo';
    case ContactoPendiente = 'contacto_pendiente';
    case CorreoEnviado = 'correo_enviado';
    case EnSeguimiento = 'en_seguimiento';
    case Interesado = 'interesado';
    case CotizacionEnviada = 'cotizacion_enviada';
    case Negociacion = 'negociacion';
    case CerradoGanado = 'cerrado_ganado';
    case CerradoPerdido = 'cerrado_perdido';
    case NoResponde = 'no_responde';

    public function label(): string
    {
        return match($this) {
            self::Nuevo => 'Nuevo',
            self::ContactoPendiente => 'Contacto pendiente',
            self::CorreoEnviado => 'Correo enviado',
            self::EnSeguimiento => 'En seguimiento',
            self::Interesado => 'Interesado',
            self::CotizacionEnviada => 'Cotización enviada',
            self::Negociacion => 'Negociación',
            self::CerradoGanado => 'Cerrado ganado',
            self::CerradoPerdido => 'Cerrado perdido',
            self::NoResponde => 'No responde',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Nuevo => 'gray',
            self::ContactoPendiente => 'yellow',
            self::CorreoEnviado => 'blue',
            self::EnSeguimiento => 'indigo',
            self::Interesado => 'purple',
            self::CotizacionEnviada => 'orange',
            self::Negociacion => 'amber',
            self::CerradoGanado => 'green',
            self::CerradoPerdido => 'red',
            self::NoResponde => 'slate',
        };
    }
}
