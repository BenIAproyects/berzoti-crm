<?php

namespace App\Livewire\Cotizaciones;

use App\Enums\EstadoCotizacion;
use App\Models\Cliente;
use App\Models\Cotizacion;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class ListaCotizaciones extends Component
{
    use WithPagination;

    public Cliente $cliente;

    #[On('cotizacion-guardada')]
    public function refrescar(): void {}

    public function editar(int $id): void
    {
        $this->dispatch('editar-cotizacion', id: $id);
    }

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
