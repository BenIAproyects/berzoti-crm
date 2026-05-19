<?php

namespace App\Livewire\Clientes;

use App\Enums\EstadoComercial;
use App\Enums\TipoCliente;
use App\Models\Cliente;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class ListaClientes extends Component
{
    use WithPagination;

    public string $busqueda = '';
    public string $filtroEstado = '';
    public string $filtroTipo = '';
    public string $filtroVendedor = '';
    public bool $soloActivos = true;

    protected $queryString = [
        'busqueda' => ['except' => ''],
        'filtroEstado' => ['except' => ''],
        'filtroTipo' => ['except' => ''],
        'filtroVendedor' => ['except' => ''],
    ];

    public function updatingBusqueda(): void
    {
        $this->resetPage();
    }

    public function updatingFiltroEstado(): void
    {
        $this->resetPage();
    }

    public function updatingFiltroTipo(): void
    {
        $this->resetPage();
    }

    public function updatingFiltroVendedor(): void
    {
        $this->resetPage();
    }

    public function limpiarFiltros(): void
    {
        $this->busqueda = '';
        $this->filtroEstado = '';
        $this->filtroTipo = '';
        $this->filtroVendedor = '';
        $this->resetPage();
    }

    public function toggleActivo(int $id): void
    {
        $cliente = Cliente::findOrFail($id);
        $cliente->update(['activo' => !$cliente->activo]);
        session()->flash('success', 'Estado del cliente actualizado.');
    }

    public function eliminar(int $id): void
    {
        $cliente = Cliente::findOrFail($id);
        $cliente->update(['activo' => false]);
        session()->flash('success', 'Cliente desactivado correctamente.');
        $this->resetPage();
    }

    public function render()
    {
        $clientes = Cliente::with('vendedor')
            ->when($this->soloActivos, fn($q) => $q->where('activo', true))
            ->when($this->busqueda, fn($q) => $q->buscar($this->busqueda))
            ->when($this->filtroEstado, fn($q) => $q->where('estado_comercial', $this->filtroEstado))
            ->when($this->filtroTipo, fn($q) => $q->where('tipo_cliente', $this->filtroTipo))
            ->when($this->filtroVendedor, fn($q) => $q->where('vendedor_asignado_id', $this->filtroVendedor))
            ->orderByDesc('updated_at')
            ->paginate(20);

        return view('livewire.clientes.lista-clientes', [
            'clientes' => $clientes,
            'estados' => EstadoComercial::cases(),
            'tipos' => TipoCliente::cases(),
            'vendedores' => User::role('vendedor')->orderBy('name')->get(),
        ]);
    }
}
