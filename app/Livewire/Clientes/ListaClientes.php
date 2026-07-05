<?php

namespace App\Livewire\Clientes;

use App\Enums\EstadoComercial;
use App\Enums\FuenteCliente;
use App\Enums\SegmentoCliente;
use App\Enums\TipoCliente;
use App\Models\Cliente;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class ListaClientes extends Component
{
    use WithPagination;

    public string $busqueda      = '';
    public string $filtroEstado  = '';
    public string $filtroTipo    = '';
    public string $filtroVendedor = '';
    public string $filtroFuente  = '';
    public string $filtroSegmento = '';
    public string $filtroZona    = '';
    public bool   $soloActivos   = true;

    public string $ordenarPor  = 'updated_at';
    public string $ordenarDir  = 'desc';

    protected $queryString = [
        'busqueda'       => ['except' => ''],
        'filtroEstado'   => ['except' => ''],
        'filtroTipo'     => ['except' => ''],
        'filtroVendedor' => ['except' => ''],
        'filtroFuente'   => ['except' => ''],
        'filtroSegmento' => ['except' => ''],
        'filtroZona'     => ['except' => ''],
        'ordenarPor'     => ['except' => 'updated_at'],
        'ordenarDir'     => ['except' => 'desc'],
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

    public function updatingFiltroFuente(): void
    {
        $this->resetPage();
    }

    public function updatingFiltroSegmento(): void { $this->resetPage(); }
    public function updatingFiltroZona(): void      { $this->resetPage(); }

    public function ordenar(string $campo): void
    {
        $permitidos = ['razon_social', 'tipo_cliente', 'estado_comercial', 'cantidad_compra', 'fecha_proximo_contacto', 'updated_at'];
        if (! in_array($campo, $permitidos)) return;

        if ($this->ordenarPor === $campo) {
            $this->ordenarDir = $this->ordenarDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->ordenarPor = $campo;
            $this->ordenarDir = 'asc';
        }
        $this->resetPage();
    }

    public function limpiarFiltros(): void
    {
        $this->reset(['busqueda', 'filtroEstado', 'filtroTipo', 'filtroVendedor',
                      'filtroFuente', 'filtroSegmento', 'filtroZona']);
        $this->resetPage();
    }

    public function cambiarEstado(int $id, string $nuevoEstado): void
    {
        $cliente = Cliente::findOrFail($id);
        $cliente->update(['estado_comercial' => $nuevoEstado]);
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
            ->when($this->filtroFuente, fn($q) => $q->where('fuente', $this->filtroFuente))
            ->when($this->filtroSegmento, fn($q) => $q->where('segmento', $this->filtroSegmento))
            ->when($this->filtroZona, fn($q) => $q->where('zona', $this->filtroZona))
            ->orderBy($this->ordenarPor, $this->ordenarDir)
            ->paginate(20);

        $zonas = Cliente::whereNotNull('zona')->where('zona', '!=', '')
            ->distinct()->orderBy('zona')->pluck('zona');

        return view('livewire.clientes.lista-clientes', [
            'clientes'  => $clientes,
            'estados'   => EstadoComercial::cases(),
            'tipos'     => TipoCliente::cases(),
            'fuentes'   => FuenteCliente::cases(),
            'segmentos' => SegmentoCliente::cases(),
            'vendedores' => User::role('vendedor')->orderBy('name')->get(),
            'zonas'     => $zonas,
        ]);
    }
}
