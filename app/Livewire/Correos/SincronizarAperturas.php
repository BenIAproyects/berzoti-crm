<?php

namespace App\Livewire\Correos;

use App\Models\CorreoEnviado;
use Illuminate\Support\Facades\Http;
use Livewire\Component;

class SincronizarAperturas extends Component
{
    public string $mensaje = '';

    public function sincronizar(): void
    {
        $apiKey = env('BREVO_API_KEY');

        try {
            $sincronizados = 0;
            $offset = 0;
            $limit  = 100;

            do {
                $response = Http::withHeaders([
                    'api-key' => $apiKey,
                    'accept'  => 'application/json',
                ])->get('https://api.brevo.com/v3/smtp/statistics/events', [
                    'event'     => 'opened',
                    'limit'     => $limit,
                    'offset'    => $offset,
                    'startDate' => now()->subDays(60)->format('Y-m-d'),
                    'endDate'   => now()->format('Y-m-d'),
                ]);

                if (!$response->successful()) {
                    $this->mensaje = 'Error Brevo: ' . $response->body();
                    return;
                }

                $eventos = $response->json('events') ?? [];

                foreach ($eventos as $evento) {
                    $email   = $evento['email']   ?? '';
                    $asunto  = $evento['subject']  ?? '';
                    $fecha   = $evento['date']     ?? now();

                    if (!$email) {
                        continue;
                    }

                    // Buscar por email + asunto (sin importar cuántas veces se abrió)
                    $correos = CorreoEnviado::where('destinatario', $email)
                        ->where('asunto', $asunto)
                        ->where('estado_envio', 'enviado')
                        ->get();

                    foreach ($correos as $correo) {
                        if (!$correo->abierto) {
                            $correo->update([
                                'abierto'       => true,
                                'abierto_en'    => $fecha,
                                'veces_abierto' => 1,
                            ]);
                            $sincronizados++;
                        } else {
                            // Ya estaba marcado como abierto, solo incrementar contador
                            $correo->increment('veces_abierto');
                        }
                    }
                }

                $offset += $limit;
            } while (count($eventos) === $limit);

            $this->mensaje = $sincronizados > 0
                ? "{$sincronizados} apertura(s) nuevas encontradas."
                : 'Sin nuevas aperturas.';

            $this->dispatch('aperturas-sincronizadas');

        } catch (\Throwable $e) {
            $this->mensaje = 'Error: ' . $e->getMessage();
        }
    }

    public function render()
    {
        return view('livewire.correos.sincronizar-aperturas');
    }
}
