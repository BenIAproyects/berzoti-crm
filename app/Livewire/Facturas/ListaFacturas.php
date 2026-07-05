<?php

namespace App\Livewire\Facturas;

use App\Enums\EstadoFactura;
use App\Models\Factura;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class ListaFacturas extends Component
{
    use WithPagination;

    public string $busqueda      = '';
    public string $filtroEstado  = '';
    public string $filtroVendedor = '';
    public string $fechaDesde    = '';
    public string $fechaHasta    = '';
    public bool   $soloVencidas  = false;

    protected $queryString = [
        'busqueda'       => ['except' => ''],
        'filtroEstado'   => ['except' => ''],
        'filtroVendedor' => ['except' => ''],
        'fechaDesde'     => ['except' => ''],
        'fechaHasta'     => ['except' => ''],
        'soloVencidas'   => ['except' => false],
    ];

    public function updatingBusqueda(): void       { $this->resetPage(); }
    public function updatingFiltroEstado(): void   { $this->resetPage(); }
    public function updatingFiltroVendedor(): void { $this->resetPage(); }
    public function updatingFechaDesde(): void     { $this->resetPage(); }
    public function updatingFechaHasta(): void     { $this->resetPage(); }
    public function updatingSoloVencidas(): void   { $this->resetPage(); }

    public function limpiarFiltros(): void
    {
        $this->reset(['busqueda', 'filtroEstado', 'filtroVendedor', 'fechaDesde', 'fechaHasta', 'soloVencidas']);
        $this->resetPage();
    }

    public function abrirPago(int $facturaId, int $clienteId): void
    {
        $this->dispatch('registrar-pago-factura', facturaId: $facturaId, clienteId: $clienteId);
    }

    public function render()
    {
        $facturas = Factura::with(['cliente', 'ordenCompra', 'vendedor'])
            ->when($this->busqueda, function ($q) {
                $q->whereHas('cliente', fn($c) =>
                    $c->where('razon_social', 'like', "%{$this->busqueda}%")
                      ->orWhere('ruc', 'like', "%{$this->busqueda}%"))
                  ->orWhere('codigo', 'like', "%{$this->busqueda}%")
                  ->orWhere('numero_factura', 'like', "%{$this->busqueda}%");
            })
            ->when($this->filtroEstado, fn($q) => $q->where('estado_pago', $this->filtroEstado))
            ->when($this->filtroVendedor, fn($q) => $q->where('vendedor_id', $this->filtroVendedor))
            ->when($this->fechaDesde, fn($q) => $q->whereDate('fecha_emision', '>=', $this->fechaDesde))
            ->when($this->fechaHasta, fn($q) => $q->whereDate('fecha_emision', '<=', $this->fechaHasta))
            ->when($this->soloVencidas, fn($q) => $q->vencidas())
            ->orderByDesc('fecha_emision')
            ->paginate(20);

        return view('livewire.facturas.lista-facturas', [
            'facturas'   => $facturas,
            'estados'    => EstadoFactura::cases(),
            'vendedores' => User::orderBy('name')->get(),
        ]);
    }
}
