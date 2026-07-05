<?php

namespace App\Models;

use App\Enums\MetodoPago;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pago extends Model
{
    protected $fillable = [
        'factura_id', 'cliente_id', 'fecha_pago',
        'monto_pagado', 'metodo_pago', 'banco',
        'numero_operacion', 'observaciones', 'archivo_adjunto',
    ];

    protected $casts = [
        'fecha_pago'   => 'date',
        'monto_pagado' => 'decimal:2',
        'metodo_pago'  => MetodoPago::class,
    ];

    public function factura(): BelongsTo
    {
        return $this->belongsTo(Factura::class);
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }
}
