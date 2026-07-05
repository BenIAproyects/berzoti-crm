<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Cotizacion;
use App\Models\CorreoEnviado;
use App\Models\Factura;
use App\Models\GuiaRemision;
use App\Models\OrdenCompra;
use App\Models\Seguimiento;
use App\Models\Tarea;
use App\Models\Campana;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user   = auth()->user();
        $esAdmin = $user->hasRole('administrador') || $user->hasRole('supervisor');

        // KPIs generales
        $totalClientes   = Cliente::activos()->count();
        $clientesNuevos  = Cliente::activos()->whereMonth('created_at', now()->month)->count();
        $tareasVencidas  = Tarea::vencidas()->count();
        $tareasPendientes = Tarea::pendientes()->count();

        // Correos
        $correosTotal   = CorreoEnviado::where('estado_envio', 'enviado')->count();
        $correosMes     = CorreoEnviado::where('estado_envio', 'enviado')
                            ->whereMonth('created_at', now()->month)->count();

        // Cotizaciones
        $cotizacionesEnviadas  = Cotizacion::where('estado', 'enviada')->count();
        $cotizacionesAprobadas = Cotizacion::where('estado', 'aprobada')->count();
        $cotizacionesRechazadas = Cotizacion::where('estado', 'rechazada')->count();
        $montoAprobado = Cotizacion::where('estado', 'aprobada')->sum('monto_total');

        // Seguimientos esta semana
        $seguimientosSemana = Seguimiento::whereBetween('fecha_hora', [now()->startOfWeek(), now()->endOfWeek()])->count();

        // Embudo: clientes por estado comercial
        $embudo = Cliente::activos()
            ->select('estado_comercial', DB::raw('count(*) as total'))
            ->groupBy('estado_comercial')
            ->pluck('total', 'estado_comercial');

        // Clientes sin seguimiento en los últimos 7 días
        $sinSeguimiento = Cliente::activos()
            ->where(function ($q) {
                $q->whereNull('fecha_ultimo_contacto')
                  ->orWhere('fecha_ultimo_contacto', '<', now()->subDays(7));
            })->count();

        // Campañas activas
        $campanasActivas = Campana::activas()->count();

        // KPIs comerciales (si el usuario tiene permisos)
        $kpiComercial = [];
        if ($user->can('ordenes.ver') || $user->can('facturas.ver')) {
            $ocQuery = OrdenCompra::whereNotIn('estado', ['anulada']);
            $facQuery = Factura::whereNotIn('estado_pago', ['anulada']);

            if (! $esAdmin) {
                $ocQuery  = $ocQuery->where('vendedor_id', $user->id);
                $facQuery = $facQuery->where('vendedor_id', $user->id);
            }

            $kpiComercial = [
                'ocs_abiertas'       => (clone $ocQuery)->whereNotIn('estado', ['entregada'])->count(),
                'ocs_total'          => (float) (clone $ocQuery)->whereNotIn('estado', ['entregada'])->sum('total'),
                'facturas_vencidas'  => (clone $facQuery)->vencidas()->count(),
                'monto_vencido'      => (float) (clone $facQuery)->vencidas()->sum('saldo_pendiente'),
                'saldo_cobrar'       => (float) (clone $facQuery)->whereNotIn('estado_pago', ['pagada'])->sum('saldo_pendiente'),
                'guias_pendientes'   => $user->can('guias.ver')
                    ? GuiaRemision::pendientesEntrega()->when(! $esAdmin, fn($q) => $q->where('vendedor_id', $user->id))->count()
                    : null,
            ];
        }

        // Top vendedores (solo admin/supervisor)
        $topVendedores = [];
        if ($esAdmin) {
            $topVendedores = Cliente::activos()
                ->select('vendedor_asignado_id', DB::raw('count(*) as total'))
                ->whereNotNull('vendedor_asignado_id')
                ->with('vendedor:id,name')
                ->groupBy('vendedor_asignado_id')
                ->orderByDesc('total')
                ->limit(5)
                ->get();
        }

        return view('dashboard', compact(
            'totalClientes', 'clientesNuevos', 'tareasVencidas', 'tareasPendientes',
            'correosTotal', 'correosMes', 'cotizacionesEnviadas', 'cotizacionesAprobadas',
            'cotizacionesRechazadas', 'montoAprobado', 'seguimientosSemana',
            'embudo', 'sinSeguimiento', 'campanasActivas', 'topVendedores', 'esAdmin',
            'kpiComercial'
        ));
    }
}
