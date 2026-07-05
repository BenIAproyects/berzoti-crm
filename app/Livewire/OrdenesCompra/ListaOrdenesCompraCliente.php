<?php

namespace App\Livewire\OrdenesCompra;

use App\Models\Cliente;
use App\Models\OrdenCompra;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class ListaOrdenesCompraCliente extends Component
{
    use WithPagination;

    public Cliente $cliente;

    #[On('orden-compra-guardada')]
    public function refrescar(): void {}

    public function render()
    {
        $ordenes = OrdenCompra::with(['items', 'campana', 'cotizacion', 'vendedor'])
            ->where('cliente_id', $this->cliente->id)
            ->orderByDesc('fecha_oc')
            ->paginate(10);

        return view('livewire.ordenes-compra.lista-ordenes-compra-cliente', compact('ordenes'));
    }
}
