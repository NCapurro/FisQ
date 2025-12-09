<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;


class Result extends Model
{
    protected $fillable = [
        'votes',
        'user_id',
        'mesa_id',
        'political_party_id',
        'latitude',
        'longitude'
    ];

    // Pertenece a una mesa
    public function mesa()
    {
        return $this->belongsTo(Mesa::class);
    }

    // Pertenece (referencia) a un partido politico
    public function politicalParty()
    {
        return $this->belongsTo(PoliticalParty::class);
    }

    // Pertenece a un usuario (quien cargó el resultado)
    public function fiscal()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // MUTADOR: Asegura que los votos sean enteros no negativos
    protected function votes(): Attribute
    {
        return Attribute::make(
            // Asegura que sea entero y no negativo (max(0, ...))
            set: fn ($value) => max(0, (int) $value),
        );
    }

}
