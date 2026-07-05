<?php

namespace App\Livewire\Clientes;

use App\Models\Cliente;
use App\Models\Cotizacion;
use App\Models\Factura;
use App\Models\GuiaRemision;
use App\Models\OrdenCompra;
use App\Models\Pago;
use App\Models\Seguimiento;
use Illuminate\Support\Collection;
use Livewire\Component;

class HistorialComercial extends Component
{
    public Cliente $cliente;
    public string $filtroTipo = '';

    public function render()
    {
        return view('livewire.clientes.historial-comercial', [
            'resumen'  => $this->calcularResumen(),
            'eventos'  => $this->construirTimeline(),
        ]);
    }

    private function construirTimeline(): Collection
    {
        $eventos = collect();
        $id      = $this->cliente->id;

        if (! $this->filtroTipo || $this->filtroTipo === 'cotizaciones') {
            Cotizacion::with(['items', 'campana', 'usuario'])
                ->where('cliente_id', $id)
                ->get()
                ->each(fn($c) => $eventos->push(['tipo' => 'cotizacion', 'fecha' => $c->fecha, 'modelo' => $c]));
        }

        if (! $this->filtroTipo || $this->filtroTipo === 'ordenes') {
            OrdenCompra::with(['items', 'campana', 'vendedor'])
                ->where('cliente_id', $id)
                ->get()
                ->each(fn($oc) => $eventos->push(['tipo' => 'orden', 'fecha' => $oc->fecha_oc, 'modelo' => $oc]));
        }

        if (! $this->filtroTipo || $this->filtroTipo === 'facturas') {
            Factura::with(['ordenCompra', 'vendedor'])
                ->where('cliente_id', $id)
                ->get()
                ->each(fn($f) => $eventos->push(['tipo' => 'factura', 'fecha' => $f->fecha_emision, 'modelo' => $f]));
        }

        if (! $this->filtroTipo || $this->filtroTipo === 'guias') {
            GuiaRemision::with(['ordenCompra', 'factura', 'vendedor'])
                ->where('cliente_id', $id)
                ->get()
                ->each(fn($g) => $eventos->push(['tipo' => 'guia', 'fecha' => $g->fecha_emision, 'modelo' => $g]));
        }

        if (! $this->filtroTipo || $this->filtroTipo === 'pagos') {
            Pago::with(['factura'])
                ->where('cliente_id', $id)
                ->get()
                ->each(fn($p) => $eventos->push(['tipo' => 'pago', 'fecha' => $p->fecha_pago, 'modelo' => $p]));
        }

        if (! $this->filtroTipo || $this->filtroTipo === 'seguimientos') {
            Seguimiento::with(['usuario'])
                ->where('cliente_id', $id)
                ->get()
                ->each(fn($s) => $eventos->push(['tipo' => 'seguimiento', 'fecha' => $s->fecha_hora, 'modelo' => $s]));
        }

        return $eventos->sortByDesc('fecha')->values();
    }

    private function calcularResumen(): array
    {
        $id = $this->cliente->id;

        $cotizaciones = Cotizacion::where('cliente_id', $id);
        $ordenes      = OrdenCompra::where('cliente_id', $id);
        $facturas     = Factura::where('cliente_id', $id);
        $pagos        = Pago::where('cliente_id', $id);

        $montoFacturado = (float) $facturas->sum('total');
        $montoPagado    = (float) $pagos->sum('monto_pagado');
        $saldoPendiente = (float) Factura::where('cliente_id', $id)
            ->whereNotIn('estado_pago', ['pagada', 'anulada'])
            ->sum('saldo_pendiente');

        return [
            'cotizaciones_count'  => $cotizaciones->count(),
            'cotizaciones_monto'  => (float) $cotizaciones->sum('monto_total'),
            'ordenes_count'       => $ordenes->count(),
            'ordenes_monto'       => (float) $ordenes->sum('total'),
            'facturas_count'      => $facturas->count(),
            'monto_facturado'     => $montoFacturado,
            'monto_pagado'        => $montoPagado,
            'saldo_pendiente'     => $saldoPendiente,
            'porcentaje_cobrado'  => $montoFacturado > 0 ? round(($montoPagado / $montoFacturado) * 100, 1) : 0,
        ];
    }
}
