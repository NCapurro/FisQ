@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        
    <h2 class="mb-0"><i class="fa-solid fa-list-check me-2 text-primary"></i>Auditoría del Sistema</h2>
        <div class="d-flex gap-2">
    <a href="{{ route('logs.excel', request()->all()) }}" class="btn btn-outline-success shadow-sm">
        <i class="fa-solid fa-file-excel me-1"></i> Excel
    </a>
    <a href="{{ route('logs.pdf', request()->all()) }}" class="btn btn-outline-danger shadow-sm">
        <i class="fa-solid fa-file-pdf me-1"></i> PDF
    </a>
</div>
        
    </div>

    {{-- Panel de Filtros --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body bg-light rounded">
            <form action="{{ route('logs.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-bold small text-uppercase text-secondary">Usuario</label>
                    <select name="user_id" class="form-select border-0 shadow-sm">
                        <option value="">Todos los usuarios</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold small text-uppercase text-secondary">Módulo</label>
                    <select name="module" class="form-select border-0 shadow-sm">
                        <option value="">Todos los módulos</option>
                        {{-- Puedes hardcodear los módulos o traerlos de la DB con un pluck('module') --}}
                        @foreach($modules as $module)
                            <option value="{{ $module }}" {{ request('module') == $module ? 'selected' : '' }}>
                                {{ ucfirst($module) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold small text-uppercase text-secondary">Rango de Fecha</label>
                    <div class="input-group">
                        <input type="date" name="from" class="form-control border-0 shadow-sm" value="{{ request('from') }}">
                        <span class="input-group-text border-0 bg-transparent">a</span>
                        <input type="date" name="to" class="form-control border-0 shadow-sm" value="{{ request('to') }}">
                    </div>
                </div>

                <div class="col-md-2 d-flex align-items-end gap-1">
    {{-- Botón Filtrar (Principal) --}}
    <button type="submit" class="btn btn-primary w-100 shadow-sm fw-bold" title="Aplicar Filtros">
        <i class="fa-solid fa-magnifying-glass"></i> Filtrar
    </button>
    
    {{-- Botón Limpiar (Chiquito con X) --}}
    <a href="{{ route('logs.index') }}" 
       class="btn btn-outline-secondary shadow-sm px-2" 
       title="Limpiar Filtros">
        <i class="fa-solid fa-xmark"></i>
    </a>
</div>
                
            </form>
        </div>
    </div>

    {{-- Tabla de Resultados --}}
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-dark text-white">
                        <tr>
                            <th class="ps-4">Fecha</th>
                            <th>Usuario</th>
                            <th>Módulo</th>
                            <th>Acción</th>
                            <th>Descripción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            <tr>
                                <td class="ps-4 text-nowrap">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    @if($log->user)
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-2 bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 30px; height: 30px; font-size: 12px;">
                                                {{ substr($log->user->name, 0, 2) }}
                                            </div>
                                            <span class="fw-bold">{{ $log->user->name }}</span>
                                        </div>
                                    @else
                                        <span class="text-muted"><i class="fa-solid fa-robot me-1"></i> Sistema</span>
                                    @endif
                                </td>
                                <td><span class="badge bg-light text-dark border">{{ ucfirst($log->module) }}</span></td>
                                <td>
                                    @php
                                        $color = match($log->action) {
                                            'crear' => 'success',
                                            'eliminar' => 'danger',
                                            'restaurar' => 'info',
                                            default => 'primary'
                                        };
                                        $label = match($log->action) {
                                            'crear' => 'Creación',
                                            'eliminar' => 'Eliminación',
                                            'restaurar' => 'Restauración',
                                            default => ucfirst($log->action)
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $color }}">{{ $label }}</span>
                                </td>
                                <td class="small">{{ $log->description }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-triangle-exclamation fa-2x mb-3"></i>
                                    <p>No se encontraron registros con los filtros seleccionados.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Paginación --}}
    <div class="mt-4">
        {{ $logs->appends(request()->query())->links() }}
    </div>
</div>
@endsection