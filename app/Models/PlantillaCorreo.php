<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PlantillaCorreo extends Model
{
    protected $table = 'plantillas_correo';

    protected $fillable = [
        'nombre', 'asunto', 'cuerpo_html', 'activo', 'created_by',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function creador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function correos(): HasMany
    {
        return $this->hasMany(CorreoEnviado::class, 'plantilla_id');
    }

    public function renderizar(Cliente $cliente): string
    {
        $variables = [
            '{{contacto_principal}}' => $cliente->contacto_principal ?: 'Estimados señores',
            '{{razon_social}}'       => $cliente->razon_social,
            '{{nombre_comercial}}'   => $cliente->nombre_comercial ?? $cliente->razon_social,
            '{{tipo_cliente}}'       => $cliente->tipo_cliente->label(),
            '{{vendedor_nombre}}'    => $cliente->vendedor?->name ?? '',
        ];

        return str_replace(
            array_keys($variables),
            array_values($variables),
            $this->cuerpo_html
        );
    }

    public function renderizarAsunto(Cliente $cliente): string
    {
        $variables = [
            '{{contacto_principal}}' => $cliente->contacto_principal ?: 'Estimados señores',
            '{{razon_social}}'       => $cliente->razon_social,
            '{{nombre_comercial}}'   => $cliente->nombre_comercial ?? $cliente->razon_social,
        ];

        return str_replace(
            array_keys($variables),
            array_values($variables),
            $this->asunto
        );
    }
}
