<?php

namespace App\Models;

use App\Enums\EstadoFactura;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Factura extends Model
{
    protected $fillable = [
        'codigo', 'cliente_id', 'orden_compra_id', 'vendedor_id',
        'numero_factura', 'fecha_emision', 'fecha_vencimiento',
        'subtotal', 'igv', 'total',
        'estado_pago', 'monto_pagado', 'saldo_pendiente',
        'observaciones', 'archivo_adjunto',
    ];

    protected $casts = [
        'fecha_emision'    => 'date',
        'fecha_vencimiento' => 'date',
        'estado_pago'      => EstadoFactura::class,
        'subtotal'         => 'decimal:2',
        'igv'              => 'decimal:2',
        'total'            => 'decimal:2',
        'monto_pagado'     => 'decimal:2',
        'saldo_pendiente'  => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (Factura $factura) {
            $factura->codigo = self::generarCodigo();
        });
    }

    private static function generarCodigo(): string
    {
        $ultimo = self::max('id') ?? 0;
        return 'FAC-' . str_pad($ultimo + 1, 5, '0', STR_PAD_LEFT);
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function ordenCompra(): BelongsTo
    {
        return $this->belongsTo(OrdenCompra::class, 'orden_compra_id');
    }

    public function vendedor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vendedor_id');
    }

    public function guiasRemision(): HasMany
    {
        return $this->hasMany(GuiaRemision::class);
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(Pago::class);
    }

    public function recalcularDesdePagos(): void
    {
        $totalPagado = (float) $this->pagos()->sum('monto_pagado');
        $saldo       = max(0, (float) $this->total - $totalPagado);

        $estado = $this->estado_pago->value;
        if ($estado !== 'anulada') {
            if ($saldo <= 0) {
                $estado = 'pagada';
            } elseif ($totalPagado > 0) {
                $estado = 'parcialmente_pagada';
            } elseif ($this->fecha_vencimiento && $this->fecha_vencimiento->isPast()) {
                $estado = 'vencida';
            } else {
                $estado = 'pendiente';
            }
        }

        $this->update([
            'monto_pagado'    => $totalPagado,
            'saldo_pendiente' => $saldo,
            'estado_pago'     => $estado,
        ]);
    }

    public function estaVencida(): bool
    {
        return $this->fecha_vencimiento
            && $this->fecha_vencimiento->isPast()
            && ! in_array($this->estado_pago->value, ['pagada', 'anulada']);
    }

    public function scopePorEstado(Builder $query, string $estado): Builder
    {
        return $query->where('estado_pago', $estado);
    }

    public function scopeVencidas(Builder $query): Builder
    {
        return $query->whereDate('fecha_vencimiento', '<', today())
                     ->whereNotIn('estado_pago', ['pagada', 'anulada']);
    }
}
