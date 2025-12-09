<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Str;

class PoliticalParty extends Model
{
    protected $fillable = ['name', 'abbreviation', 'color_hex'];

    // Tiene muchos resultados
    public function results()
    {
        return $this->hasMany(Result::class);
    }

    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => Str::title($value),
            set: fn ($value) => Str::title($value),
        );
    }

    protected function abbreviation(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => Str::upper($value),
            set: fn ($value) => Str::upper($value),
        );
    }


    // MUTADOR: Normaliza el color
    protected function colorHex(): Attribute
    {
        return Attribute::make(
            set: function ($value) {
                // 1. Asegura que empiece con #
                $conHash = Str::start($value, '#');
                
                // 2. Convierte a mayúsculas
                return Str::upper($conHash);
            }
        );
    }


    
}
