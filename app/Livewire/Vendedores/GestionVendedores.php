<?php

namespace App\Livewire\Vendedores;

use App\Enums\EstadoComercial;
use App\Enums\TipoCliente;
use App\Models\Cliente;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;

class GestionVendedores extends Component
{
    use WithPagination;

    public ?int $vendedorSeleccionadoId = null;
    public string $tab = 'asignados';
    public string $busqueda = '';
    public string $filtroTipo = '';
    public string $filtroEstadoRendimiento = '';
    public string $mensaje = '';

    public function updatedBusqueda(): void { $this->resetPage(); }
    public function updatedFiltroTipo(): void { $this->resetPage(); }
    public function updatedFiltroEstadoRendimiento(): void { $this->resetPage(); }
    public function updatedTab(): void
    {
        $this->resetPage();
        $this->busqueda = '';
        $this->filtroTipo = '';
        $this->filtroEstadoRendimiento = '';
    }

    public function seleccionarVendedor(int $id): void
    {
        $this->vendedorSeleccionadoId = ($this->vendedorSeleccionadoId === $id) ? null : $id;
        $this->tab = 'asignados';
        $this->busqueda = '';
        $this->filtroTipo = '';
        $this->resetPage();
    }

    public function asignar(int $clienteId): void
    {
        Cliente::where('id', $clienteId)->update(['vendedor_asignado_id' => $this->vendedorSeleccionadoId]);
        $this->mensaje = 'Cliente asignado correctamente.';
        $this->resetPage();
    }

    public function quitar(int $clienteId): void
    {
        Cliente::where('id', $clienteId)->update(['vendedor_asignado_id' => null]);
        $this->mensaje = 'Cliente desasignado.';
        $this->resetPage();
    }

    public function render()
    {
        $vendedores = User::role('vendedor')
            ->withCount(['clientes as total_clientes'])
            ->orderBy('name')
            ->get();

        $vendedorSeleccionado = $this->vendedorSeleccionadoId
            ? $vendedores->firstWhere('id', $this->vendedorSeleccionadoId)
            : null;

        $clientes  = null;
        $kpis      = null;
        $estados   = EstadoComercial::cases();

        if ($this->vendedorSeleccionadoId) {
            if ($this->tab === 'rendimiento') {
                $clientes = $this->queryRendimiento()->paginate(20);
                $kpis     = $this->calcularKpis();
            } else {
                $query = Cliente::activos()
                    ->when($this->busqueda, fn($q) => $q->buscar($this->busqueda))
                    ->when($this->filtroTipo, fn($q) => $q->where('tipo_cliente', $this->filtroTipo));

                if ($this->tab === 'asignados') {
                    $query->where('vendedor_asignado_id', $this->vendedorSeleccionadoId);
                } else {
                    $query->whereNull('vendedor_asignado_id');
                }

                $clientes = $query->orderBy('razon_social')->paginate(15);
            }
        }

        return view('livewire.vendedores.gestion-vendedores', [
            'vendedores'           => $vendedores,
            'vendedorSeleccionado' => $vendedorSeleccionado,
            'clientes'             => $clientes,
            'kpis'                 => $kpis,
            'tipos'                => TipoCliente::cases(),
            'estados'              => $estados,
            'totalAsignados'       => $this->vendedorSeleccionadoId
                ? Cliente::where('vendedor_asignado_id', $this->vendedorSeleccionadoId)->count()
                : 0,
        ]);
    }

    private function baseQueryVendedor(): Builder
    {
        return Cliente::activos()->where('vendedor_asignado_id', $this->vendedorSeleccionadoId);
    }

    private function queryRendimiento(): Builder
    {
        return $this->baseQueryVendedor()
            ->withCount([
                'cotizaciones as cotizaciones_enviadas' => fn($q) => $q->whereIn('estado', ['enviada', 'aprobada']),
            ])
            ->when($this->filtroEstadoRendimiento, fn($q) => $q->where('estado_comercial', $this->filtroEstadoRendimiento))
            ->orderByDesc('cantidad_compra')
            ->orderBy('razon_social');
    }

    private function calcularKpis(): array
    {
        $base = $this->baseQueryVendedor();

        return [
            'total'          => (clone $base)->count(),
            'contactados'    => (clone $base)->where('fecha_ultimo_contacto', '>=', now()->startOfWeek())->count(),
            'con_cotizacion' => (clone $base)->whereHas('cotizaciones', fn($q) => $q->whereIn('estado', ['enviada', 'aprobada']))->count(),
            'ganados'        => (clone $base)->where('estado_comercial', 'ganado')->count(),
        ];
    }
}
