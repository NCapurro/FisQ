<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ActivityLog;

class ActivityLogController extends Controller
{
    public function index()
    {
        // Traemos los logs ordenados del más nuevo al más viejo
    // Usamos 'with' para traer el nombre del usuario sin hacer 100 consultas
    $logs = ActivityLog::with('user')->latest()->paginate(20);

    return view('logs.index', compact('logs'));
    }
}
