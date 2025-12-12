<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Result;
use Illuminate\Support\Facades\DB;

class MapController extends Controller
{
    public function index()
    {
        // Obtenemos la ubicación de la carga de resultados
        // Agrupamos por mesa para no repetir pines si hay varios partidos
        $cargas = Result::with(['mesa.school', 'fiscal'])
                    ->select('mesa_id', 'user_id', 'latitude', 'longitude', 'created_at')
                    ->whereNotNull('latitude')
                    ->whereNotNull('longitude')
                    // Obtener solo una fila por mesa (la última carga)
                    ->groupBy('mesa_id', 'user_id', 'latitude', 'longitude', 'created_at') 
                    ->get();

        $puntos = $cargas->map(function($carga) {
            return [
                'lat' => $carga->latitude,
                'lng' => $carga->longitude,
                'mesa' => $carga->mesa->number,
                'escuela' => $carga->mesa->school->name,
                'departamento' => $carga->mesa->school->department->name ?? 'N/A',
                'fiscal' => $carga->fiscal->name . ' ' . $carga->fiscal->lastname,
                'hora' => $carga->created_at->format('H:i:s'),
                'url' => route('results.show', $carga->mesa_id),
            ];
        });

        // Uso values() para reindexar el array y evitar problemas en JS
        return view('maps.index', ['puntos' => $puntos->values()]);
    }
}