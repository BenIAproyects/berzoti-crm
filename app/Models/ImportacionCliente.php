<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportacionCliente extends Model
{
    protected $table = 'importaciones_clientes';

    protected $fillable = [
        'usuario_id', 'archivo', 'total_filas', 'total_importadas',
        'total_duplicadas', 'total_error', 'estado', 'resultado_json',
    ];

    protected $casts = [
        'resultado_json' => 'array',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
