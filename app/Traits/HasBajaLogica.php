<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

trait HasBajaLogica
{
    /**
     * Boot del Trait: Se ejecuta automáticamente.
     */
    protected static function bootHasBajaLogica()
    {
        static::addGlobalScope('active', function (Builder $builder) {
            $builder->where('is_active', true);
        });
    }

    /**
     * Reemplazo del delete()
     */
    public function delete()
    {
        return DB::transaction(function () {
            // 1. Cascada
            if (property_exists($this, 'cascades')) {
                foreach ($this->cascades as $relation) {
                    $this->{$relation}()->get()->each(function ($child) {
                        $child->delete();
                    });
                }
            }

            // 2. Baja Lógica
            $this->is_active = false;
            $this->save();
            
            return true;
        });
    }

    public function scopeOnlyTrashed($query)
    {
        return $query->withoutGlobalScope('active')->where('is_active', false);
    }

    /**
     * Función para restaurar
     */
    public function restore()
    {
        return DB::transaction(function () {
            
            // 1. Lógica de "Restauración Inversa" (Padres)
            // Verificamos si el modelo define quiénes son sus padres
            if (property_exists($this, 'restore_parents')) {
                foreach ($this->restore_parents as $relation) {
                    
                    // Obtenemos al padre (incluso si está eliminado)
                    // Usamos la relación definida en el modelo
                    $parent = $this->{$relation}()->withoutGlobalScope('active')->first();

                    // Si existe y está inactivo, lo restauramos recursivamente
                    // (Al llamar a $parent->restore(), si él tiene padres, también los restaurará)
                    if ($parent && !$parent->is_active) {
                        $parent->restore();
                    }
                }
            }

        $this->is_active = true;
        $this->save();
        
        return true;
    });
    }

    public function restoreWithChildren()
    {
        return DB::transaction(function () {
            $this->restore();

            // Restaurar relaciones en cascada
            if (property_exists($this, 'cascades')) {
                foreach ($this->cascades as $relation) {
                    $this->{$relation}()->onlyTrashed()->get()->each(function ($child) {
                        $child->restoreWithChildren();
                    });
                }
            }

            return true;
        });
    }
}