<?php

namespace App\Livewire\Campanas;

use App\Enums\EstadoCampana;
use App\Models\Campana;
use Livewire\Component;
use Livewire\WithPagination;

class ListaCampanas extends Component
{
    use WithPagination;

    public string $busqueda = '';
    public string $filtroEstado = '';

    protected $queryString = [
        'busqueda'     => ['except' => ''],
        'filtroEstado' => ['except' => ''],
    ];

    public function updatingBusqueda(): void { $this->resetPage(); }
    public function updatingFiltroEstado(): void { $this->resetPage(); }

    public function eliminar(int $id): void
    {
        $campana = Campana::findOrFail($id);
        $campana->clientes()->detach();
        $campana->delete();
        session()->flash('success', 'Campaña eliminada correctamente.');
    }

    public function render()
    {
        $campanas = Campana::withCount('clientes')
            ->when($this->busqueda, fn($q) => $q->where('nombre', 'like', "%{$this->busqueda}%"))
            ->when($this->filtroEstado, fn($q) => $q->where('estado', $this->filtroEstado))
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('livewire.campanas.lista-campanas', [
            'campanas' => $campanas,
            'estados'  => EstadoCampana::cases(),
        ]);
    }
}
