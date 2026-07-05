<?php

namespace App\Livewire\Facturas;

use App\Models\Cliente;
use App\Models\Factura;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class ListaFacturasCliente extends Component
{
    use WithPagination;

    public Cliente $cliente;

    #[On('factura-guardada')]
    public function refrescar(): void {}

    public function render()
    {
        $facturas = Factura::with(['ordenCompra', 'vendedor'])
            ->where('cliente_id', $this->cliente->id)
            ->orderByDesc('fecha_emision')
            ->paginate(10);

        return view('livewire.facturas.lista-facturas-cliente', compact('facturas'));
    }
}
