<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ReportController;
use Illuminate\Http\Request;
use App\Models\ActivityLog;
use App\Models\User;


use App\Exports\ActivityLogsExport; 
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

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

public function exportExcel(Request $request) 
{
    // Obtenemos los logs con los mismos filtros de la vista
    $logs = $this->applyFilters($request)->get();
    return Excel::download(new ActivityLogsExport($logs), 'auditoria_fisq_' . now()->format('d-m-Y') . '.xlsx');
}

public function exportPdf(Request $request) 
{
    $aux = new ReportController();
    $logoBase64 = $aux->generarLogo();

    $logs = $this->applyFilters($request)->get();
    $pdf = Pdf::loadView('logs.pdf', compact('logs', 'logoBase64'));
    
    
    // Configuramos horizontal para que entre bien la descripción
    return $pdf->setPaper('a4', 'landscape')->download('auditoria_fisq.pdf');
}

// Método privado para no repetir código de filtros
private function applyFilters(Request $request)
{
    $query = ActivityLog::with('user');

    if ($request->filled('user_id')) {
        $query->where('user_id', $request->user_id);
    }
    if ($request->filled('module')) {
        $query->where('module', $request->module);
    }
    if ($request->filled('from')) {
        $query->whereDate('created_at', '>=', $request->from);
    }
    if ($request->filled('to')) {
        $query->whereDate('created_at', '<=', $request->to);
    }

    return $query->latest();
}


}
