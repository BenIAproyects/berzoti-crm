<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\CorreoEnviado;
use App\Models\Cotizacion;
use App\Models\Seguimiento;
use App\Models\Tarea;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ReporteController extends Controller
{
    public function index()
    {
        return view('reportes.index');
    }

    public function exportar(Request $request)
    {
        $tab          = $request->input('tab', 'clientes');
        $filtroEstado = $request->input('estado', '');
        $filtroUsuario = $request->input('usuario', '');
        $filename     = 'reporte_' . $tab . '_' . now()->format('Ymd_His') . '.csv';

        return Response::stream(function () use ($tab, $filtroEstado, $filtroUsuario) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            match ($tab) {
                'clientes'     => $this->exportarClientes($handle, $filtroEstado, $filtroUsuario),
                'seguimientos' => $this->exportarSeguimientos($handle, $filtroUsuario),
                'tareas'       => $this->exportarTareas($handle, $filtroEstado, $filtroUsuario),
                'cotizaciones' => $this->exportarCotizaciones($handle, $filtroEstado, $filtroUsuario),
                'correos'      => $this->exportarCorreos($handle),
                default        => null,
            };

            fclose($handle);
        }, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    private function exportarClientes($handle, string $filtroEstado, string $filtroUsuario): void
    {
        fputcsv($handle, ['Código', 'Razón Social', 'Contacto', 'Correo', 'Teléfono', 'Estado', 'Vendedor', 'Último contacto']);
        Cliente::activos()->with('vendedor')
            ->when($filtroEstado,  fn($q) => $q->where('estado_comercial', $filtroEstado))
            ->when($filtroUsuario, fn($q) => $q->where('vendedor_asignado_id', $filtroUsuario))
            ->orderByDesc('created_at')
            ->chunk(200, function ($rows) use ($handle) {
                foreach ($rows as $r) {
                    fputcsv($handle, [
                        $r->codigo, $r->razon_social, $r->contacto_principal,
                        $r->correo, $r->telefono,
                        $r->estado_comercial->label(),
                        $r->vendedor?->name ?? '',
                        $r->fecha_ultimo_contacto?->format('d/m/Y') ?? '',
                    ]);
                }
            });
    }

    private function exportarSeguimientos($handle, string $filtroUsuario): void
    {
        fputcsv($handle, ['Fecha', 'Cliente', 'Tipo', 'Detalle', 'Resultado', 'Próxima acción', 'Usuario']);
        Seguimiento::with(['cliente', 'usuario'])
            ->when($filtroUsuario, fn($q) => $q->where('usuario_id', $filtroUsuario))
            ->orderByDesc('fecha_hora')
            ->chunk(200, function ($rows) use ($handle) {
                foreach ($rows as $r) {
                    fputcsv($handle, [
                        $r->fecha_hora->format('d/m/Y H:i'),
                        $r->cliente->razon_social,
                        $r->tipo->label(),
                        $r->detalle,
                        $r->resultado ?? '',
                        $r->proxima_accion ?? '',
                        $r->usuario->name,
                    ]);
                }
            });
    }

    private function exportarTareas($handle, string $filtroEstado, string $filtroUsuario): void
    {
        fputcsv($handle, ['Título', 'Cliente', 'Vencimiento', 'Estado', 'Prioridad', 'Usuario']);
        Tarea::with(['cliente', 'usuario'])
            ->when($filtroEstado,  fn($q) => $q->where('estado', $filtroEstado))
            ->when($filtroUsuario, fn($q) => $q->where('usuario_id', $filtroUsuario))
            ->orderBy('fecha_vencimiento')
            ->chunk(200, function ($rows) use ($handle) {
                foreach ($rows as $r) {
                    fputcsv($handle, [
                        $r->titulo,
                        $r->cliente->razon_social,
                        $r->fecha_vencimiento->format('d/m/Y'),
                        $r->estado,
                        $r->prioridad,
                        $r->usuario->name,
                    ]);
                }
            });
    }

    private function exportarCotizaciones($handle, string $filtroEstado, string $filtroUsuario): void
    {
        fputcsv($handle, ['Código', 'Cliente', 'Fecha', 'Monto', 'Estado', 'F. Envío', 'F. Respuesta', 'Vendedor']);
        Cotizacion::with(['cliente', 'usuario'])
            ->when($filtroEstado,  fn($q) => $q->where('estado', $filtroEstado))
            ->when($filtroUsuario, fn($q) => $q->where('usuario_id', $filtroUsuario))
            ->orderByDesc('fecha')
            ->chunk(200, function ($rows) use ($handle) {
                foreach ($rows as $r) {
                    fputcsv($handle, [
                        $r->codigo,
                        $r->cliente->razon_social,
                        $r->fecha->format('d/m/Y'),
                        $r->monto_total,
                        $r->estado->label(),
                        $r->fecha_envio?->format('d/m/Y') ?? '',
                        $r->fecha_respuesta?->format('d/m/Y') ?? '',
                        $r->usuario->name,
                    ]);
                }
            });
    }

    private function exportarCorreos($handle): void
    {
        fputcsv($handle, ['Fecha', 'Destinatario', 'Asunto', 'Estado', 'Error']);
        CorreoEnviado::orderByDesc('created_at')
            ->chunk(200, function ($rows) use ($handle) {
                foreach ($rows as $r) {
                    fputcsv($handle, [
                        $r->created_at->format('d/m/Y H:i'),
                        $r->destinatario,
                        $r->asunto,
                        $r->estado_envio,
                        $r->error_mensaje ?? '',
                    ]);
                }
            });
    }
}
