<?php

namespace App\Models;

use App\Enums\EstadoComercial;
use App\Enums\TipoCliente;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Cliente extends Model
{
    protected $fillable = [
        'codigo', 'razon_social', 'nombre_comercial', 'ruc',
        'tipo_cliente', 'sector', 'contacto_principal', 'cargo_contacto',
        'telefono', 'whatsapp', 'correo', 'correo_secundario',
        'pais', 'departamento', 'provincia', 'distrito', 'direccion', 'referencia',
        'vendedor_asignado_id', 'estado_comercial', 'prioridad', 'origen',
        'cantidad_compra', 'mes_contacto', 'precio_ano_anterior',
        'observaciones', 'fecha_ultimo_contacto', 'fecha_proximo_contacto', 'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'fecha_ultimo_contacto' => 'date',
        'fecha_proximo_contacto' => 'date',
        'estado_comercial' => EstadoComercial::class,
        'tipo_cliente' => TipoCliente::class,
        'cantidad_compra' => 'integer',
        'precio_ano_anterior' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (Cliente $cliente) {
            $cliente->codigo = self::generarCodigo();
        });
    }

    private static function generarCodigo(): string
    {
        $ultimo = self::max('id') ?? 0;
        return 'CLI-' . str_pad($ultimo + 1, 5, '0', STR_PAD_LEFT);
    }

    public function vendedor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vendedor_asignado_id');
    }

    public function seguimientos(): HasMany
    {
        return $this->hasMany(Seguimiento::class);
    }

    public function tareas(): HasMany
    {
        return $this->hasMany(Tarea::class);
    }

    public function cotizaciones(): HasMany
    {
        return $this->hasMany(Cotizacion::class);
    }

    public function campanas(): BelongsToMany
    {
        return $this->belongsToMany(Campana::class, 'campana_cliente')
                    ->withPivot('estado_en_campana')
                    ->withTimestamps();
    }

    public function scopeActivos(Builder $query): Builder
    {
        return $query->where('activo', true);
    }

    public function scopeBuscar(Builder $query, string $termino): Builder
    {
        return $query->where(function ($q) use ($termino) {
            $q->where('razon_social', 'like', "%{$termino}%")
              ->orWhere('nombre_comercial', 'like', "%{$termino}%")
              ->orWhere('ruc', 'like', "%{$termino}%")
              ->orWhere('correo', 'like', "%{$termino}%")
              ->orWhere('telefono', 'like', "%{$termino}%")
              ->orWhere('contacto_principal', 'like', "%{$termino}%");
        });
    }

    public function getNombreDisplayAttribute(): string
    {
        return $this->razon_social ?? $this->nombre_comercial;
    }
}
