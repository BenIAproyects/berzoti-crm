<?php

namespace App\Livewire\Campanas;

use App\Enums\TipoCliente;
use App\Models\Campana;
use App\Models\Cliente;
use Livewire\Component;
use Livewire\WithPagination;

class GestionarClientes extends Component
{
    use WithPagination;

    public Campana $campana;
    public string $busqueda = '';
    public string $filtroTipo = '';
    public string $tab = 'asignados';
    public string $mensaje = '';

    public function mount(Campana $campana): void
    {
        $this->campana = $campana;
    }

    public function updatingBusqueda(): void { $this->resetPage(); }
    public function updatingTab(): void
    {
        $this->resetPage();
        $this->mensaje = '';
    }

    public function asignar(int $clienteId): void
    {
        $ya = $this->campana->clientes()->where('cliente_id', $clienteId)->exists();

        if (!$ya) {
            $this->campana->clientes()->attach($clienteId, ['estado_en_campana' => 'nuevo']);
        }

        $this->mensaje = 'Cliente asignado a la campaña.';
        $this->resetPage();
    }

    public function quitar(int $clienteId): void
    {
        $this->campana->clientes()->detach($clienteId);
        $this->mensaje = 'Cliente quitado de la campaña.';
        $this->resetPage();
    }

    public function render()
    {
        $idsAsignados = $this->campana->clientes()->pluck('clientes.id');

        if ($this->tab === 'asignados') {
            $clientes = $this->campana->clientes()
                ->when($this->busqueda, fn($q) => $q->where(function ($q) {
                    $q->where('razon_social', 'like', "%{$this->busqueda}%")
                      ->orWhere('contacto_principal', 'like', "%{$this->busqueda}%")
                      ->orWhere('ruc', 'like', "%{$this->busqueda}%");
                }))
                ->when($this->filtroTipo, fn($q) => $q->where('tipo_cliente', $this->filtroTipo))
                ->orderBy('razon_social')
                ->paginate(20);
        } else {
            $clientes = Cliente::activos()
                ->whereNotIn('id', $idsAsignados)
                ->when($this->busqueda, fn($q) => $q->buscar($this->busqueda))
                ->when($this->filtroTipo, fn($q) => $q->where('tipo_cliente', $this->filtroTipo))
                ->orderBy('razon_social')
                ->paginate(20);
        }

        return view('livewire.campanas.gestionar-clientes', [
            'clientes'       => $clientes,
            'tipos'          => TipoCliente::cases(),
            'totalAsignados' => $idsAsignados->count(),
        ]);
    }
}
