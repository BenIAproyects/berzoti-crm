<?php

namespace App\Livewire\Reportes;

use App\Models\Cliente;
use App\Models\Cotizacion;
use App\Models\Seguimiento;
use App\Models\Tarea;
use App\Models\CorreoEnviado;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class ReporteGeneral extends Component
{
    use WithPagination;

    public string $tab = 'clientes';
    public string $filtroEstado = '';
    public string $filtroUsuario = '';
    public int $diasSinContacto = 7;

    public function updatingTab(): void { $this->resetPage(); }
    public function updatingFiltroEstado(): void { $this->resetPage(); }
    public function updatingFiltroUsuario(): void { $this->resetPage(); }

    // Queries base
    private function queryClientes()
    {
        return Cliente::activos()->with(['vendedor'])
            ->when($this->filtroEstado, fn($q) => $q->where('estado_comercial', $this->filtroEstado))
            ->when($this->filtroUsuario, fn($q) => $q->where('vendedor_asignado_id', $this->filtroUsuario))
            ->when($this->tab === 'sin_contacto', fn($q) =>
                $q->where(fn($q2) => $q2->whereNull('fecha_ultimo_contacto')
                    ->orWhere('fecha_ultimo_contacto', '<', now()->subDays($this->diasSinContacto)))
            )
            ->orderByDesc('created_at');
    }

    private function querySeguimientos()
    {
        return Seguimiento::with(['cliente', 'usuario'])
            ->when($this->filtroUsuario, fn($q) => $q->where('usuario_id', $this->filtroUsuario))
            ->orderByDesc('fecha_hora');
    }

    private function queryTareas()
    {
        return Tarea::with(['cliente', 'usuario'])
            ->when($this->filtroEstado, fn($q) => $q->where('estado', $this->filtroEstado))
            ->when($this->filtroUsuario, fn($q) => $q->where('usuario_id', $this->filtroUsuario))
            ->orderByRaw("CASE WHEN estado='pendiente' AND fecha_vencimiento < CURDATE() THEN 0 ELSE 1 END")
            ->orderBy('fecha_vencimiento');
    }

    private function queryCotizaciones()
    {
        return Cotizacion::with(['cliente', 'usuario'])
            ->when($this->filtroEstado, fn($q) => $q->where('estado', $this->filtroEstado))
            ->when($this->filtroUsuario, fn($q) => $q->where('usuario_id', $this->filtroUsuario))
            ->orderByDesc('fecha');
    }

    private function queryCorreos()
    {
        return CorreoEnviado::orderByDesc('created_at');
    }

    public function render()
    {
        $datos = match ($this->tab) {
            'clientes'    => $this->queryClientes()->paginate(15),
            'seguimientos' => $this->querySeguimientos()->paginate(15),
            'tareas'      => $this->queryTareas()->paginate(15),
            'cotizaciones' => $this->queryCotizaciones()->paginate(15),
            'correos'     => $this->queryCorreos()->paginate(15),
            'sin_contacto' => $this->queryClientes()->paginate(15),
            default       => collect(),
        };

        return view('livewire.reportes.reporte-general', [
            'datos'      => $datos,
            'vendedores' => User::role('vendedor')->orderBy('name')->get(),
        ]);
    }
}
