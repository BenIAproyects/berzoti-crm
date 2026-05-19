<?php

namespace App\Livewire\Cotizaciones;

use App\Enums\EstadoCotizacion;
use App\Models\Cotizacion;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class ListaGlobalCotizaciones extends Component
{
    use WithPagination;

    public string $filtroEstado  = '';
    public string $busqueda      = '';
    public string $filtroUsuario = '';

    public ?int   $editandoId    = null;
    public string $nuevoEstado   = '';
    public string $fecha_respuesta = '';

    public function updatingBusqueda(): void { $this->resetPage(); }
    public function updatingFiltroEstado(): void { $this->resetPage(); }

    public function abrirCambioEstado(int $id): void
    {
        $cot = Cotizacion::findOrFail($id);
        $this->editandoId      = $id;
        $this->nuevoEstado     = $cot->estado->value;
        $this->fecha_respuesta = $cot->fecha_respuesta?->format('Y-m-d') ?? '';
    }

    public function guardarEstado(): void
    {
        $this->validate([
            'nuevoEstado'    => 'required|string',
            'fecha_respuesta' => 'nullable|date|required_if:nuevoEstado,aprobada,rechazada',
        ], [
            'fecha_respuesta.required_if' => 'La fecha de respuesta es obligatoria al aprobar o rechazar.',
        ]);

        $datos = ['estado' => $this->nuevoEstado];
        if ($this->fecha_respuesta) {
            $datos['fecha_respuesta'] = $this->fecha_respuesta;
        }

        Cotizacion::where('id', $this->editandoId)->update($datos);

        $this->editandoId    = null;
        $this->nuevoEstado   = '';
        $this->fecha_respuesta = '';
        $this->resetValidation();
    }

    public function cancelarEdicion(): void
    {
        $this->editandoId    = null;
        $this->nuevoEstado   = '';
        $this->fecha_respuesta = '';
        $this->resetValidation();
    }

    public function eliminar(int $id): void
    {
        Cotizacion::destroy($id);
        $this->resetPage();
    }

    public function render()
    {
        $cotizaciones = Cotizacion::with(['cliente', 'usuario', 'campana'])
            ->when($this->filtroEstado,  fn($q) => $q->where('estado', $this->filtroEstado))
            ->when($this->filtroUsuario, fn($q) => $q->where('usuario_id', $this->filtroUsuario))
            ->when($this->busqueda, fn($q) => $q->whereHas('cliente', fn($c) =>
                $c->where('razon_social', 'like', "%{$this->busqueda}%")
            ))
            ->orderByDesc('fecha')
            ->paginate(20);

        return view('livewire.cotizaciones.lista-global-cotizaciones', [
            'cotizaciones' => $cotizaciones,
            'estados'      => EstadoCotizacion::cases(),
            'vendedores'   => User::role('vendedor')->orderBy('name')->get(),
        ]);
    }
}
