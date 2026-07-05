<?php

namespace App\Models;

use App\Enums\TipoTelefono;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClienteTelefono extends Model
{
    protected $table = 'cliente_telefonos';

    protected $fillable = [
        'cliente_id',
        'numero',
        'nombre',
        'tipo',
        'es_principal',
        'activo',
        'contacto_id',
    ];

    protected $casts = [
        'tipo'         => TipoTelefono::class,
        'es_principal' => 'boolean',
        'activo'       => 'boolean',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }
}
