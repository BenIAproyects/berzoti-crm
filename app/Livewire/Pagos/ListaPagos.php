<?php

namespace App\Livewire\Pagos;

use App\Enums\MetodoPago;
use App\Models\Pago;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class ListaPagos extends Component
{
    use WithPagination;

    public string $busqueda      = '';
    public string $filtroMetodo  = '';
    public string $fechaDesde    = '';
    public string $fechaHasta    = '';

    protected $queryString = [
        'busqueda'     => ['except' => ''],
        'filtroMetodo' => ['except' => ''],
        'fechaDesde'   => ['except' => ''],
        'fechaHasta'   => ['except' => ''],
    ];

    public function updatingBusqueda(): void     { $this->resetPage(); }
    public function updatingFiltroMetodo(): void { $this->resetPage(); }
    public function updatingFechaDesde(): void   { $this->resetPage(); }
    public function updatingFechaHasta(): void   { $this->resetPage(); }

    public function limpiarFiltros(): void
    {
        $this->reset(['busqueda', 'filtroMetodo', 'fechaDesde', 'fechaHasta']);
        $this->resetPage();
    }

    public function render()
    {
        $pagos = Pago::with(['cliente', 'factura'])
            ->when($this->busqueda, function ($q) {
                $q->whereHas('cliente', fn($c) =>
                    $c->where('razon_social', 'like', "%{$this->busqueda}%")
                      ->orWhere('ruc', 'like', "%{$this->busqueda}%"))
                  ->orWhere('numero_operacion', 'like', "%{$this->busqueda}%")
                  ->orWhereHas('factura', fn($f) =>
                    $f->where('numero_factura', 'like', "%{$this->busqueda}%")
                      ->orWhere('codigo', 'like', "%{$this->busqueda}%"));
            })
            ->when($this->filtroMetodo, fn($q) => $q->where('metodo_pago', $this->filtroMetodo))
            ->when($this->fechaDesde, fn($q) => $q->whereDate('fecha_pago', '>=', $this->fechaDesde))
            ->when($this->fechaHasta, fn($q) => $q->whereDate('fecha_pago', '<=', $this->fechaHasta))
            ->orderByDesc('fecha_pago')
            ->paginate(20);

        return view('livewire.pagos.lista-pagos', [
            'pagos'   => $pagos,
            'metodos' => MetodoPago::cases(),
        ]);
    }
}
