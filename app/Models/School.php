<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Str;

class School extends Model
{
    protected $fillable = ['name', 'department_id', 'address'];


    // Pertenece a un departamento
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    // Tiene muchas mesas
    public function mesas()
    {
        return $this->hasMany(Mesa::class);
    }

    // Tiene muchos resultados a través de las mesas
    public function results()
    {
        return $this->hasManyThrough(Result::class, Mesa::class);
    }

    // MUTADOR: Normaliza el nombre
    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => Str::title($value),
            set: fn ($value) => Str::title($value),
        );
    }


}
