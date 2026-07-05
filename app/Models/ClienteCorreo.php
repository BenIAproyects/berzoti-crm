<?php

namespace App\Models;

use App\Enums\EstadoCorreoCliente;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClienteCorreo extends Model
{
    protected $table = 'cliente_correos';

    protected $fillable = [
        'cliente_id',
        'email',
        'nombre',
        'fuente',
        'estado',
        'es_principal',
        'activo',
        'contacto_id',
    ];

    protected $casts = [
        'estado'       => EstadoCorreoCliente::class,
        'es_principal' => 'boolean',
        'activo'       => 'boolean',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }
}
