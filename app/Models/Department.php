<?php

namespace App\Models;

use Illuminate\Support\Str; // Agregado para usar Str en el mutador, maneja caracteres especiales o con tilde
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Traits\HasBajaLogica;

class Department extends Model
{
    use HasBajaLogica;
    protected $cascades = ['schools'];
    protected $fillable = ['name', 'province_id', 'is_active'];

    // Pertenece a una provincia
    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    // Tiene muchas localidades
    public function schools(): HasMany
    {
        return $this->hasMany(School::class);
    }


    // Mutador para el atributo 'name', mayus y minus
    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => Str::upper($value),
            set: fn ($value) => Str::lower($value),
        );
    }

   
}