<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GuiaRemisionItem extends Model
{
    protected $table = 'guia_remision_items';

    protected $fillable = [
        'guia_remision_id', 'producto', 'descripcion',
        'cantidad_enviada', 'observaciones', 'orden',
    ];

    protected $casts = [
        'cantidad_enviada' => 'integer',
    ];

    public function guiaRemision(): BelongsTo
    {
        return $this->belongsTo(GuiaRemision::class);
    }
}
