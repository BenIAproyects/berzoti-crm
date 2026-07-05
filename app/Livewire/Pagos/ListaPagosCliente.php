<?php

namespace App\Livewire\Pagos;

use App\Models\Cliente;
use App\Models\Factura;
use App\Models\Pago;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class ListaPagosCliente extends Component
{
    use WithPagination;

    public Cliente $cliente;

    #[On('pago-guardado')]
    public function refrescar(): void {}

    public function eliminar(int $id): void
    {
        $pago    = Pago::find($id);
        $facturaId = $pago?->factura_id;
        $pago?->delete();

        if ($facturaId) {
            Factura::find($facturaId)?->recalcularDesdePagos();
        }
    }

    public function render()
    {
        $pagos = Pago::with(['factura'])
            ->where('cliente_id', $this->cliente->id)
            ->orderByDesc('fecha_pago')
            ->paginate(10);

        return view('livewire.pagos.lista-pagos-cliente', compact('pagos'));
    }
}
