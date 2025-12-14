<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Str;
use App\Traits\HasBajaLogica;

class Province extends Model
{
    use HasBajaLogica;
    protected $fillable = ['name', 'is_active'];
    protected $cascades = ['departments'];

    // Tiene muchos departamentos
    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }

    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => Str::title($value),
            set: fn ($value) => Str::title($value),
        );
    }

   
}
