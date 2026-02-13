<?php

namespace App\Observers;

use App\Models\Incident;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class IncidentObserver
{
    /**
     * Se ejecuta después de que el incidente se guarda en la DB.
     */
    public function created(Incident $incident): void
    {
        ActivityLog::create([
            'user_id'     => Auth::id(),
            'module'      => 'incidencias',
            'action'      => 'crear',
            'description' => "Reportó incidencia en Mesa N° {$incident->mesa->number}: " . substr($incident->description, 0, 50) . "...",
        ]);
    }

    /**
     * Se ejecuta después de actualizar (ej: cuando se marca como resuelto).
     */
    public function updated(Incident $incident): void
    {
        // Solo logueamos si cambió el estado de resolución
        if ($incident->wasChanged('is_resolved') && $incident->is_resolved) {
            ActivityLog::create([
                'user_id'     => Auth::id(),
                'module'      => 'incidencias',
                'action'      => 'actualizar',
                'description' => "Marcó como RESUELTA la incidencia en Mesa N° {$incident->mesa->number}",
            ]);
        }
    }

    /**
     * Handle the Incident "deleted" event.
     */
    public function deleted(Incident $incident): void
    {
        return; // No hacemos nada al eliminar, para preservar el historial de actividades
    }

    /**
     * Handle the Incident "restored" event.
     */
    public function restored(Incident $incident): void
    {
        //
    }

    /**
     * Handle the Incident "force deleted" event.
     */
    public function forceDeleted(Incident $incident): void
    {
        //
    }
}
