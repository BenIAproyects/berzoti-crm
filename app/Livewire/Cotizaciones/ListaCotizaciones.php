<?php

namespace App\Livewire\Cotizaciones;

use App\Enums\EstadoCotizacion;
use App\Models\Cliente;
use App\Models\Cotizacion;
use Livewire\Component;
use Livewire\WithPagination;

class ListaCotizaciones extends Component
{
    use WithPagination;

    public Cliente $cliente;

    protected $listeners = ['cotizacion-guardada' => '$refresh'];

    public function mount(Cliente $cliente): void
    {
        $this->cliente = $cliente;
    }

    public function render()
    {
        $cotizaciones = Cotizacion::where('cliente_id', $this->cliente->id)
            ->with(['usuario', 'campana', 'items'])
            ->orderByDesc('fecha')
            ->paginate(10);

        return view('livewire.cotizaciones.lista-cotizaciones', [
            'cotizaciones' => $cotizaciones,
        ]);
    }
}
