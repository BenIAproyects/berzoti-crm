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
            EnviarCorreoJob::dispatchSync($correo->id);
        }
    }

    public function reintentarTodos(): void
    {
        $fallidos = CorreoEnviado::where('estado_envio', 'fallido')
            ->when($this->campanaId, fn($q) => $q->where('campana_id', $this->campanaId))
            ->get();

        foreach ($fallidos as $correo) {
            $correo->update(['estado_envio' => 'pendiente', 'error_mensaje' => null]);
            EnviarCorreoJob::dispatchSync($correo->id);
        }
    }

    public function enviarPendientes(): void
    {
        $pendientes = CorreoEnviado::where('estado_envio', 'pendiente')
            ->when($this->campanaId, fn($q) => $q->where('campana_id', $this->campanaId))
            ->get();

        foreach ($pendientes as $correo) {
            EnviarCorreoJob::dispatchSync($correo->id);
        }
    }

    public function render()
    {
        $baseQuery = CorreoEnviado::query()
            ->when($this->clienteId, fn($q) => $q->where('cliente_id', $this->clienteId))
            ->when($this->campanaId, fn($q) => $q->where('campana_id', $this->campanaId));

        $stats = [
            'enviados'  => (clone $baseQuery)->where('estado_envio', 'enviado')->count(),
            'pendientes' => (clone $baseQuery)->where('estado_envio', 'pendiente')->count(),
            'fallidos'  => (clone $baseQuery)->where('estado_envio', 'fallido')->count(),
            'abiertos'  => (clone $baseQuery)->where('abierto', true)->count(),
        ];

        $correos = (clone $baseQuery)
            ->with(['cliente', 'campana', 'plantilla', 'usuario'])
            ->when($this->filtroEstado, fn($q) => $q->where('estado_envio', $this->filtroEstado))
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('livewire.correos.historial-correos', compact('correos', 'stats'));
    }
}
