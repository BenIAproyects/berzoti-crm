<?php

namespace App\Http\Controllers;

use App\Models\CorreoEnviado;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function brevo(Request $request): \Illuminate\Http\JsonResponse
    {
        $payload = $request->all();

        // Brevo puede enviar un array de eventos o un objeto único
        $eventos = isset($payload[0]) ? $payload : [$payload];

        foreach ($eventos as $evento) {
            $tipo      = $evento['event'] ?? '';
            $messageId = $evento['message-id'] ?? '';

            if ($tipo === 'opened' && $messageId) {
                $this->registrarApertura($messageId);
            }
        }

        return response()->json(['ok' => true]);
    }

    private function registrarApertura(string $messageId): void
    {
        // Formato: <correo-123@berzoti-crm>
        $messageId = trim($messageId, '<>');

        if (preg_match('/^correo-(\d+)@berzoti-crm$/', $messageId, $matches)) {
            $correo = CorreoEnviado::find((int) $matches[1]);
        } else {
            // Fallback: buscar por mensaje_id guardado
            $correo = CorreoEnviado::where('mensaje_id', $messageId)->first();
        }

        if (!$correo) {
            return;
        }

        $correo->increment('veces_abierto');

        if (!$correo->abierto) {
            $correo->update([
                'abierto'    => true,
                'abierto_en' => now(),
            ]);
        }
    }
}
