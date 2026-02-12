<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ActivityLog;
use App\Models\User;

class ActivityLogController extends Controller
{
    public function index(Request $request)
{
    $query = ActivityLog::with('user');

    // Filtro por Usuario
    if ($request->filled('user_id')) {
        $query->where('user_id', $request->user_id);
    }

    // Filtro por Módulo
    if ($request->filled('module')) {
        $query->where('module', $request->module);
    }

    // Filtro por Fecha
    if ($request->filled('from')) {
        $query->whereDate('created_at', '>=', $request->from);
    }
    if ($request->filled('to')) {
        $query->whereDate('created_at', '<=', $request->to);
    }

    $logs = $query->latest()->paginate(20);
    
    // Datos para los selects de los filtros
    $users = User::orderBy('name')->get();
    $modules = ActivityLog::distinct()->pluck('module');

    return view('logs.index', compact('logs', 'users', 'modules'));
}
}
