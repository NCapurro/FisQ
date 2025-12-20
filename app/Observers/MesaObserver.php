<?php

namespace App\Observers;

use App\Models\Mesa;
use App\Models\School;
use App\Models\User;
use App\Models\ActivityLog;

class MesaObserver
{
    //Diccionario de campos
    protected $campos = [
        'number' => 'Número de Mesa',
        'school_id' => 'Escuela ID',
        // 'status' y 'user_id' están aquí, pero los filtraremos si aplica la lógica especial
        'status' => 'Estado',
        'user_id' => 'Usuario Asignado',
        'image_path' => 'Ruta de Imagen',
    ];

    /**
     * Handle the Mesa "created" event.
     */
    public function created(Mesa $mesa): void
    {
        ActivityLog::registrar('crear', 'Mesas', "Creó la Mesa: {$mesa->number} - ID: {$mesa->id}");
    }

    /**
     * Handle the Mesa "updated" event.
     */
    public function updated(Mesa $mesa): void
    {

        // --- 1. LÓGICA DE BAJA ---
        if ($mesa->wasChanged('is_active') && !$mesa->is_active) {
            ActivityLog::registrar(
                'eliminar', 
                'Mesas', 
                "Eliminó la mesa: '{$mesa->number}' - ID: {$mesa->id}"
            );
            return;
        }

        // --- 2. LÓGICA DE RESTAURACIÓN---
        if ($mesa->wasChanged('is_active') && $mesa->is_active) {
            ActivityLog::registrar(
                'restaurar', 
                'Mesas', 
                "Restauró la mesa: '{$mesa->number}' - ID: {$mesa->id}"
            );
            return; 
        }


        // === 1. LÓGICA ESPECIAL: ASIGNACIÓN DE FISCAL ===
        $huboAsignacion = false; // Bandera para controlar el loop de abajo

        // Si cambió el status a 'asigned' O cambió el usuario...
        if (($mesa->wasChanged('status') && $mesa->status === 'asigned') || $mesa->wasChanged('user_id')) {
            
            // Cargamos la relación para obtener el nombre real
            $mesa->load('fiscal');
            $nombreFiscal = $mesa->fiscal ? $mesa->fiscal->name : 'Un Fiscal';
            
            // Creamos el LOG DETALLADO
            ActivityLog::registrar(
                'asignar',
                'Mesas',
                "Asignó la Mesa N° {$mesa->number} (ID: {$mesa->id}) al fiscal {$nombreFiscal} (ID: {$mesa->user_id})"
            );

            // Marcamos que ya manejamos estos campos para que el bucle no los repita
            $huboAsignacion = true;
        }


        // === 2. LÓGICA GENÉRICA (EL BUCLE) ===
        $cambios = $mesa->getChanges();

        foreach ($cambios as $key => $valorNuevo) {
            // A. Ignoramos timestamps
            if ($key === 'updated_at') continue;

            // B. EVITAR DUPLICADOS: 
            // Si ya corrió la lógica de asignación, saltamos 'user_id' y 'status'
            if ($huboAsignacion && ($key === 'user_id' || $key === 'status')) {
                continue; 
            }

            // C. Logueamos el resto de campos (ej: number, image_path, school_id)
            if (array_key_exists($key, $this->campos)) {
                
                $nombreBonito = $this->campos[$key];
                $valorViejo = $mesa->getOriginal($key);

                ActivityLog::registrar(
                    'editar_campo', 
                    'Mesas', 
                    "Cambió $nombreBonito de la Mesa '{$mesa->number}' (ID: {$mesa->id}): de '$valorViejo' a '$valorNuevo'"
                );
            }
        }
    }

    /**
     * Handle the Mesa "deleted" event.
     */
    public function deleted(Mesa $mesa): void
    {
    }

    /**
     * Handle the Mesa "restored" event.
     */
    public function restored(Mesa $mesa): void
    {
        //
    }

    /**
     * Handle the Mesa "force deleted" event.
     */
    public function forceDeleted(Mesa $mesa): void
    {
        //
    }
}
