<?php

namespace App\Livewire\Seguimientos;

use App\Models\Cliente;
use App\Models\Seguimiento;
use Livewire\Component;
use Livewire\WithPagination;

class ListaSeguimientos extends Component
{
    use WithPagination;

    public Cliente $cliente;

    protected $listeners = ['seguimiento-guardado' => '$refresh'];

    public function mount(Cliente $cliente): void
    {
        $this->cliente = $cliente;
    }

    public function render()
    {
        $seguimientos = Seguimiento::where('cliente_id', $this->cliente->id)
            ->with(['usuario', 'campana'])
            ->orderByDesc('fecha_hora')
            ->paginate(10);

        return view('livewire.seguimientos.lista-seguimientos', [
            'seguimientos' => $seguimientos,
        ]);
    }
}
