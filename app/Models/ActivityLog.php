<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ActivityLog extends Model
{
    protected $fillable = ['user_id', 'action', 'module', 'description'];

    // Relación para saber quién fue
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Función estática para guardar logs fácilmente desde cualquier lado.
     */
    public static function registrar($action, $module, $description)
    {
        self::create([
            'user_id' => Auth::id(), // Detecta automáticamente al usuario logueado
            'action' => $action,
            'module' => $module,
            'description' => $description
        ]);
    }
}