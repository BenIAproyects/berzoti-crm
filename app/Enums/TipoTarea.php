<?php

namespace App\Enums;

enum TipoTarea: string
{
    case Llamar = 'llamar';
    case ReenviarCorreo = 'reenviar_correo';
    case EnviarCotizacion = 'enviar_cotizacion';
    case ConfirmarReunion = 'confirmar_reunion';
    case CerrarNegociacion = 'cerrar_negociacion';
    case Visitar = 'visitar';
    case EnviarCorreo = 'enviar_correo';
    case Seguimiento = 'seguimiento';
    case Otro = 'otro';

    public function label(): string
    {
        return match($this) {
            self::Llamar           => 'Llamar',
            self::ReenviarCorreo   => 'Reenviar correo',
            self::EnviarCotizacion => 'Enviar cotización',
            self::ConfirmarReunion => 'Confirmar reunión',
            self::CerrarNegociacion => 'Cerrar negociación',
            self::Visitar          => 'Visitar',
            self::EnviarCorreo     => 'Enviar correo',
            self::Seguimiento      => 'Seguimiento',
            self::Otro             => 'Otro',
        };
    }
}
