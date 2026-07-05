<?php

namespace App\Models;

use App\Enums\EstadoOrdenCompra;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrdenCompra extends Model
{
    protected $table = 'ordenes_compra';

    protected $fillable = [
        'codigo', 'cliente_id', 'campana_id', 'cotizacion_id', 'vendedor_id',
        'numero_oc', 'fecha_oc', 'fecha_recepcion', 'estado',
        'subtotal', 'igv', 'total', 'observaciones', 'archivo_adjunto',
    ];

    protected $casts = [
        'fecha_oc'        => 'date',
        'fecha_recepcion' => 'date',
        'estado'          => EstadoOrdenCompra::class,
        'subtotal'        => 'decimal:2',
        'igv'             => 'decimal:2',
        'total'           => 'decimal:2',
    ];

    const IGV_RATE = 0.18;

    protected static function booted(): void
    {
        static::creating(function (OrdenCompra $oc) {
            $oc->codigo = self::generarCodigo();
        });
    }

    private static function generarCodigo(): string
    {
        $ultimo = self::max('id') ?? 0;
        return 'OC-' . str_pad($ultimo + 1, 5, '0', STR_PAD_LEFT);
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function campana(): BelongsTo
    {
        return $this->belongsTo(Campana::class);
    }

    public function cotizacion(): BelongsTo
    {
        return $this->belongsTo(Cotizacion::class);
    }

    public function vendedor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vendedor_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrdenCompraItem::class)->orderBy('orden');
    }

    public function facturas(): HasMany
    {
        return $this->hasMany(Factura::class);
    }

    public function guiasRemision(): HasMany
    {
        return $this->hasMany(GuiaRemision::class);
    }

    public function scopePorEstado(Builder $query, string $estado): Builder
    {
        return $query->where('estado', $estado);
    }
}
