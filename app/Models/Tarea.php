<?php

namespace App\Models;

use App\Enums\TipoTarea;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tarea extends Model
{
    protected $fillable = [
        'cliente_id', 'campana_id', 'usuario_id', 'seguimiento_id',
        'titulo', 'descripcion', 'tipo', 'fecha_vencimiento',
        'estado', 'prioridad', 'completada_en',
    ];

    protected $casts = [
        'fecha_vencimiento' => 'date',
        'completada_en'     => 'datetime',
        'tipo'              => TipoTarea::class,
    ];

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

    public function seguimiento(): BelongsTo
    {
        return $this->belongsTo(Seguimiento::class);
    }

    public static function estadosActivos(): array
    {
        return ['pendiente', 'en_proceso'];
    }

    public static function labelEstado(string $estado): string
    {
        return match($estado) {
            'pendiente'  => 'Por iniciar',
            'en_proceso' => 'En proceso',
            'completada' => 'Completada',
            default      => ucfirst($estado),
        };
    }

    public static function colorEstado(string $estado): string
    {
        return match($estado) {
            'pendiente'  => 'bg-gray-100 text-gray-600',
            'en_proceso' => 'bg-blue-100 text-blue-700',
            'completada' => 'bg-green-100 text-green-700',
            default      => 'bg-gray-100 text-gray-600',
        };
    }

    public function estaVencida(): bool
    {
        return in_array($this->estado, self::estadosActivos())
            && $this->fecha_vencimiento->isPast();
    }

    public function scopePendientes(Builder $query): Builder
    {
        return $query->whereIn('estado', self::estadosActivos());
    }

    public function scopeVencidas(Builder $query): Builder
    {
        return $query->whereIn('estado', self::estadosActivos())
                     ->where('fecha_vencimiento', '<', today());
    }

    public function scopeDeHoy(Builder $query): Builder
    {
        return $query->whereIn('estado', self::estadosActivos())
                     ->whereDate('fecha_vencimiento', today());
    }
}
