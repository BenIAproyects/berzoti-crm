<?php

namespace App\Models;

use App\Enums\EstadoComercial;
use App\Enums\TipoSeguimiento;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Seguimiento extends Model
{
    protected $fillable = [
        'cliente_id', 'campana_id', 'usuario_id', 'tipo',
        'fecha_hora', 'detalle', 'resultado',
        'estado_comercial_nuevo', 'proxima_accion', 'fecha_proxima_accion',
    ];

    protected $casts = [
        'fecha_hora'          => 'datetime',
        'fecha_proxima_accion' => 'date',
        'tipo'                => TipoSeguimiento::class,
        'estado_comercial_nuevo' => EstadoComercial::class,
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

    public function tarea(): HasOne
    {
        return $this->hasOne(Tarea::class);
    }
}
