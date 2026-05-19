<?php

namespace App\Services;

use App\Jobs\EnviarCorreoJob;
use App\Models\Campana;
use App\Models\Cliente;
use App\Models\CorreoEnviado;
use App\Models\PlantillaCorreo;

class CorreoService
{
    /**
     * Programa el envío individual de un correo a un cliente.
     * Retorna el registro creado.
     */
    public function programarEnvio(
        Cliente        $cliente,
        PlantillaCorreo $plantilla,
        ?Campana       $campana = null,
    ): CorreoEnviado {
        if (!$cliente->correo) {
            throw new \RuntimeException("El cliente {$cliente->razon_social} no tiene correo registrado.");
        }

        if (!$cliente->activo) {
            throw new \RuntimeException("El cliente {$cliente->razon_social} está inactivo.");
        }

        $registro = CorreoEnviado::create([
            'cliente_id'        => $cliente->id,
            'campana_id'        => $campana?->id,
            'plantilla_id'      => $plantilla->id,
            'usuario_id'        => auth()->id(),
            'destinatario'      => $cliente->correo,
            'asunto'            => $plantilla->renderizarAsunto($cliente),
            'cuerpo_renderizado' => $plantilla->renderizar($cliente),
            'estado_envio'      => 'pendiente',
        ]);

        EnviarCorreoJob::dispatch($registro->id);

        return $registro;
    }

    /**
     * Programa el envío masivo a todos los clientes activos de una campaña.
     * Retorna [enviados, omitidos].
     */
    public function programarEnvioMasivo(
        Campana        $campana,
        PlantillaCorreo $plantilla,
    ): array {
        if (!$campana->estaActiva()) {
            throw new \RuntimeException('Solo se pueden enviar correos desde campañas activas.');
        }

        $clientes = $campana->clientes()->activos()->whereNotNull('correo')->get();

        $enviados = 0;
        $omitidos = 0;

        foreach ($clientes as $cliente) {
            // Evitar reenvío si ya se le envió en esta campaña con esta plantilla
            $yaEnviado = CorreoEnviado::where('cliente_id', $cliente->id)
                ->where('campana_id', $campana->id)
                ->where('plantilla_id', $plantilla->id)
                ->where('estado_envio', 'enviado')
                ->exists();

            if ($yaEnviado) {
                $omitidos++;
                continue;
            }

            $registro = CorreoEnviado::create([
                'cliente_id'        => $cliente->id,
                'campana_id'        => $campana->id,
                'plantilla_id'      => $plantilla->id,
                'usuario_id'        => auth()->id(),
                'destinatario'      => $cliente->correo,
                'asunto'            => $plantilla->renderizarAsunto($cliente),
                'cuerpo_renderizado' => $plantilla->renderizar($cliente),
                'estado_envio'      => 'pendiente',
            ]);

            EnviarCorreoJob::dispatch($registro->id);
            $enviados++;
        }

        return [$enviados, $omitidos];
    }
}
