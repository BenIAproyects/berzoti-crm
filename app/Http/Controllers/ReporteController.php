<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\CorreoEnviado;
use App\Models\Cotizacion;
use App\Models\Factura;
use App\Models\GuiaRemision;
use App\Models\OrdenCompra;
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
        $tab           = $request->input('tab', 'clientes');
        $filtroEstado  = $request->input('estado', '');
        $filtroUsuario = $request->input('usuario', '');
        $desde         = $request->input('desde', '');
        $hasta         = $request->input('hasta', '');
        $filename      = 'reporte_' . $tab . '_' . now()->format('Ymd_His') . '.csv';

        return Response::stream(function () use ($tab, $filtroEstado, $filtroUsuario, $desde, $hasta) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            match ($tab) {
                'clientes'     => $this->exportarClientes($handle, $filtroEstado, $filtroUsuario),
                'seguimientos' => $this->exportarSeguimientos($handle, $filtroUsuario),
                'tareas'       => $this->exportarTareas($handle, $filtroEstado, $filtroUsuario),
                'cotizaciones' => $this->exportarCotizaciones($handle, $filtroEstado, $filtroUsuario),
                'correos'      => $this->exportarCorreos($handle),
                'ordenes'      => $this->exportarOrdenes($handle, $filtroUsuario, $desde, $hasta),
                'cobranzas'    => $this->exportarCobranzas($handle, $filtroUsuario, $desde, $hasta),
                'guias'        => $this->exportarGuias($handle, $filtroUsuario, $desde, $hasta),
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

    private function exportarOrdenes($handle, string $filtroUsuario, string $desde, string $hasta): void
    {
        fputcsv($handle, ['Código', 'N° OC', 'Cliente', 'RUC', 'Fecha OC', 'Estado', 'Subtotal', 'IGV', 'Total', 'Vendedor', 'Campaña']);
        OrdenCompra::with(['cliente', 'vendedor', 'campana'])
            ->whereNotIn('estado', ['anulada'])
            ->when($filtroUsuario, fn($q) => $q->where('vendedor_id', $filtroUsuario))
            ->when($desde,         fn($q) => $q->whereDate('fecha_oc', '>=', $desde))
            ->when($hasta,         fn($q) => $q->whereDate('fecha_oc', '<=', $hasta))
            ->orderByDesc('fecha_oc')
            ->chunk(200, function ($rows) use ($handle) {
                foreach ($rows as $r) {
                    fputcsv($handle, [
                        $r->codigo,
                        $r->numero_oc ?? '',
                        $r->cliente?->razon_social ?? '',
                        $r->cliente?->ruc ?? '',
                        $r->fecha_oc->format('d/m/Y'),
                        $r->estado->label(),
                        number_format($r->subtotal, 2),
                        number_format($r->igv, 2),
                        number_format($r->total, 2),
                        $r->vendedor?->name ?? '',
                        $r->campana?->nombre ?? '',
                    ]);
                }
            });
    }

    private function exportarCobranzas($handle, string $filtroUsuario, string $desde, string $hasta): void
    {
        fputcsv($handle, ['Código', 'N° Factura', 'Cliente', 'RUC', 'F. Emisión', 'F. Vencimiento', 'Total', 'Cobrado', 'Saldo', 'Estado', 'Vendedor']);
        Factura::with(['cliente', 'vendedor'])
            ->whereNotIn('estado_pago', ['pagada', 'anulada'])
            ->when($filtroUsuario, fn($q) => $q->where('vendedor_id', $filtroUsuario))
            ->when($desde,         fn($q) => $q->whereDate('fecha_emision', '>=', $desde))
            ->when($hasta,         fn($q) => $q->whereDate('fecha_emision', '<=', $hasta))
            ->orderBy('fecha_vencimiento')
            ->chunk(200, function ($rows) use ($handle) {
                foreach ($rows as $r) {
                    fputcsv($handle, [
                        $r->codigo,
                        $r->numero_factura ?? '',
                        $r->cliente?->razon_social ?? '',
                        $r->cliente?->ruc ?? '',
                        $r->fecha_emision->format('d/m/Y'),
                        $r->fecha_vencimiento?->format('d/m/Y') ?? '',
                        number_format($r->total, 2),
                        number_format($r->monto_pagado, 2),
                        number_format($r->saldo_pendiente, 2),
                        $r->estado_pago->label(),
                        $r->vendedor?->name ?? '',
                    ]);
                }
            });
    }

    private function exportarGuias($handle, string $filtroUsuario, string $desde, string $hasta): void
    {
        fputcsv($handle, ['Código', 'N° Guía', 'Cliente', 'OC', 'F. Emisión', 'F. Entrega', 'Estado', 'Dirección', 'Vendedor']);
        GuiaRemision::with(['cliente', 'vendedor', 'ordenCompra'])
            ->pendientesEntrega()
            ->when($filtroUsuario, fn($q) => $q->where('vendedor_id', $filtroUsuario))
            ->when($desde,         fn($q) => $q->whereDate('fecha_emision', '>=', $desde))
            ->when($hasta,         fn($q) => $q->whereDate('fecha_emision', '<=', $hasta))
            ->orderBy('fecha_emision')
            ->chunk(200, function ($rows) use ($handle) {
                foreach ($rows as $r) {
                    fputcsv($handle, [
                        $r->codigo,
                        $r->numero_guia ?? '',
                        $r->cliente?->razon_social ?? '',
                        $r->ordenCompra?->codigo ?? '',
                        $r->fecha_emision->format('d/m/Y'),
                        $r->fecha_entrega?->format('d/m/Y') ?? '',
                        $r->estado_entrega->label(),
                        $r->direccion_entrega,
                        $r->vendedor?->name ?? '',
                    ]);
                }
            });
    }
}
