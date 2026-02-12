<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Incident extends Model
{
    use HasFactory;

    /**
     * Los atributos que se pueden asignar masivamente.
     */
    protected $fillable = [
        'user_id',
        'mesa_id',
        'description',
        'priority',
        'is_resolved',
    ];

    /**
     * Atributos con valores predeterminados (opcional, ya están en la migración).
     */
    protected $attributes = [
        'is_resolved' => false,
        'priority' => 'media',
    ];

    /**
     * Obtener el usuario (Fiscal) que reportó la incidencia.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtener la mesa asociada a la incidencia (si aplica).
     */
    public function mesa(): BelongsTo
    {
        return $this->belongsTo(Mesa::class);
    }
}