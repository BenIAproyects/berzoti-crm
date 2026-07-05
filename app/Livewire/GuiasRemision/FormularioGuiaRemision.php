<?php

namespace App\Livewire\GuiasRemision;

use App\Enums\EstadoGuiaRemision;
use App\Models\Cliente;
use App\Models\Factura;
use App\Models\GuiaRemision;
use App\Models\OrdenCompra;
use Livewire\Attributes\On;
use Livewire\Component;

class FormularioGuiaRemision extends Component
{
    public Cliente $cliente;
    public ?GuiaRemision $guia = null;
    public bool $mostrar = false;
    public bool $modoEdicion = false;

    public string $numero_guia       = '';
    public string $fecha_emision     = '';
    public string $fecha_entrega     = '';
    public string $orden_compra_id   = '';
    public string $factura_id        = '';
    public string $estado_entrega    = 'pendiente';
    public string $direccion_entrega = '';
    public string $observaciones     = '';

    public array $items = [];

    public function mount(Cliente $cliente): void
    {
        $this->cliente           = $cliente;
        $this->fecha_emision     = today()->format('Y-m-d');
        $this->direccion_entrega = $cliente->direccion ?? '';
        $this->agregarItem();
    }

    #[On('editar-guia-remision')]
    public function editar(int $id): void
    {
        $this->guia              = GuiaRemision::with('items')->findOrFail($id);
        $this->modoEdicion       = true;
        $this->mostrar           = true;
        $this->numero_guia       = $this->guia->numero_guia ?? '';
        $this->fecha_emision     = $this->guia->fecha_emision->format('Y-m-d');
        $this->fecha_entrega     = $this->guia->fecha_entrega?->format('Y-m-d') ?? '';
        $this->orden_compra_id   = (string) ($this->guia->orden_compra_id ?? '');
        $this->factura_id        = (string) ($this->guia->factura_id ?? '');
        $this->estado_entrega    = $this->guia->estado_entrega->value;
        $this->direccion_entrega = $this->guia->direccion_entrega ?? '';
        $this->observaciones     = $this->guia->observaciones ?? '';

        $this->items = $this->guia->items
            ->map(fn($item) => [
                'producto'         => $item->producto,
                'descripcion'      => $item->descripcion ?? '',
                'cantidad_enviada' => (string) $item->cantidad_enviada,
                'observaciones'    => $item->observaciones ?? '',
            ])
            ->toArray();

        if (empty($this->items)) {
            $this->agregarItem();
        }
    }

    public function cancelar(): void
    {
        $this->reset(['modoEdicion', 'mostrar', 'numero_guia', 'fecha_entrega',
                      'orden_compra_id', 'factura_id', 'observaciones', 'items']);
        $this->guia              = null;
        $this->estado_entrega    = 'pendiente';
        $this->fecha_emision     = today()->format('Y-m-d');
        $this->direccion_entrega = $this->cliente->direccion ?? '';
        $this->agregarItem();
    }

    public function updatedOrdenCompraId(): void
    {
        if ($this->orden_compra_id) {
            $oc = OrdenCompra::with('items')->find($this->orden_compra_id);
            if ($oc && $oc->items->isNotEmpty()) {
                $this->items = $oc->items->map(fn($item) => [
                    'producto'         => $item->producto,
                    'descripcion'      => $item->descripcion ?? '',
                    'cantidad_enviada' => (string) $item->cantidad_pedida,
                    'observaciones'    => '',
                ])->toArray();
                return;
            }
        }
        if (empty($this->items)) {
            $this->agregarItem();
        }
    }

    public function updatedFacturaId(): void
    {
        if ($this->factura_id) {
            $factura = Factura::with('ordenCompra.items')->find($this->factura_id);
            if ($factura) {
                if ($factura->orden_compra_id && ! $this->orden_compra_id) {
                    $this->orden_compra_id = (string) $factura->orden_compra_id;
                }
                if ($factura->ordenCompra && $factura->ordenCompra->items->isNotEmpty()) {
                    $this->items = $factura->ordenCompra->items->map(fn($item) => [
                        'producto'         => $item->producto,
                        'descripcion'      => $item->descripcion ?? '',
                        'cantidad_enviada' => (string) $item->cantidad_pedida,
                        'observaciones'    => '',
                    ])->toArray();
                    return;
                }
            }
        }
        if (empty($this->items)) {
            $this->agregarItem();
        }
    }

    public function agregarItem(): void
    {
        $this->items[] = [
            'producto'         => '',
            'descripcion'      => '',
            'cantidad_enviada' => '1',
            'observaciones'    => '',
        ];
    }

    public function quitarItem(int $index): void
    {
        array_splice($this->items, $index, 1);
        if (empty($this->items)) {
            $this->agregarItem();
        }
    }

    protected function rules(): array
    {
        return [
            'numero_guia'                      => 'nullable|string|max:50',
            'fecha_emision'                    => 'required|date',
            'fecha_entrega'                    => 'nullable|date',
            'orden_compra_id'                  => 'nullable|exists:ordenes_compra,id',
            'factura_id'                       => 'nullable|exists:facturas,id',
            'estado_entrega'                   => 'required|string',
            'direccion_entrega'                => 'nullable|string|max:500',
            'observaciones'                    => 'nullable|string|max:2000',
            'items'                            => 'required|array|min:1',
            'items.*.producto'                 => 'required|string|max:255',
            'items.*.descripcion'              => 'nullable|string|max:500',
            'items.*.cantidad_enviada'         => 'required|integer|min:1',
            'items.*.observaciones'            => 'nullable|string|max:500',
        ];
    }

    protected function messages(): array
    {
        return [
            'items.required'                   => 'Debes agregar al menos un ítem.',
            'items.min'                        => 'Debes agregar al menos un ítem.',
            'items.*.producto.required'        => 'El nombre del producto es obligatorio.',
            'items.*.cantidad_enviada.required' => 'La cantidad enviada es obligatoria.',
            'items.*.cantidad_enviada.min'     => 'La cantidad debe ser al menos 1.',
        ];
    }

    public function guardar(): void
    {
        $this->validate();

        $datos = [
            'numero_guia'       => $this->numero_guia ?: null,
            'fecha_emision'     => $this->fecha_emision,
            'fecha_entrega'     => $this->fecha_entrega ?: null,
            'orden_compra_id'   => $this->orden_compra_id ?: null,
            'factura_id'        => $this->factura_id ?: null,
            'estado_entrega'    => $this->estado_entrega,
            'direccion_entrega' => $this->direccion_entrega ?: null,
            'observaciones'     => $this->observaciones ?: null,
        ];

        if ($this->modoEdicion) {
            $this->guia->update($datos);
            $this->guia->items()->delete();
        } else {
            $datos['cliente_id']  = $this->cliente->id;
            $datos['vendedor_id'] = auth()->id();
            $this->guia = GuiaRemision::create($datos);
        }

        foreach ($this->items as $orden => $item) {
            $this->guia->items()->create([
                'producto'         => $item['producto'],
                'descripcion'      => $item['descripcion'] ?: null,
                'cantidad_enviada' => $item['cantidad_enviada'],
                'observaciones'    => $item['observaciones'] ?: null,
                'orden'            => $orden,
            ]);
        }

        $this->cancelar();
        $this->dispatch('guia-remision-guardada');
    }

    public function render()
    {
        return view('livewire.guias-remision.formulario-guia-remision', [
            'estados'        => EstadoGuiaRemision::cases(),
            'ordenes_compra' => OrdenCompra::where('cliente_id', $this->cliente->id)
                                    ->orderByDesc('fecha_oc')->get(),
            'facturas'       => Factura::where('cliente_id', $this->cliente->id)
                                    ->orderByDesc('fecha_emision')->get(),
        ]);
    }
}
