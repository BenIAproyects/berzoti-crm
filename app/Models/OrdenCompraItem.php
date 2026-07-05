<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrdenCompraItem extends Model
{
    protected $table = 'orden_compra_items';

    protected $fillable = [
        'orden_compra_id', 'producto', 'descripcion',
        'cantidad_pedida', 'precio_unitario', 'subtotal', 'igv', 'total', 'orden',
    ];

    protected $casts = [
        'cantidad_pedida'  => 'integer',
        'precio_unitario'  => 'decimal:2',
        'subtotal'         => 'decimal:2',
        'igv'              => 'decimal:2',
        'total'            => 'decimal:2',
    ];

    public function ordenCompra(): BelongsTo
    {
        return $this->belongsTo(OrdenCompra::class);
    }
}
