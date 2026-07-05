<?php

namespace App\Livewire\OrdenesCompra;

use App\Enums\EstadoOrdenCompra;
use App\Models\OrdenCompra;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class ListaOrdenesCompra extends Component
{
    use WithPagination;

    public string $busqueda      = '';
    public string $filtroEstado  = '';
    public string $filtroVendedor = '';
    public string $fechaDesde    = '';
    public string $fechaHasta    = '';

    protected $queryString = [
        'busqueda'       => ['except' => ''],
        'filtroEstado'   => ['except' => ''],
        'filtroVendedor' => ['except' => ''],
        'fechaDesde'     => ['except' => ''],
        'fechaHasta'     => ['except' => ''],
    ];

    public function updatingBusqueda(): void      { $this->resetPage(); }
    public function updatingFiltroEstado(): void  { $this->resetPage(); }
    public function updatingFiltroVendedor(): void { $this->resetPage(); }
    public function updatingFechaDesde(): void    { $this->resetPage(); }
    public function updatingFechaHasta(): void    { $this->resetPage(); }

    public function limpiarFiltros(): void
    {
        $this->reset(['busqueda', 'filtroEstado', 'filtroVendedor', 'fechaDesde', 'fechaHasta']);
        $this->resetPage();
    }

    public function render()
    {
        $ordenes = OrdenCompra::with(['cliente', 'campana', 'vendedor'])
            ->when($this->busqueda, function ($q) {
                $q->whereHas('cliente', fn($c) => $c->where('razon_social', 'like', "%{$this->busqueda}%")
                    ->orWhere('ruc', 'like', "%{$this->busqueda}%"))
                  ->orWhere('codigo', 'like', "%{$this->busqueda}%")
                  ->orWhere('numero_oc', 'like', "%{$this->busqueda}%");
            })
            ->when($this->filtroEstado, fn($q) => $q->where('estado', $this->filtroEstado))
            ->when($this->filtroVendedor, fn($q) => $q->where('vendedor_id', $this->filtroVendedor))
            ->when($this->fechaDesde, fn($q) => $q->whereDate('fecha_oc', '>=', $this->fechaDesde))
            ->when($this->fechaHasta, fn($q) => $q->whereDate('fecha_oc', '<=', $this->fechaHasta))
            ->orderByDesc('fecha_oc')
            ->paginate(20);

        return view('livewire.ordenes-compra.lista-ordenes-compra', [
            'ordenes'   => $ordenes,
            'estados'   => EstadoOrdenCompra::cases(),
            'vendedores' => User::orderBy('name')->get(),
        ]);
    }
}
