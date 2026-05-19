<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CorreoEnviado extends Model
{
    protected $table = 'correos_enviados';

    protected $fillable = [
        'cliente_id', 'campana_id', 'plantilla_id', 'usuario_id',
        'destinatario', 'asunto', 'cuerpo_renderizado',
        'estado_envio', 'error_mensaje', 'enviado_en',
        'mensaje_id', 'abierto', 'abierto_en', 'veces_abierto',
    ];

    protected $casts = [
        'enviado_en' => 'datetime',
        'abierto_en' => 'datetime',
        'abierto'    => 'boolean',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function campana(): BelongsTo
    {
        return $this->belongsTo(Campana::class);
    }

    public function plantilla(): BelongsTo
    {
        return $this->belongsTo(PlantillaCorreo::class, 'plantilla_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function fueEnviado(): bool
    {
        return $this->estado_envio === 'enviado';
    }
}
