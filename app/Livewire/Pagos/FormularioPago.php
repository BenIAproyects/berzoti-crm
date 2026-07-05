<?php

namespace App\Livewire\Pagos;

use App\Enums\MetodoPago;
use App\Models\Cliente;
use App\Models\Factura;
use App\Models\Pago;
use Livewire\Attributes\On;
use Livewire\Component;

class FormularioPago extends Component
{
    public ?Cliente $cliente = null;   // null = modo global (sin cliente fijo)
    public ?Pago $pago = null;
    public bool $mostrar = false;
    public bool $modoEdicion = false;

    public string $clienteSelId      = '';  // selector de cliente en modo global
    public string $factura_id        = '';
    public string $fecha_pago        = '';
    public string $monto_pagado      = '0.00';
    public string $metodo_pago       = 'transferencia';
    public string $banco             = '';
    public string $numero_operacion  = '';
    public string $observaciones     = '';

    public string $infoSaldo         = '';
    public string $infoTotal         = '';
    public string $infoYaPagado      = '';


    public function mount(?Cliente $cliente = null): void
    {
        $this->cliente    = $cliente;
        $this->fecha_pago = today()->format('Y-m-d');
    }

    #[On('registrar-pago-factura')]
    public function preCargarFactura(int $facturaId, int $clienteId): void
    {
        $this->mostrar      = true;
        $this->clienteSelId = (string) $clienteId;
        $this->factura_id   = (string) $facturaId;
        $this->cargarInfoFactura();

        $factura = Factura::find($facturaId);
        if ($factura) {
            $this->monto_pagado = number_format((float) $factura->saldo_pendiente, 2, '.', '');
        }

        $this->js('window.scrollTo({top: 0, behavior: "smooth"})');
    }

    public function updatedClienteSelId(): void
    {
        $this->factura_id   = '';
        $this->monto_pagado = '0.00';
        $this->infoTotal    = $this->infoYaPagado = $this->infoSaldo = '';
    }

    #[On('editar-pago')]
    public function editar(int $id): void
    {
        $this->pago             = Pago::findOrFail($id);
        $this->modoEdicion      = true;
        $this->mostrar          = true;
        $this->factura_id       = (string) $this->pago->factura_id;
        $this->fecha_pago       = $this->pago->fecha_pago->format('Y-m-d');
        $this->monto_pagado     = (string) $this->pago->monto_pagado;
        $this->metodo_pago      = $this->pago->metodo_pago->value;
        $this->banco            = $this->pago->banco ?? '';
        $this->numero_operacion = $this->pago->numero_operacion ?? '';
        $this->observaciones    = $this->pago->observaciones ?? '';
        // En modo global, preseleccionar el cliente del pago
        if (! $this->cliente) {
            $this->clienteSelId = (string) $this->pago->cliente_id;
        }
        $this->cargarInfoFactura();
    }

    public function cancelar(): void
    {
        $this->reset(['modoEdicion', 'mostrar', 'factura_id', 'clienteSelId', 'banco',
                      'numero_operacion', 'observaciones', 'infoSaldo', 'infoTotal', 'infoYaPagado']);
        $this->pago         = null;
        $this->metodo_pago  = 'transferencia';
        $this->monto_pagado = '0.00';
        $this->fecha_pago   = today()->format('Y-m-d');
    }

    public function updatedFacturaId(): void
    {
        $this->cargarInfoFactura();
        if ($this->factura_id) {
            $factura = Factura::find($this->factura_id);
            if ($factura) {
                $this->monto_pagado = number_format((float) $factura->saldo_pendiente, 2, '.', '');
            }
        }
    }

    private function cargarInfoFactura(): void
    {
        if ($this->factura_id) {
            $factura = Factura::find($this->factura_id);
            if ($factura) {
                $this->infoTotal    = number_format((float) $factura->total, 2);
                $this->infoYaPagado = number_format((float) $factura->monto_pagado, 2);
                $this->infoSaldo    = number_format((float) $factura->saldo_pendiente, 2);
                return;
            }
        }
        $this->infoTotal = $this->infoYaPagado = $this->infoSaldo = '';
    }

    protected function rules(): array
    {
        return [
            'clienteSelId'     => $this->cliente ? 'nullable' : 'required|exists:clientes,id',
            'factura_id'       => 'required|exists:facturas,id',
            'fecha_pago'       => 'required|date',
            'monto_pagado'     => 'required|numeric|min:0.01',
            'metodo_pago'      => 'required|string',
            'banco'            => 'nullable|string|max:100',
            'numero_operacion' => 'nullable|string|max:100',
            'observaciones'    => 'nullable|string|max:2000',
        ];
    }

    protected function messages(): array
    {
        return [
            'clienteSelId.required' => 'Debes seleccionar un cliente.',
            'factura_id.required'   => 'Debes seleccionar una factura.',
            'monto_pagado.min'      => 'El monto debe ser mayor a cero.',
        ];
    }

    public function guardar(): void
    {
        $this->validate();

        $clienteId = $this->cliente?->id ?? (int) $this->clienteSelId;

        $datos = [
            'factura_id'       => $this->factura_id,
            'cliente_id'       => $clienteId,
            'fecha_pago'       => $this->fecha_pago,
            'monto_pagado'     => $this->monto_pagado,
            'metodo_pago'      => $this->metodo_pago,
            'banco'            => $this->banco ?: null,
            'numero_operacion' => $this->numero_operacion ?: null,
            'observaciones'    => $this->observaciones ?: null,
        ];

        if ($this->modoEdicion) {
            $this->pago->update($datos);
        } else {
            Pago::create($datos);
        }

        Factura::find($this->factura_id)?->recalcularDesdePagos();

        $this->cancelar();
        $this->dispatch('pago-guardado');
    }

    public function render()
    {
        $clienteId = $this->cliente?->id ?? ($this->clienteSelId ?: null);

        $facturas = $clienteId
            ? Factura::where('cliente_id', $clienteId)
                ->whereNotIn('estado_pago', ['pagada', 'anulada'])
                ->orderByDesc('fecha_emision')
                ->get()
            : collect();

        $clientes = $this->cliente
            ? collect()
            : Cliente::activos()->orderBy('razon_social')->get();

        return view('livewire.pagos.formulario-pago', [
            'facturas' => $facturas,
            'metodos'  => MetodoPago::cases(),
            'clientes' => $clientes,
        ]);
    }
}
