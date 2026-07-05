<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClienteContacto extends Model
{
    protected $table = 'cliente_contactos';

    protected $fillable = [
        'cliente_id',
        'nombre_contacto',
        'cargo',
        'es_principal',
        'telefono',
        'correo',
        'observaciones',
        'activo',
    ];

    protected $casts = [
        'es_principal' => 'boolean',
        'activo'       => 'boolean',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }
}
