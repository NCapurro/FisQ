<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\HasBajaLogica;

class Mesa extends Model
{
    use HasBajaLogica;
    protected $cascades = ['results'];
    protected $restore_parents = ['school'];

    protected $fillable = [
    'number',
    'school_id',
    'status',
    'user_id',
    'electores_totales',
    'image_path',
    'is_active'
    ];
    
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

    public function incidents(): HasMany
    {
        return $this->hasMany(Incident::class);
    }

}
