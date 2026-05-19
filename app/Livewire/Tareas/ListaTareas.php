<?php

namespace App\Livewire\Tareas;

use App\Models\Cliente;
use App\Models\Tarea;
use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class ListaTareas extends Component
{
    use WithPagination;

    public string $filtroEstado    = 'activas';
    public string $filtroUsuario   = '';
    public string $filtroPrioridad = '';

    // Formulario nueva tarea
    public bool $mostrarFormulario   = false;
    public string $titulo            = '';
    public string $tipo              = '';
    public string $prioridad         = 'media';
    public string $fecha_vencimiento = '';
    public string $descripcion       = '';
    public string $cliente_id        = '';
    public string $clienteBusqueda   = '';
    public bool   $clienteConfirmado = false;

    public function updatingFiltroEstado(): void { $this->resetPage(); }
    public function updatingFiltroUsuario(): void { $this->resetPage(); }
    public function updatingFiltroPrioridad(): void { $this->resetPage(); }

    public function updatedClienteBusqueda(): void
    {
        // Si el usuario vuelve a escribir después de haber seleccionado, reinicia la selección
        if ($this->clienteConfirmado) {
            $this->clienteConfirmado = false;
            $this->cliente_id        = '';
        }
    }

    public function abrirFormulario(): void
    {
        $this->resetFormulario();
        $this->mostrarFormulario = true;
    }

    public function resetFormulario(): void
    {
        $this->titulo            = '';
        $this->tipo              = '';
        $this->prioridad         = 'media';
        $this->fecha_vencimiento = today()->format('Y-m-d');
        $this->descripcion       = '';
        $this->cliente_id        = '';
        $this->clienteBusqueda   = '';
        $this->clienteConfirmado = false;
        $this->resetValidation();
    }

    #[Computed]
    public function clientesSugeridos()
    {
        if ($this->clienteConfirmado || strlen($this->clienteBusqueda) < 2) {
            return collect();
        }
        return Cliente::activos()
            ->where(fn($q) => $q
                ->where('razon_social', 'like', "%{$this->clienteBusqueda}%")
                ->orWhere('ruc', 'like', "%{$this->clienteBusqueda}%")
            )
            ->orderBy('razon_social')
            ->limit(8)
            ->get(['id', 'razon_social', 'ruc']);
    }

    public function seleccionarCliente(int $id, string $nombre): void
    {
        $this->cliente_id        = (string) $id;
        $this->clienteBusqueda   = $nombre;
        $this->clienteConfirmado = true;
    }

    public function limpiarCliente(): void
    {
        $this->cliente_id        = '';
        $this->clienteBusqueda   = '';
        $this->clienteConfirmado = false;
        $this->dispatch('limpiar-cliente');
    }

    public function guardar(): void
    {
        $this->validate([
            'titulo'            => 'required|string|max:200',
            'cliente_id'        => 'required|exists:clientes,id',
            'fecha_vencimiento' => 'required|date',
            'prioridad'         => 'required|in:alta,media,baja',
        ], [
            'titulo.required'            => 'El título es obligatorio.',
            'cliente_id.required'        => 'Debes seleccionar un cliente.',
            'fecha_vencimiento.required' => 'La fecha de vencimiento es obligatoria.',
        ]);

        Tarea::create([
            'titulo'            => $this->titulo,
            'tipo'              => $this->tipo ?: null,
            'prioridad'         => $this->prioridad,
            'fecha_vencimiento' => $this->fecha_vencimiento,
            'descripcion'       => $this->descripcion ?: null,
            'cliente_id'        => $this->cliente_id,
            'usuario_id'        => auth()->id(),
            'estado'            => 'pendiente',
        ]);

        $this->mostrarFormulario = false;
        $this->resetFormulario();
        $this->resetPage();
    }

    public function cambiarEstado(int $tareaId, string $estado): void
    {
        $datos = ['estado' => $estado];
        if ($estado === 'completada') {
            $datos['completada_en'] = now();
        }
        Tarea::where('id', $tareaId)->update($datos);
    }

    public function completar(int $tareaId): void
    {
        $this->cambiarEstado($tareaId, 'completada');
    }

    public function eliminar(int $tareaId): void
    {
        Tarea::destroy($tareaId);
    }

    public function render()
    {
        $tareas = Tarea::with(['cliente', 'usuario'])
            ->when($this->filtroEstado, function ($q) {
                if ($this->filtroEstado === 'activas') {
                    $q->whereIn('estado', Tarea::estadosActivos());
                } else {
                    $q->where('estado', $this->filtroEstado);
                }
            })
            ->when($this->filtroUsuario,   fn($q) => $q->where('usuario_id', $this->filtroUsuario))
            ->when($this->filtroPrioridad, fn($q) => $q->where('prioridad', $this->filtroPrioridad))
            ->orderByRaw("CASE WHEN estado = 'pendiente' AND fecha_vencimiento < CURDATE() THEN 0 ELSE 1 END")
            ->orderBy('fecha_vencimiento')
            ->paginate(20);

        return view('livewire.tareas.lista-tareas', [
            'tareas'     => $tareas,
            'vendedores' => User::role('vendedor')->orderBy('name')->get(),
        ]);
    }
}
