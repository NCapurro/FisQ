<?php

namespace App\Observers;

use App\Models\School;
use App\Models\ActivityLog;


class SchoolObserver
{
    //Diccionario de campos
    protected $campos = [
        'name' => 'Nombre',
        'address' => 'Dirección',
        'department_id' => 'Departamento ID',
    ];



    /**
     * Handle the School "created" event.
     */
    public function created(School $school): void
    {
        ActivityLog::registrar(
            'crear', 
            'Escuelas', 
            "Se creó la Escuela: '{$school->name}' - ID: {$school->id}"
        );
    }

    /**
     * Handle the School "updated" event.
     */
    public function updated(School $school): void
    {
        // --- 1. LÓGICA DE BAJA LÓGICA (Simulando Eliminar) ---
        // Si cambió 'is_active' y ahora es FALSE
        if ($school->wasChanged('is_active') && !$school->is_active) {
            ActivityLog::registrar(
                'eliminar', 
                'Escuelas', 
                "Eliminó la Escuela: '{$school->name}' - ID: {$school->id}"
            );
            return; // Cortamos aquí para que no siga logueando campos
        }

        // --- 2. LÓGICA DE RESTAURACIÓN (Simulando Restaurar) ---
        // Si cambió 'is_active' y ahora es TRUE
        if ($school->wasChanged('is_active') && $school->is_active) {
            ActivityLog::registrar(
                'restaurar', 
                'Escuelas', 
                "Restauró la Escuela: '{$school->name}' - ID: {$school->id}"
            );
            return; // Cortamos aquí también
        }

        $cambios = $school->getChanges();

        foreach ($cambios as $key => $valorNuevo) {
       
            if (array_key_exists($key, $this->campos)) {
                
                $nombreBonito = $this->campos[$key];
                $valorViejo = $school->getOriginal($key);

                ActivityLog::registrar(
                    'editar_campo', 
                    'Escuelas', 
                    "Cambió $nombreBonito de la Escuela '{$school->name}' (ID: {$school->id}): de '$valorViejo' a '$valorNuevo'"
                );
            }
        }
    }

    /**
     * Handle the School "deleted" event.
     */
    public function delete(School $school): void

    {
        //No hay delete físico, solo baja lógica
    }

    /**
     * Handle the School "restored" event.
     */
    public function restored(School $school): void
    {
        //No hay restore físico, solo restauración lógica
    }

    /**
     * Handle the School "force deleted" event.
     */
    public function forceDeleted(School $school): void
    {
        //
    }
}
