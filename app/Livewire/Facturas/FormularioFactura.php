<?php

namespace App\Livewire\Facturas;

use App\Enums\EstadoFactura;
use App\Models\Cliente;
use App\Models\Factura;
use App\Models\OrdenCompra;
use Livewire\Attributes\On;
use Livewire\Component;

class FormularioFactura extends Component
{
    public Cliente $cliente;
    public ?Factura $factura = null;
    public bool $mostrar = false;
    public bool $modoEdicion = false;

    public string $numero_factura    = '';
    public string $fecha_emision     = '';
    public string $fecha_vencimiento = '';
    public string $orden_compra_id   = '';
    public string $subtotal          = '0.00';
    public string $igv               = '0.00';
    public string $total             = '0.00';
    public string $estado_pago       = 'pendiente';
    public string $monto_pagado      = '0.00';
    public string $saldo_pendiente   = '0.00';
    public string $observaciones     = '';

    public function mount(Cliente $cliente): void
    {
        $this->cliente       = $cliente;
        $this->fecha_emision = today()->format('Y-m-d');
    }

    #[On('editar-factura')]
    public function editar(int $id): void
    {
        $this->factura          = Factura::findOrFail($id);
        $this->modoEdicion      = true;
        $this->mostrar          = true;
        $this->numero_factura   = $this->factura->numero_factura ?? '';
        $this->fecha_emision    = $this->factura->fecha_emision->format('Y-m-d');
        $this->fecha_vencimiento = $this->factura->fecha_vencimiento?->format('Y-m-d') ?? '';
        $this->orden_compra_id  = (string) ($this->factura->orden_compra_id ?? '');
        $this->subtotal         = (string) $this->factura->subtotal;
        $this->igv              = (string) $this->factura->igv;
        $this->total            = (string) $this->factura->total;
        $this->estado_pago      = $this->factura->estado_pago->value;
        $this->monto_pagado     = (string) $this->factura->monto_pagado;
        $this->saldo_pendiente  = (string) $this->factura->saldo_pendiente;
        $this->observaciones    = $this->factura->observaciones ?? '';
    }

    public function cancelar(): void
    {
        $this->reset(['modoEdicion', 'mostrar', 'numero_factura', 'fecha_vencimiento',
                      'orden_compra_id', 'observaciones']);
        $this->factura         = null;
        $this->estado_pago     = 'pendiente';
        $this->subtotal        = '0.00';
        $this->igv             = '0.00';
        $this->total           = '0.00';
        $this->monto_pagado    = '0.00';
        $this->saldo_pendiente = '0.00';
        $this->fecha_emision   = today()->format('Y-m-d');
    }

    public function updatedOrdenCompraId(): void
    {
        if ($this->orden_compra_id) {
            $oc = OrdenCompra::find($this->orden_compra_id);
            if ($oc) {
                $this->subtotal = (string) $oc->subtotal;
                $this->igv      = (string) $oc->igv;
                $this->total    = (string) $oc->total;
                $this->calcularSaldo();
            }
        }
    }

    public function updatedSubtotal(): void
    {
        $subtotal    = (float) $this->subtotal;
        $igv         = round($subtotal * 0.18, 2);
        $this->igv   = number_format($igv, 2, '.', '');
        $this->total = number_format($subtotal + $igv, 2, '.', '');
        $this->calcularSaldo();
    }

    public function updatedMontoPagado(): void
    {
        $this->calcularSaldo();
    }

    public function updatedTotal(): void
    {
        $this->calcularSaldo();
    }

    public function calcularSaldo(): void
    {
        $total       = (float) $this->total;
        $pagado      = (float) $this->monto_pagado;
        $saldo       = max(0, $total - $pagado);
        $this->saldo_pendiente = number_format($saldo, 2, '.', '');

        if ($this->estado_pago === 'anulada') {
            return;
        }
        if ($total > 0 && $pagado >= $total) {
            $this->estado_pago = 'pagada';
        } elseif ($pagado > 0) {
            $this->estado_pago = 'parcialmente_pagada';
        } elseif ($this->estado_pago !== 'vencida') {
            $this->estado_pago = 'pendiente';
        }
    }

    protected function rules(): array
    {
        return [
            'numero_factura'   => 'nullable|string|max:50',
            'fecha_emision'    => 'required|date',
            'fecha_vencimiento' => 'nullable|date|after_or_equal:fecha_emision',
            'orden_compra_id'  => 'nullable|exists:ordenes_compra,id',
            'subtotal'         => 'required|numeric|min:0',
            'igv'              => 'required|numeric|min:0',
            'total'            => 'required|numeric|min:0',
            'estado_pago'      => 'required|string',
            'monto_pagado'     => 'required|numeric|min:0',
            'observaciones'    => 'nullable|string|max:2000',
        ];
    }

    protected function messages(): array
    {
        return [
            'fecha_vencimiento.after_or_equal' => 'La fecha de vencimiento no puede ser anterior a la emisión.',
            'monto_pagado.min'                 => 'El monto pagado no puede ser negativo.',
        ];
    }

    public function guardar(): void
    {
        $this->calcularSaldo();
        $this->validate();

        $datos = [
            'numero_factura'   => $this->numero_factura ?: null,
            'fecha_emision'    => $this->fecha_emision,
            'fecha_vencimiento' => $this->fecha_vencimiento ?: null,
            'orden_compra_id'  => $this->orden_compra_id ?: null,
            'subtotal'         => $this->subtotal,
            'igv'              => $this->igv,
            'total'            => $this->total,
            'estado_pago'      => $this->estado_pago,
            'monto_pagado'     => $this->monto_pagado,
            'saldo_pendiente'  => $this->saldo_pendiente,
            'observaciones'    => $this->observaciones ?: null,
        ];

        if ($this->modoEdicion) {
            $this->factura->update($datos);
        } else {
            $datos['cliente_id']  = $this->cliente->id;
            $datos['vendedor_id'] = auth()->id();
            Factura::create($datos);
        }

        $this->cancelar();
        $this->dispatch('factura-guardada');
    }

    public function render()
    {
        return view('livewire.facturas.formulario-factura', [
            'estados'         => EstadoFactura::cases(),
            'ordenes_compra'  => OrdenCompra::where('cliente_id', $this->cliente->id)
                                    ->orderByDesc('fecha_oc')
                                    ->get(),
        ]);
    }
}
