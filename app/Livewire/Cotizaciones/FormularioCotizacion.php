<?php

namespace App\Livewire\Cotizaciones;

use App\Enums\EstadoCotizacion;
use App\Models\Campana;
use App\Models\Cliente;
use App\Models\Cotizacion;
use Livewire\Component;

class FormularioCotizacion extends Component
{
    public Cliente $cliente;
    public ?Cotizacion $cotizacion = null;
    public bool $mostrar = false;
    public bool $modoEdicion = false;

    public string $fecha = '';
    public string $monto_total = '0.00';
    public string $estado = 'borrador';
    public string $observaciones = '';
    public string $campana_id = '';
    public string $fecha_envio = '';
    public string $fecha_respuesta = '';

    public array $items = [];

    protected $listeners = ['editar-cotizacion' => 'editar'];

    public function mount(Cliente $cliente): void
    {
        $this->cliente = $cliente;
        $this->fecha   = today()->format('Y-m-d');
        $this->agregarItem();
    }

    public function editar(int $id): void
    {
        $this->cotizacion      = Cotizacion::with('items')->findOrFail($id);
        $this->modoEdicion     = true;
        $this->mostrar         = true;
        $this->fecha           = $this->cotizacion->fecha->format('Y-m-d');
        $this->monto_total     = (string) $this->cotizacion->monto_total;
        $this->estado          = $this->cotizacion->estado->value;
        $this->observaciones   = $this->cotizacion->observaciones ?? '';
        $this->campana_id      = (string) ($this->cotizacion->campana_id ?? '');
        $this->fecha_envio     = $this->cotizacion->fecha_envio?->format('Y-m-d') ?? '';
        $this->fecha_respuesta = $this->cotizacion->fecha_respuesta?->format('Y-m-d') ?? '';

        $this->items = $this->cotizacion->items
            ->map(fn($item) => [
                'descripcion'     => $item->descripcion,
                'cantidad'        => (string) $item->cantidad,
                'precio_unitario' => (string) $item->precio_unitario,
                'subtotal'        => (string) $item->subtotal,
            ])
            ->toArray();

        if (empty($this->items)) {
            $this->agregarItem();
        }
    }

    public function cancelar(): void
    {
        $this->reset(['modoEdicion', 'mostrar', 'observaciones',
                      'campana_id', 'fecha_envio', 'fecha_respuesta', 'items']);
        $this->cotizacion  = null;
        $this->estado      = 'borrador';
        $this->monto_total = '0.00';
        $this->fecha       = today()->format('Y-m-d');
        $this->agregarItem();
    }

    public function agregarItem(): void
    {
        $this->items[] = [
            'descripcion'     => '',
            'cantidad'        => '1',
            'precio_unitario' => '',
            'subtotal'        => '0.00',
        ];
    }

    public function quitarItem(int $index): void
    {
        array_splice($this->items, $index, 1);
        $this->calcularTotal();
    }

    public function calcularItem(int $index): void
    {
        $cantidad       = (float) ($this->items[$index]['cantidad'] ?? 0);
        $precio         = (float) ($this->items[$index]['precio_unitario'] ?? 0);
        $this->items[$index]['subtotal'] = number_format($cantidad * $precio, 2, '.', '');
        $this->calcularTotal();
    }

    public function calcularTotal(): void
    {
        $total = collect($this->items)->sum(fn($i) => (float) ($i['subtotal'] ?? 0));
        $this->monto_total = number_format($total, 2, '.', '');
    }

    protected function rules(): array
    {
        return [
            'fecha'                       => 'required|date',
            'estado'                      => 'required|string',
            'observaciones'               => 'nullable|string|max:2000',
            'campana_id'                  => 'nullable|exists:campanas,id',
            'fecha_envio'                 => 'nullable|date',
            'fecha_respuesta'             => 'nullable|date|required_if:estado,aprobada,rechazada',
            'items'                       => 'required|array|min:1',
            'items.*.descripcion'         => 'required|string|max:500',
            'items.*.cantidad'            => 'required|numeric|min:0.01',
            'items.*.precio_unitario'     => 'required|numeric|min:0',
        ];
    }

    protected function messages(): array
    {
        return [
            'items.required'                   => 'Debes agregar al menos un ítem.',
            'items.min'                        => 'Debes agregar al menos un ítem.',
            'items.*.descripcion.required'     => 'La descripción del ítem es obligatoria.',
            'items.*.cantidad.required'        => 'La cantidad es obligatoria.',
            'items.*.cantidad.min'             => 'La cantidad debe ser mayor a cero.',
            'items.*.precio_unitario.required' => 'El precio unitario es obligatorio.',
            'fecha_respuesta.required_if'      => 'La fecha de respuesta es obligatoria para aprobar o rechazar.',
        ];
    }

    public function guardar(): void
    {
        $this->calcularTotal();
        $this->validate();

        $datos = [
            'fecha'           => $this->fecha,
            'monto_total'     => $this->monto_total,
            'estado'          => $this->estado,
            'observaciones'   => $this->observaciones ?: null,
            'campana_id'      => $this->campana_id ?: null,
            'fecha_envio'     => $this->fecha_envio ?: null,
            'fecha_respuesta' => $this->fecha_respuesta ?: null,
        ];

        if ($this->modoEdicion) {
            $this->cotizacion->update($datos);
            $this->cotizacion->items()->delete();
        } else {
            $datos['cliente_id'] = $this->cliente->id;
            $datos['usuario_id'] = auth()->id();
            $this->cotizacion = Cotizacion::create($datos);
        }

        foreach ($this->items as $orden => $item) {
            $this->cotizacion->items()->create([
                'descripcion'     => $item['descripcion'],
                'cantidad'        => $item['cantidad'],
                'precio_unitario' => $item['precio_unitario'],
                'subtotal'        => $item['subtotal'],
                'orden'           => $orden,
            ]);
        }

        $this->cancelar();
        $this->dispatch('cotizacion-guardada');
    }

    public function render()
    {
        return view('livewire.cotizaciones.formulario-cotizacion', [
            'estados'  => EstadoCotizacion::cases(),
            'campanas' => Campana::orderBy('nombre')->get(),
        ]);
    }
}
