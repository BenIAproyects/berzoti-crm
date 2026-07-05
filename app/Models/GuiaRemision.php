<?php

namespace App\Models;

use App\Enums\EstadoGuiaRemision;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GuiaRemision extends Model
{
    protected $table = 'guias_remision';

    protected $fillable = [
        'codigo', 'cliente_id', 'orden_compra_id', 'factura_id', 'vendedor_id',
        'numero_guia', 'fecha_emision', 'fecha_entrega', 'estado_entrega',
        'direccion_entrega', 'observaciones', 'archivo_adjunto',
    ];

    protected $casts = [
        'fecha_emision' => 'date',
        'fecha_entrega' => 'date',
        'estado_entrega' => EstadoGuiaRemision::class,
    ];

    protected static function booted(): void
    {
        static::creating(function (GuiaRemision $guia) {
            $guia->codigo = self::generarCodigo();
        });
    }

    private static function generarCodigo(): string
    {
        $ultimo = self::max('id') ?? 0;
        return 'GR-' . str_pad($ultimo + 1, 5, '0', STR_PAD_LEFT);
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function ordenCompra(): BelongsTo
    {
        return $this->belongsTo(OrdenCompra::class, 'orden_compra_id');
    }

    public function factura(): BelongsTo
    {
        return $this->belongsTo(Factura::class);
    }

    public function vendedor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vendedor_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(GuiaRemisionItem::class)->orderBy('orden');
    }

    public function scopePorEstado(Builder $query, string $estado): Builder
    {
        return $query->where('estado_entrega', $estado);
    }

    public function scopePendientesEntrega(Builder $query): Builder
    {
        return $query->whereNotIn('estado_entrega', ['entregada', 'anulada']);
    }
}
