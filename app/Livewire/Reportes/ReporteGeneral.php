<?php

namespace App\Livewire\Reportes;

use App\Models\Cliente;
use App\Models\Cotizacion;
use App\Models\CorreoEnviado;
use App\Models\Factura;
use App\Models\GuiaRemision;
use App\Models\OrdenCompra;
use App\Models\Seguimiento;
use App\Models\Tarea;
use App\Models\User;
use Illuminate\Support\Collection;
use Livewire\Component;
use Livewire\WithPagination;

class ReporteGeneral extends Component
{
    use WithPagination;

    public string $tab            = 'ventas';
    public string $filtroEstado   = '';
    public string $filtroUsuario  = '';
    public int    $diasSinContacto = 7;
    public string $fechaDesde     = '';
    public string $fechaHasta     = '';
    public string $agruparPor     = 'cliente';

    private const LEGACY_TABS = ['clientes', 'seguimientos', 'tareas', 'cotizaciones', 'correos', 'sin_contacto'];

    public function updatingTab(): void         { $this->resetPage(); }
    public function updatingFiltroEstado(): void { $this->resetPage(); }
    public function updatingFiltroUsuario(): void { $this->resetPage(); }

    public function render(): \Illuminate\View\View
    {
        $datos = in_array($this->tab, self::LEGACY_TABS)
            ? match ($this->tab) {
                'clientes', 'sin_contacto' => $this->queryClientes()->paginate(15),
                'seguimientos'             => $this->querySeguimientos()->paginate(15),
                'tareas'                   => $this->queryTareas()->paginate(15),
                'cotizaciones'             => $this->queryCotizaciones()->paginate(15),
                'correos'                  => $this->queryCorreos()->paginate(15),
                default                    => collect(),
            }
            : null;

        return view('livewire.reportes.reporte-general', [
            'datos'           => $datos,
            'kpis'            => $this->calcularKpis(),
            'vendedores'      => User::orderBy('name')->get(),
            'datosVentas'     => $this->tab === 'ventas'     ? $this->calcularVentas()     : collect(),
            'ordenesAbiertas' => $this->tab === 'ordenes'    ? $this->getOrdenesAbiertas() : collect(),
            'cobranzas'       => $this->tab === 'cobranzas'  ? $this->getCobranzas()       : collect(),
            'guiasPendientes' => $this->tab === 'guias'      ? $this->getGuiasPendientes() : collect(),
            'conversion'      => $this->tab === 'conversion' ? $this->calcularConversion() : [],
        ]);
    }

    // ─── Legacy queries ───────────────────────────────────────────────────────

    private function queryClientes()
    {
        return Cliente::activos()->with(['vendedor'])
            ->when($this->filtroEstado,   fn($q) => $q->where('estado_comercial', $this->filtroEstado))
            ->when($this->filtroUsuario,  fn($q) => $q->where('vendedor_asignado_id', $this->filtroUsuario))
            ->when($this->tab === 'sin_contacto', fn($q) =>
                $q->where(fn($q2) => $q2->whereNull('fecha_ultimo_contacto')
                    ->orWhere('fecha_ultimo_contacto', '<', now()->subDays($this->diasSinContacto)))
            )
            ->orderByDesc('created_at');
    }

    private function querySeguimientos()
    {
        return Seguimiento::with(['cliente', 'usuario'])
            ->when($this->filtroUsuario, fn($q) => $q->where('usuario_id', $this->filtroUsuario))
            ->orderByDesc('fecha_hora');
    }

    private function queryTareas()
    {
        return Tarea::with(['cliente', 'usuario'])
            ->when($this->filtroEstado,  fn($q) => $q->where('estado', $this->filtroEstado))
            ->when($this->filtroUsuario, fn($q) => $q->where('usuario_id', $this->filtroUsuario))
            ->orderByRaw("CASE WHEN estado='pendiente' AND fecha_vencimiento < CURDATE() THEN 0 ELSE 1 END")
            ->orderBy('fecha_vencimiento');
    }

    private function queryCotizaciones()
    {
        return Cotizacion::with(['cliente', 'usuario'])
            ->when($this->filtroEstado,  fn($q) => $q->where('estado', $this->filtroEstado))
            ->when($this->filtroUsuario, fn($q) => $q->where('usuario_id', $this->filtroUsuario))
            ->orderByDesc('fecha');
    }

    private function queryCorreos()
    {
        return CorreoEnviado::orderByDesc('created_at');
    }

    // ─── Commercial KPIs ──────────────────────────────────────────────────────

    private function calcularKpis(): array
    {
        $qOC = OrdenCompra::query()
            ->when($this->fechaDesde,    fn($q) => $q->whereDate('fecha_oc', '>=', $this->fechaDesde))
            ->when($this->fechaHasta,    fn($q) => $q->whereDate('fecha_oc', '<=', $this->fechaHasta))
            ->when($this->filtroUsuario, fn($q) => $q->where('vendedor_id', $this->filtroUsuario));

        $qFac = Factura::query()
            ->when($this->fechaDesde,    fn($q) => $q->whereDate('fecha_emision', '>=', $this->fechaDesde))
            ->when($this->fechaHasta,    fn($q) => $q->whereDate('fecha_emision', '<=', $this->fechaHasta))
            ->when($this->filtroUsuario, fn($q) => $q->where('vendedor_id', $this->filtroUsuario));

        return [
            'ordenes_count' => (clone $qOC)->count(),
            'ordenes_total' => (float) (clone $qOC)->whereNotIn('estado', ['anulada'])->sum('total'),
            'facturado'     => (float) (clone $qFac)->sum('total'),
            'cobrado'       => (float) (clone $qFac)->sum('monto_pagado'),
            'pendiente'     => (float) (clone $qFac)->whereNotIn('estado_pago', ['pagada', 'anulada'])->sum('saldo_pendiente'),
            'vencido'       => (float) (clone $qFac)->vencidas()->sum('saldo_pendiente'),
        ];
    }

    // ─── Ventas ───────────────────────────────────────────────────────────────

    private function calcularVentas(): Collection
    {
        return match ($this->agruparPor) {
            'cliente'  => $this->ventasPorCliente(),
            'vendedor' => $this->ventasPorVendedor(),
            'zona'     => $this->ventasPorCampo('zona'),
            'segmento' => $this->ventasPorCampo('segmento'),
            'campana'  => $this->ventasPorCampana(),
            default    => collect(),
        };
    }

    private function ventasPorCliente(): Collection
    {
        $rows = OrdenCompra::with('cliente')
            ->selectRaw('cliente_id, COUNT(*) as ocs, SUM(total) as total_oc')
            ->when($this->fechaDesde,    fn($q) => $q->whereDate('fecha_oc', '>=', $this->fechaDesde))
            ->when($this->fechaHasta,    fn($q) => $q->whereDate('fecha_oc', '<=', $this->fechaHasta))
            ->when($this->filtroUsuario, fn($q) => $q->where('vendedor_id', $this->filtroUsuario))
            ->groupBy('cliente_id')
            ->orderByDesc('total_oc')
            ->get();

        $ids      = $rows->pluck('cliente_id');
        $facStats = Factura::whereIn('cliente_id', $ids)
            ->selectRaw('cliente_id, SUM(total) as facturado, SUM(monto_pagado) as cobrado, SUM(saldo_pendiente) as pendiente')
            ->groupBy('cliente_id')
            ->get()
            ->keyBy('cliente_id');

        return $rows->map(function ($r) use ($facStats) {
            $fs           = $facStats->get($r->cliente_id);
            $r->facturado = $fs ? (float) $fs->facturado : 0;
            $r->cobrado   = $fs ? (float) $fs->cobrado   : 0;
            $r->pendiente = $fs ? (float) $fs->pendiente : 0;
            return $r;
        });
    }

    private function ventasPorVendedor(): Collection
    {
        $rows = OrdenCompra::with('vendedor')
            ->selectRaw('vendedor_id, COUNT(*) as ocs, SUM(total) as total_oc')
            ->when($this->fechaDesde, fn($q) => $q->whereDate('fecha_oc', '>=', $this->fechaDesde))
            ->when($this->fechaHasta, fn($q) => $q->whereDate('fecha_oc', '<=', $this->fechaHasta))
            ->groupBy('vendedor_id')
            ->orderByDesc('total_oc')
            ->get();

        $ids      = $rows->pluck('vendedor_id');
        $facStats = Factura::whereIn('vendedor_id', $ids)
            ->selectRaw('vendedor_id, SUM(total) as facturado, SUM(monto_pagado) as cobrado')
            ->groupBy('vendedor_id')
            ->get()
            ->keyBy('vendedor_id');

        return $rows->map(function ($r) use ($facStats) {
            $fs           = $facStats->get($r->vendedor_id);
            $r->facturado = $fs ? (float) $fs->facturado : 0;
            $r->cobrado   = $fs ? (float) $fs->cobrado   : 0;
            return $r;
        });
    }

    private function ventasPorCampo(string $campo): Collection
    {
        return OrdenCompra::join('clientes', 'ordenes_compra.cliente_id', '=', 'clientes.id')
            ->selectRaw("clientes.{$campo} as grupo, COUNT(ordenes_compra.id) as ocs, SUM(ordenes_compra.total) as total_oc, COUNT(DISTINCT ordenes_compra.cliente_id) as clientes_count")
            ->when($this->fechaDesde,    fn($q) => $q->whereDate('ordenes_compra.fecha_oc', '>=', $this->fechaDesde))
            ->when($this->fechaHasta,    fn($q) => $q->whereDate('ordenes_compra.fecha_oc', '<=', $this->fechaHasta))
            ->when($this->filtroUsuario, fn($q) => $q->where('ordenes_compra.vendedor_id', $this->filtroUsuario))
            ->groupBy("clientes.{$campo}")
            ->orderByDesc('total_oc')
            ->get();
    }

    private function ventasPorCampana(): Collection
    {
        return OrdenCompra::with('campana')
            ->selectRaw('campana_id, COUNT(*) as ocs, SUM(total) as total_oc, COUNT(DISTINCT cliente_id) as clientes_count')
            ->when($this->fechaDesde,    fn($q) => $q->whereDate('fecha_oc', '>=', $this->fechaDesde))
            ->when($this->fechaHasta,    fn($q) => $q->whereDate('fecha_oc', '<=', $this->fechaHasta))
            ->when($this->filtroUsuario, fn($q) => $q->where('vendedor_id', $this->filtroUsuario))
            ->groupBy('campana_id')
            ->orderByDesc('total_oc')
            ->get();
    }

    // ─── OCs abiertas ─────────────────────────────────────────────────────────

    private function getOrdenesAbiertas(): Collection
    {
        return OrdenCompra::with(['cliente', 'vendedor'])
            ->whereNotIn('estado', ['entregada', 'anulada'])
            ->when($this->fechaDesde,    fn($q) => $q->whereDate('fecha_oc', '>=', $this->fechaDesde))
            ->when($this->fechaHasta,    fn($q) => $q->whereDate('fecha_oc', '<=', $this->fechaHasta))
            ->when($this->filtroUsuario, fn($q) => $q->where('vendedor_id', $this->filtroUsuario))
            ->orderByDesc('fecha_oc')
            ->get();
    }

    // ─── Cobranzas ────────────────────────────────────────────────────────────

    private function getCobranzas(): Collection
    {
        return Factura::with(['cliente', 'vendedor'])
            ->whereNotIn('estado_pago', ['pagada', 'anulada'])
            ->when($this->fechaDesde,    fn($q) => $q->whereDate('fecha_emision', '>=', $this->fechaDesde))
            ->when($this->fechaHasta,    fn($q) => $q->whereDate('fecha_emision', '<=', $this->fechaHasta))
            ->when($this->filtroUsuario, fn($q) => $q->where('vendedor_id', $this->filtroUsuario))
            ->orderBy('fecha_vencimiento')
            ->get();
    }

    // ─── Guías pendientes ─────────────────────────────────────────────────────

    private function getGuiasPendientes(): Collection
    {
        return GuiaRemision::with(['cliente', 'vendedor', 'ordenCompra'])
            ->pendientesEntrega()
            ->when($this->fechaDesde,    fn($q) => $q->whereDate('fecha_emision', '>=', $this->fechaDesde))
            ->when($this->fechaHasta,    fn($q) => $q->whereDate('fecha_emision', '<=', $this->fechaHasta))
            ->when($this->filtroUsuario, fn($q) => $q->where('vendedor_id', $this->filtroUsuario))
            ->orderBy('fecha_emision')
            ->get();
    }

    // ─── Conversión ───────────────────────────────────────────────────────────

    private function calcularConversion(): array
    {
        $qCot = Cotizacion::query()
            ->when($this->fechaDesde,    fn($q) => $q->whereDate('fecha', '>=', $this->fechaDesde))
            ->when($this->fechaHasta,    fn($q) => $q->whereDate('fecha', '<=', $this->fechaHasta))
            ->when($this->filtroUsuario, fn($q) => $q->where('usuario_id', $this->filtroUsuario));

        $totalCot    = (clone $qCot)->count();
        $convertidas = (clone $qCot)->where('convertida_a_oc', true)->count();

        $qOC = OrdenCompra::query()
            ->when($this->fechaDesde,    fn($q) => $q->whereDate('fecha_oc', '>=', $this->fechaDesde))
            ->when($this->fechaHasta,    fn($q) => $q->whereDate('fecha_oc', '<=', $this->fechaHasta))
            ->when($this->filtroUsuario, fn($q) => $q->where('vendedor_id', $this->filtroUsuario));

        $totalOC   = (clone $qOC)->count();
        $ocConFact = (clone $qOC)->whereHas('facturas')->count();

        return [
            'cotizaciones_count'  => $totalCot,
            'cotizaciones_monto'  => (float) (clone $qCot)->sum('monto_total'),
            'convertidas_oc'      => $convertidas,
            'tasa_cot_oc'         => $totalCot > 0 ? round(($convertidas / $totalCot) * 100, 1) : 0,
            'ordenes_count'       => $totalOC,
            'ordenes_monto'       => (float) (clone $qOC)->sum('total'),
            'ordenes_con_factura' => $ocConFact,
            'tasa_oc_fac'         => $totalOC > 0 ? round(($ocConFact / $totalOC) * 100, 1) : 0,
        ];
    }
}
