<?php

namespace App\Observers;

use App\Models\User;
use App\Models\ActivityLog;

class UserObserver
{
    //Diccionario de campos
    protected $campos = [
        'DNI' => 'DNI',
        'name' => 'Nombre',
        'lastname' => 'Apellido',
        'email' => 'Correo Electrónico',
        'phone' => 'Teléfono',
        'address' => 'Dirección',
        'role' => 'Rol',    
        'department_id' => 'Departamento ID',
        
    ];


    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        ActivityLog::registrar(
            'crear', 
            'Usuarios', 
            "Se creó el Usuario: '{$user->name}' - ID: {$user->id}"
        );
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {

        // --- 1. LÓGICA DE BAJA ---
        if ($user->wasChanged('is_active') && !$user->is_active) {
            ActivityLog::registrar(
                'eliminar', 
                'Usuarios', 
                "Eliminó el Usuario: '{$user->full_name}' - ID: {$user->id}"
            );
            return;
        }

        // --- 2. LÓGICA DE RESTAURACIÓN---
        if ($user->wasChanged('is_active') && $user->is_active) {
            ActivityLog::registrar(
                'restaurar', 
                'Usuarios', 
                "Restauró el Usuario: '{$user->full_name}' - ID: {$user->id}"
            );
            return; 
        }

        $cambios = $user->getChanges();

        foreach ($cambios as $key => $valorNuevo) {
            
            
            if (array_key_exists($key, $this->campos)) {

                // === LÓGICA ESPECIAL PARA PASSWORD ===
                if ($key === 'password') {
                    ActivityLog::registrar(
                        'seguridad', 
                        'Usuarios', 
                        "El Usuario '{$user->name}' (ID: {$user->id}) modificó su contraseña."
                    );
                    
                    continue; // Evitar el log genérico abajo
                }
                
                $nombreBonito = $this->campos[$key];
                $valorViejo = $user->getOriginal($key);

                ActivityLog::registrar(
                    'editar_campo', 
                    'Usuarios', 
                    "Cambió $nombreBonito del Usuario '{$user->name}' (ID: {$user->id}): de '$valorViejo' a '$valorNuevo'"
                );
            }
        }
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
       
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
      
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
