<?php

namespace App\Livewire\Seguimientos;

use App\Models\Cliente;
use App\Models\CorreoEnviado;
use App\Models\Seguimiento;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class ListaSeguimientos extends Component
{
    use WithPagination;

    public Cliente $cliente;

    #[On('seguimiento-guardado')]
    public function refrescar(): void {}

    public function mount(Cliente $cliente): void
    {
        $this->cliente = $cliente;
    }

    public function render()
    {
        $seguimientos = Seguimiento::where('cliente_id', $this->cliente->id)
            ->with(['usuario', 'campana'])
            ->get()
            ->map(fn($s) => [
                'source' => 'seguimiento',
                'fecha'  => $s->fecha_hora,
                'data'   => $s,
            ]);

        $correos = CorreoEnviado::where('cliente_id', $this->cliente->id)
            ->with(['usuario', 'campana', 'plantilla'])
            ->get()
            ->map(fn($c) => [
                'source' => 'correo',
                'fecha'  => $c->created_at,
                'data'   => $c,
            ]);

        $timeline = $seguimientos->concat($correos)
            ->sortByDesc('fecha')
            ->values();

        $perPage  = 15;
        $page     = $this->getPage();
        $items    = $timeline->forPage($page, $perPage);

        $paginator = new LengthAwarePaginator(
            $items,
            $timeline->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'pageName' => 'page'],
        );

        return view('livewire.seguimientos.lista-seguimientos', [
            'timeline' => $paginator,
        ]);
    }
}
