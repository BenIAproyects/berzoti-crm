<?php

namespace App\Livewire\GuiasRemision;

use App\Models\Cliente;
use App\Models\GuiaRemision;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class ListaGuiasRemisionCliente extends Component
{
    use WithPagination;

    public Cliente $cliente;

    #[On('guia-remision-guardada')]
    public function refrescar(): void {}

    public function render()
    {
        $guias = GuiaRemision::with(['items', 'ordenCompra', 'factura', 'vendedor'])
            ->where('cliente_id', $this->cliente->id)
            ->orderByDesc('fecha_emision')
            ->paginate(10);

        return view('livewire.guias-remision.lista-guias-remision-cliente', compact('guias'));
    }
}
