<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mesa extends Model
{
    protected $fillable = [
    'number',
    'school_id',
    'status',
    'user_id',
    'image_path' ];
    
    //Pertenece a una escuela
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    //Tiene muchos resultados
    public function results(): HasMany
    {
        return $this->hasMany(Result::class);
    }

    //Tiene un usuario (fiscal asignado)
    public function fiscal(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
