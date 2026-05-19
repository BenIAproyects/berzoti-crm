<?php

namespace App\Livewire\Tareas;

use App\Models\Cliente;
use App\Models\Tarea;
use Livewire\Component;

class TareasCliente extends Component
{
    public Cliente $cliente;

    public function completar(int $tareaId): void
    {
        Tarea::where('id', $tareaId)
             ->where('cliente_id', $this->cliente->id)
             ->update(['estado' => 'completada', 'completada_en' => now()]);
    }

    public function render()
    {
        $tareas = Tarea::where('cliente_id', $this->cliente->id)
            ->where('estado', 'pendiente')
            ->orderByRaw("CASE WHEN fecha_vencimiento < CURDATE() THEN 0 ELSE 1 END")
            ->orderBy('fecha_vencimiento')
            ->get();

        return view('livewire.tareas.tareas-cliente', ['tareas' => $tareas]);
    }
}
