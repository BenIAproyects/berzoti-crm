<?php

namespace App\Livewire\OrdenesCompra;

use App\Enums\EstadoOrdenCompra;
use App\Models\Campana;
use App\Models\Cliente;
use App\Models\Cotizacion;
use App\Models\OrdenCompra;
use Livewire\Attributes\On;
use Livewire\Component;

class FormularioOrdenCompra extends Component
{
    public Cliente $cliente;
    public ?OrdenCompra $ordenCompra = null;
    public bool $mostrar = false;
    public bool $modoEdicion = false;

    public string $numero_oc        = '';
    public string $fecha_oc         = '';
    public string $fecha_recepcion  = '';
    public string $estado           = 'recibida';
    public string $campana_id       = '';
    public string $cotizacion_id    = '';
    public string $observaciones    = '';
    public string $subtotal         = '0.00';
    public string $igv              = '0.00';
    public string $total            = '0.00';

    public array $items = [];

    public function mount(Cliente $cliente): void
    {
        $this->cliente  = $cliente;
        $this->fecha_oc = today()->format('Y-m-d');
        $this->agregarItem();
    }

    #[On('editar-orden-compra')]
    public function editar(int $id): void
    {
        $this->ordenCompra     = OrdenCompra::with('items')->findOrFail($id);
        $this->modoEdicion     = true;
        $this->mostrar         = true;
        $this->numero_oc       = $this->ordenCompra->numero_oc ?? '';
        $this->fecha_oc        = $this->ordenCompra->fecha_oc->format('Y-m-d');
        $this->fecha_recepcion = $this->ordenCompra->fecha_recepcion?->format('Y-m-d') ?? '';
        $this->estado          = $this->ordenCompra->estado->value;
        $this->campana_id      = (string) ($this->ordenCompra->campana_id ?? '');
        $this->cotizacion_id   = (string) ($this->ordenCompra->cotizacion_id ?? '');
        $this->observaciones   = $this->ordenCompra->observaciones ?? '';
        $this->subtotal        = (string) $this->ordenCompra->subtotal;
        $this->igv             = (string) $this->ordenCompra->igv;
        $this->total           = (string) $this->ordenCompra->total;

        $this->items = $this->ordenCompra->items
            ->map(fn($item) => [
                'producto'        => $item->producto,
                'descripcion'     => $item->descripcion ?? '',
                'cantidad_pedida' => (string) $item->cantidad_pedida,
                'precio_unitario' => (string) $item->precio_unitario,
                'subtotal'        => (string) $item->subtotal,
                'igv'             => (string) $item->igv,
                'total'           => (string) $item->total,
            ])
            ->toArray();

        if (empty($this->items)) {
            $this->agregarItem();
        }
    }

    public function cancelar(): void
    {
        $this->reset(['modoEdicion', 'mostrar', 'numero_oc', 'fecha_recepcion',
                      'campana_id', 'cotizacion_id', 'observaciones', 'items']);
        $this->ordenCompra = null;
        $this->estado      = 'recibida';
        $this->subtotal    = '0.00';
        $this->igv         = '0.00';
        $this->total       = '0.00';
        $this->fecha_oc    = today()->format('Y-m-d');
        $this->agregarItem();
    }

    public function agregarItem(): void
    {
        $this->items[] = [
            'producto'        => '',
            'descripcion'     => '',
            'cantidad_pedida' => '1',
            'precio_unitario' => '',
            'subtotal'        => '0.00',
            'igv'             => '0.00',
            'total'           => '0.00',
        ];
    }

    public function quitarItem(int $index): void
    {
        array_splice($this->items, $index, 1);
        $this->calcularTotales();
    }

    public function calcularItem(int $index): void
    {
        $cantidad  = (float) ($this->items[$index]['cantidad_pedida'] ?? 0);
        $precio    = (float) ($this->items[$index]['precio_unitario'] ?? 0);
        $subtotal  = $cantidad * $precio;
        $igv       = round($subtotal * OrdenCompra::IGV_RATE, 2);
        $total     = $subtotal + $igv;

        $this->items[$index]['subtotal'] = number_format($subtotal, 2, '.', '');
        $this->items[$index]['igv']      = number_format($igv, 2, '.', '');
        $this->items[$index]['total']    = number_format($total, 2, '.', '');

        $this->calcularTotales();
    }

    public function calcularTotales(): void
    {
        $subtotal = collect($this->items)->sum(fn($i) => (float) ($i['subtotal'] ?? 0));
        $igv      = collect($this->items)->sum(fn($i) => (float) ($i['igv'] ?? 0));
        $total    = collect($this->items)->sum(fn($i) => (float) ($i['total'] ?? 0));

        $this->subtotal = number_format($subtotal, 2, '.', '');
        $this->igv      = number_format($igv, 2, '.', '');
        $this->total    = number_format($total, 2, '.', '');
    }

    protected function rules(): array
    {
        return [
            'fecha_oc'                          => 'required|date',
            'fecha_recepcion'                   => 'nullable|date',
            'numero_oc'                         => 'nullable|string|max:100',
            'estado'                            => 'required|string',
            'observaciones'                     => 'nullable|string|max:2000',
            'campana_id'                        => 'nullable|exists:campanas,id',
            'cotizacion_id'                     => 'nullable|exists:cotizaciones,id',
            'items'                             => 'required|array|min:1',
            'items.*.producto'                  => 'required|string|max:255',
            'items.*.descripcion'               => 'nullable|string|max:500',
            'items.*.cantidad_pedida'           => 'required|integer|min:1',
            'items.*.precio_unitario'           => 'required|numeric|min:0',
        ];
    }

    protected function messages(): array
    {
        return [
            'items.required'                        => 'Debes agregar al menos un ítem.',
            'items.min'                             => 'Debes agregar al menos un ítem.',
            'items.*.producto.required'             => 'El nombre del producto es obligatorio.',
            'items.*.cantidad_pedida.required'      => 'La cantidad es obligatoria.',
            'items.*.cantidad_pedida.integer'       => 'La cantidad debe ser un número entero.',
            'items.*.cantidad_pedida.min'           => 'La cantidad debe ser al menos 1.',
            'items.*.precio_unitario.required'      => 'El precio unitario es obligatorio.',
        ];
    }

    public function guardar(): void
    {
        $this->calcularTotales();
        $this->validate();

        $datos = [
            'numero_oc'       => $this->numero_oc ?: null,
            'fecha_oc'        => $this->fecha_oc,
            'fecha_recepcion' => $this->fecha_recepcion ?: null,
            'estado'          => $this->estado,
            'campana_id'      => $this->campana_id ?: null,
            'cotizacion_id'   => $this->cotizacion_id ?: null,
            'observaciones'   => $this->observaciones ?: null,
            'subtotal'        => $this->subtotal,
            'igv'             => $this->igv,
            'total'           => $this->total,
        ];

        if ($this->modoEdicion) {
            $this->ordenCompra->update($datos);
            $this->ordenCompra->items()->delete();
        } else {
            $datos['cliente_id']  = $this->cliente->id;
            $datos['vendedor_id'] = auth()->id();
            $this->ordenCompra = OrdenCompra::create($datos);

            if ($this->cotizacion_id) {
                Cotizacion::where('id', $this->cotizacion_id)
                    ->update(['convertida_a_oc' => true]);
            }
        }

        foreach ($this->items as $orden => $item) {
            $this->ordenCompra->items()->create([
                'producto'        => $item['producto'],
                'descripcion'     => $item['descripcion'] ?: null,
                'cantidad_pedida' => $item['cantidad_pedida'],
                'precio_unitario' => $item['precio_unitario'],
                'subtotal'        => $item['subtotal'],
                'igv'             => $item['igv'],
                'total'           => $item['total'],
                'orden'           => $orden,
            ]);
        }

        $this->cancelar();
        $this->dispatch('orden-compra-guardada');
    }

    public function render()
    {
        return view('livewire.ordenes-compra.formulario-orden-compra', [
            'estados'      => EstadoOrdenCompra::cases(),
            'campanas'     => Campana::orderBy('nombre')->get(),
            'cotizaciones' => Cotizacion::where('cliente_id', $this->cliente->id)
                                ->orderByDesc('fecha')
                                ->get(),
        ]);
    }
}
