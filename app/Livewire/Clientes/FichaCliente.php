<?php

namespace App\Livewire\Clientes;

use App\Models\Cliente;
use Livewire\Component;

class FichaCliente extends Component
{
    public Cliente $cliente;
    public string $tabActiva = 'datos';

    public function mount(Cliente $cliente): void
    {
        $this->cliente = $cliente;
    }

    public function setTab(string $tab): void
    {
        $this->tabActiva = $tab;
    }

    public function render()
    {
        return view('livewire.clientes.ficha-cliente', [
            'cliente' => $this->cliente->load('vendedor'),
        ]);
    }
}
