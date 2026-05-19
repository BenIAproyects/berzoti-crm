<?php

namespace App\Models;

use App\Enums\EstadoCampana;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;

class Campana extends Model
{
    protected $fillable = [
        'nombre', 'descripcion', 'fecha_inicio', 'fecha_fin',
        'estado', 'objetivo_comercial', 'created_by',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin'    => 'date',
        'estado'       => EstadoCampana::class,
    ];

    public function creador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function clientes(): BelongsToMany
    {
        return $this->belongsToMany(Cliente::class, 'campana_cliente')
                    ->withPivot('estado_en_campana')
                    ->withTimestamps();
    }

    public function scopeActivas(Builder $query): Builder
    {
        return $query->where('estado', EstadoCampana::Activa->value);
    }

    public function estaActiva(): bool
    {
        return $this->estado === EstadoCampana::Activa;
    }

    public function totalClientes(): int
    {
        return $this->clientes()->count();
    }

    public function clientesPorEstado(): array
    {
        return $this->clientes()
            ->selectRaw('estado_comercial, count(*) as total')
            ->groupBy('estado_comercial')
            ->pluck('total', 'estado_comercial')
            ->toArray();
    }
}
