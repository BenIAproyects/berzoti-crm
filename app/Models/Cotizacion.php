<?php

namespace App\Models;

use App\Enums\EstadoCotizacion;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cotizacion extends Model
{
    protected $table = 'cotizaciones';

    protected $fillable = [
        'codigo', 'cliente_id', 'campana_id', 'usuario_id',
        'fecha', 'monto_total', 'estado', 'observaciones',
        'fecha_envio', 'fecha_respuesta', 'convertida_a_oc',
    ];

    protected $casts = [
        'fecha'           => 'date',
        'fecha_envio'     => 'date',
        'fecha_respuesta' => 'date',
        'monto_total'     => 'decimal:2',
        'estado'          => EstadoCotizacion::class,
        'convertida_a_oc' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (Cotizacion $cotizacion) {
            $cotizacion->codigo = self::generarCodigo();
        });
    }

    private static function generarCodigo(): string
    {
        $ultimo = self::max('id') ?? 0;
        return 'COT-' . str_pad($ultimo + 1, 5, '0', STR_PAD_LEFT);
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function campana(): BelongsTo
    {
        return $this->belongsTo(Campana::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(CotizacionItem::class)->orderBy('orden');
    }

    public function scopePorEstado(Builder $query, string $estado): Builder
    {
        return $query->where('estado', $estado);
    }
}
