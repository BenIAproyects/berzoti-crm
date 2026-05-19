<?php

namespace App\Livewire\Correos;

use App\Jobs\EnviarCorreoJob;
use App\Models\CorreoEnviado;
use Livewire\Component;
use Livewire\WithPagination;

class HistorialCorreos extends Component
{
    use WithPagination;

    public string $filtroEstado = '';
    public ?int $clienteId = null;
    public ?int $campanaId = null;

    public function updatingFiltroEstado(): void { $this->resetPage(); }

    #[\Livewire\Attributes\On('aperturas-sincronizadas')]
    public function refrescar(): void {} // fuerza re-render

    public function reintentar(int $correoId): void
    {
        $correo = CorreoEnviado::find($correoId);
        if ($correo && $correo->estado_envio === 'fallido') {
            $correo->update(['estado_envio' => 'pendiente', 'error_mensaje' => null]);
            EnviarCorreoJob::dispatch($correo->id);
        }
    }

    public function reintentarTodos(): void
    {
        $fallidos = CorreoEnviado::where('estado_envio', 'fallido')
            ->when($this->campanaId, fn($q) => $q->where('campana_id', $this->campanaId))
            ->get();

        foreach ($fallidos as $correo) {
            $correo->update(['estado_envio' => 'pendiente', 'error_mensaje' => null]);
            EnviarCorreoJob::dispatch($correo->id);
        }
    }

    public function enviarPendientes(): void
    {
        $pendientes = CorreoEnviado::where('estado_envio', 'pendiente')
            ->when($this->campanaId, fn($q) => $q->where('campana_id', $this->campanaId))
            ->get();

        foreach ($pendientes as $correo) {
            EnviarCorreoJob::dispatch($correo->id);
        }
    }

    public function render()
    {
        $correos = CorreoEnviado::with(['cliente', 'campana', 'plantilla', 'usuario'])
            ->when($this->filtroEstado, fn($q) => $q->where('estado_envio', $this->filtroEstado))
            ->when($this->clienteId,   fn($q) => $q->where('cliente_id', $this->clienteId))
            ->when($this->campanaId,   fn($q) => $q->where('campana_id', $this->campanaId))
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('livewire.correos.historial-correos', compact('correos'));
    }
}
