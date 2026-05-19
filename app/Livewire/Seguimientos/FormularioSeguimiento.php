<?php

namespace App\Livewire\Seguimientos;

use App\Enums\EstadoComercial;
use App\Enums\TipoSeguimiento;
use App\Models\Campana;
use App\Models\Cliente;
use App\Models\Seguimiento;
use App\Models\Tarea;
use Livewire\Component;

class FormularioSeguimiento extends Component
{
    public Cliente $cliente;
    public bool $mostrar = false;

    public string $tipo = 'llamada';
    public string $fecha_hora = '';
    public string $detalle = '';
    public string $resultado = '';
    public string $estado_comercial_nuevo = '';
    public string $proxima_accion = '';
    public string $fecha_proxima_accion = '';
    public string $campana_id = '';

    public function mount(Cliente $cliente): void
    {
        $this->cliente    = $cliente;
        $this->fecha_hora = now()->format('Y-m-d\TH:i');
    }

    protected function rules(): array
    {
        return [
            'tipo'                    => 'required|string',
            'fecha_hora'              => 'required|date',
            'detalle'                 => 'required|string|max:2000',
            'resultado'               => 'nullable|string|max:2000',
            'estado_comercial_nuevo'  => 'nullable|string',
            'proxima_accion'          => 'nullable|string|max:255',
            'fecha_proxima_accion'    => 'nullable|date|after_or_equal:today',
            'campana_id'              => 'nullable|exists:campanas,id',
        ];
    }

    protected function messages(): array
    {
        return [
            'tipo.required'    => 'El tipo de seguimiento es obligatorio.',
            'detalle.required' => 'El detalle es obligatorio.',
            'fecha_proxima_accion.after_or_equal' => 'La fecha próxima debe ser hoy o posterior.',
        ];
    }

    public function guardar(): void
    {
        $datos = $this->validate();
        $datos['cliente_id'] = $this->cliente->id;
        $datos['usuario_id'] = auth()->id();
        $datos['campana_id'] = $datos['campana_id'] ?: null;
        $datos['fecha_proxima_accion'] = $datos['fecha_proxima_accion'] ?: null;
        $datos['estado_comercial_nuevo'] = $datos['estado_comercial_nuevo'] ?: null;

        $seguimiento = Seguimiento::create($datos);

        // Actualizar estado comercial del cliente si cambió
        if ($datos['estado_comercial_nuevo']) {
            $this->cliente->update([
                'estado_comercial'       => $datos['estado_comercial_nuevo'],
                'fecha_ultimo_contacto'  => now(),
            ]);
        } else {
            $this->cliente->update(['fecha_ultimo_contacto' => now()]);
        }

        // Crear tarea automática si hay fecha próxima acción
        if ($datos['fecha_proxima_accion'] && $datos['proxima_accion']) {
            $tipoTarea = match($datos['tipo']) {
                'reunion'    => 'confirmar_reunion',
                'cotizacion' => 'enviar_cotizacion',
                'correo'     => 'enviar_correo',
                'visita'     => 'visitar',
                default      => 'llamar',
            };
            Tarea::create([
                'cliente_id'        => $this->cliente->id,
                'campana_id'        => $datos['campana_id'],
                'usuario_id'        => auth()->id(),
                'seguimiento_id'    => $seguimiento->id,
                'titulo'            => $datos['proxima_accion'],
                'tipo'              => $tipoTarea,
                'fecha_vencimiento' => $datos['fecha_proxima_accion'],
                'estado'            => 'pendiente',
                'prioridad'         => 'media',
            ]);
        }

        $this->reset(['tipo', 'detalle', 'resultado', 'estado_comercial_nuevo',
                      'proxima_accion', 'fecha_proxima_accion', 'campana_id']);
        $this->fecha_hora = now()->format('Y-m-d\TH:i');
        $this->mostrar    = false;

        $this->dispatch('seguimiento-guardado');
    }

    public function render()
    {
        return view('livewire.seguimientos.formulario-seguimiento', [
            'tipos'    => TipoSeguimiento::cases(),
            'estados'  => EstadoComercial::cases(),
            'campanas' => Campana::activas()->orderBy('nombre')->get(),
        ]);
    }
}
