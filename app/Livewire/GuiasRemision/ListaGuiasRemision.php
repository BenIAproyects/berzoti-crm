<?php

namespace App\Livewire\GuiasRemision;

use App\Enums\EstadoGuiaRemision;
use App\Models\GuiaRemision;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class ListaGuiasRemision extends Component
{
    use WithPagination;

    public string $busqueda          = '';
    public string $filtroEstado      = '';
    public string $filtroVendedor    = '';
    public string $fechaDesde        = '';
    public string $fechaHasta        = '';
    public bool   $soloPendientes    = false;

    protected $queryString = [
        'busqueda'       => ['except' => ''],
        'filtroEstado'   => ['except' => ''],
        'filtroVendedor' => ['except' => ''],
        'fechaDesde'     => ['except' => ''],
        'fechaHasta'     => ['except' => ''],
        'soloPendientes' => ['except' => false],
    ];

    public function updatingBusqueda(): void       { $this->resetPage(); }
    public function updatingFiltroEstado(): void   { $this->resetPage(); }
    public function updatingFiltroVendedor(): void { $this->resetPage(); }
    public function updatingFechaDesde(): void     { $this->resetPage(); }
    public function updatingFechaHasta(): void     { $this->resetPage(); }
    public function updatingSoloPendientes(): void { $this->resetPage(); }

    public function limpiarFiltros(): void
    {
        $this->reset(['busqueda', 'filtroEstado', 'filtroVendedor', 'fechaDesde', 'fechaHasta', 'soloPendientes']);
        $this->resetPage();
    }

    public function render()
    {
        $guias = GuiaRemision::with(['cliente', 'ordenCompra', 'factura', 'vendedor'])
            ->when($this->busqueda, function ($q) {
                $q->whereHas('cliente', fn($c) =>
                    $c->where('razon_social', 'like', "%{$this->busqueda}%")
                      ->orWhere('ruc', 'like', "%{$this->busqueda}%"))
                  ->orWhere('codigo', 'like', "%{$this->busqueda}%")
                  ->orWhere('numero_guia', 'like', "%{$this->busqueda}%");
            })
            ->when($this->filtroEstado, fn($q) => $q->where('estado_entrega', $this->filtroEstado))
            ->when($this->filtroVendedor, fn($q) => $q->where('vendedor_id', $this->filtroVendedor))
            ->when($this->fechaDesde, fn($q) => $q->whereDate('fecha_emision', '>=', $this->fechaDesde))
            ->when($this->fechaHasta, fn($q) => $q->whereDate('fecha_emision', '<=', $this->fechaHasta))
            ->when($this->soloPendientes, fn($q) => $q->pendientesEntrega())
            ->orderByDesc('fecha_emision')
            ->paginate(20);

        return view('livewire.guias-remision.lista-guias-remision', [
            'guias'      => $guias,
            'estados'    => EstadoGuiaRemision::cases(),
            'vendedores' => User::orderBy('name')->get(),
        ]);
    }
}
