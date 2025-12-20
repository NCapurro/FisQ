<?php

namespace App\Observers;

use App\Models\PoliticalParty;
use App\Models\ActivityLog;

class PoliticalPartyObserver
{
    //Diccionario de campos
    protected $campos = [
        'name' => 'Nombre',
        'abbreviation' => 'Abreviatura',
        'color_hex' => 'Color HEX',
    ];



    /**
     * Handle the PoliticalParty "created" event.
     */
    public function created(PoliticalParty $politicalParty): void
    {
        ActivityLog::registrar(
            'crear', 
            'Partidos', 
            "Se creó el Partido Político: '{$politicalParty->name}' - ID: {$politicalParty->id}"
        );
    }

    /**
     * Handle the PoliticalParty "updated" event.
     */
    public function updated(PoliticalParty $politicalParty): void
    {
         // --- 1. LÓGICA DE BAJA ---
        if ($politicalParty->wasChanged('is_active') && !$politicalParty->is_active) {
            ActivityLog::registrar(
                'eliminar', 
                'Partidos', 
                "Eliminó el Partido politico: '{$politicalParty->name}' - ID: {$politicalParty->id}"
            );
            return;
        }

        // --- 2. LÓGICA DE RESTAURACIÓN---
        if ($politicalParty->wasChanged('is_active') && $politicalParty->is_active) {
            ActivityLog::registrar(
                'restaurar', 
                'Partidos', 
                "Restauró el Partido politico: '{$politicalParty->name}' - ID: {$politicalParty->id}"
            );
            return; 
        }
        

        $cambios = $politicalParty->getChanges();

        foreach ($cambios as $key => $valorNuevo) {
        
            
            if (array_key_exists($key, $this->campos)) {
                
                $nombreBonito = $this->campos[$key];
                $valorViejo = $politicalParty->getOriginal($key);

                ActivityLog::registrar(
                    'editar_campo', 
                    'Partidos', 
                    "Cambió $nombreBonito del Partido '{$politicalParty->name}' (ID: {$politicalParty->id}): de '$valorViejo' a '$valorNuevo'"
                );
            }
        }
    }

    /**
     * Handle the PoliticalParty "deleted" event.
     */
    public function deleted(PoliticalParty $politicalParty): void
    {
      
    }

    /**
     * Handle the PoliticalParty "restored" event.
     */
    public function restored(PoliticalParty $politicalParty): void
    {
        
    }

    /**
     * Handle the PoliticalParty "force deleted" event.
     */
    public function forceDeleted(PoliticalParty $politicalParty): void
    {
        //
    }
}
