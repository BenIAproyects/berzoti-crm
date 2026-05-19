<?php

namespace App\Jobs;

use App\Mail\CorreoCampana;
use App\Models\CorreoEnviado;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class EnviarCorreoJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(
        public readonly int $correoEnviadoId
    ) {}

    public function handle(): void
    {
        $registro = CorreoEnviado::find($this->correoEnviadoId);

        if (!$registro || $registro->estado_envio === 'enviado') {
            return;
        }

        try {
            Mail::to($registro->destinatario)
                ->send(new CorreoCampana(
                    asunto:     $registro->asunto,
                    cuerpoHtml: $registro->cuerpo_renderizado,
                    correoId:   $registro->id,
                ));

            $registro->update([
                'estado_envio' => 'enviado',
                'enviado_en'   => now(),
                'mensaje_id'   => 'correo-' . $registro->id . '@berzoti-crm',
                'error_mensaje' => null,
            ]);
        } catch (\Throwable $e) {
            $registro->update([
                'estado_envio'  => 'fallido',
                'error_mensaje' => $e->getMessage(),
            ]);
        }
    }

    public function failed(\Throwable $exception): void
    {
        CorreoEnviado::where('id', $this->correoEnviadoId)->update([
            'estado_envio'  => 'fallido',
            'error_mensaje' => $exception->getMessage(),
        ]);
    }
}
