@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fa-solid fa-check-to-slot me-2"></i>Panel de Control de Mesas</h2>

        @if(Auth::user()->role === 'admin')
            <div class="d-flex gap-2">
                <a href="{{ route('mesas.create') }}" class="btn btn-primary">
                    <i class="fa-solid fa-plus me-1"></i> Nueva Mesa
                </a>
                <a href="{{ route('mesas.batch_create') }}" class="btn btn-outline-primary">
                    <i class="fa-solid fa-layer-group me-1"></i> Creación Masiva
                </a>
                <a href="{{ route('mesas.batch_assign') }}" class="btn btn-outline-warning text-dark">
                    <i class="fa-solid fa-user-check me-1"></i> Asignar Lote
                </a>
                @if(request('view_deleted'))
                    <a href="{{ route('mesas.index') }}" class="btn btn-secondary">
                        <i class="fa-solid fa-arrow-left me-1"></i> Volver a Activos
                    </a>
                @else
                    <a href="{{ route('mesas.index', ['view_deleted' => 1]) }}" class="btn btn-outline-danger">
                        <i class="fa-solid fa-trash-can me-1"></i> Papelera
                    </a>
                @endif
            </div>
        @else
            <div class="d-flex gap-2">
                <a href="{{ route('mesas.batch_assign') }}" class="btn btn-outline-warning text-dark">
                    <i class="fa-solid fa-user-check me-1"></i> Asignar Lote
                </a>
            </div>
        @endif
    </div>

    @if(Auth::user()->role === 'admin')
        <div class="card mb-4 border-0 shadow-sm bg-light">
            <div class="card-body py-2">
                <form method="GET" action="{{ route('mesas.index') }}" id="filterForm" class="row align-items-center gx-2">
    @if(request('view_deleted'))
        <input type="hidden" name="view_deleted" value="1">
    @endif

    <div class="col-auto">
        <label class="fw-bold text-secondary m-0"><i class="fa-solid fa-filter me-1"></i> Filtros:</label>
    </div>

    <div class="col-md-3">
        {{-- Al cambiar el Depto, se envía el form y se resetea la escuela (porque school_id ya no coincidirá) --}}
        <select name="department_id" class="form-select form-select-sm" onchange="document.getElementById('filterForm').submit()">
            <option value="">-- Departamento --</option>
            @foreach($departments as $dept)
                <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                    {{ $dept->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-4">
        <select name="school_id" class="form-select form-select-sm" onchange="document.getElementById('filterForm').submit()" 
            {{ empty($schools) ? 'disabled' : '' }}> {{-- Se deshabilita si no hay escuelas cargadas --}}
            
            <option value="">
                {{ empty($schools) ? '-- Selecciona Dpto primero --' : '-- Todas las Escuelas --' }}
            </option>
            
            @foreach($schools as $escuela)
                <option value="{{ $escuela->id }}" {{ request('school_id') == $escuela->id ? 'selected' : '' }}>
                    {{ $escuela->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-3"> <div class="input-group input-group-sm">
            <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Buscar Mesa...">
            <button type="submit" class="btn btn-secondary"><i class="fa-solid fa-magnifying-glass"></i></button>
        </div>
    </div>

    @if(request('department_id') || request('school_id') || request('search'))
        <div class="col-auto">
            <a href="{{ route('mesas.index', request('view_deleted') ? ['view_deleted' => 1] : []) }}" class="text-danger small text-decoration-none fw-bold" title="Limpiar Filtros">
                <i class="fa-solid fa-times-circle"></i>
            </a>
        </div>
    @endif
</form>
            </div>
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col" class="ps-4">N° Mesa</th>
                            <th scope="col">Escuela</th>
                            @if(Auth::user()->role === 'admin')
                                <th scope="col">Fiscal Asignado</th>
                            @endif
                            <th scope="col" class="text-center">Estado</th>
                            <th scope="col" class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($mesas as $mesa)
                            <tr>
                                <td class="ps-4 fw-bold">
                                    #{{ $mesa->number }}
                                </td>

                                <td>
                                    {{ $mesa->school->name }}
                                    @if($mesa->school->department)
                                        <br><small class="text-muted"><i class="fa-solid fa-map-pin me-1"></i>{{ $mesa->school->department->name }}</small>
                                    @endif
                                </td>

                                @if(Auth::user()->role === 'admin')
                                    <td>
                                        @if($mesa->fiscal)
                                            <div class="d-flex align-items-center">
                                                <div class="bg-light rounded-circle text-center me-2" style="width: 30px; height: 30px; line-height: 30px;">
                                                    <i class="fa-solid fa-user text-secondary"></i>
                                                </div>
                                                {{ $mesa->fiscal->name }}
                                            </div>
                                        @else
                                            <span class="text-muted fst-italic">-- Sin asignar --</span>
                                        @endif
                                    </td>
                                @endif

                                <td class="text-center">
                                    @if(request('view_deleted'))
                                        <span class="badge bg-danger">Eliminada</span>
                                    @else
                                        <span class="badge rounded-pill 
                                            {{ $mesa->status === 'scrutinized' ? 'bg-success' : ($mesa->status === 'asigned' ? 'bg-warning text-dark' : 'bg-secondary') }}">
                                            {{ $mesa->status === 'scrutinized' ? 'Escrutada' : ($mesa->status === 'asigned' ? 'Asignada' : 'Pendiente') }}
                                        </span>
                                    @endif
                                </td>

                                <td class="text-end pe-4">
                                    <div class="btn-group btn-group-sm">
                                        
                                        {{-- MODO PAPELERA: Restaurar --}}
                                        @if(request('view_deleted'))
                                            <form action="{{ route('mesas.restore', $mesa->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-success text-white" title="Restaurar Mesa">
                                                    <i class="fa-solid fa-recycle me-1"></i> Restaurar
                                                </button>
                                            </form>

                                        {{-- MODO NORMAL --}}
                                        @else
                                            
                                            {{-- Editar (Solo Admin) --}}
                                            @if(Auth::user()->role === 'admin')
                                                <a href="{{ route('mesas.edit', $mesa->id) }}" class="btn btn-outline-secondary" title="Editar Configuración">
                                                    <i class="fa-solid fa-pen"></i>
                                                </a>
                                            @endif

                                            {{-- Acción Principal (Cargar / Ver Resultados) --}}
                                            @if($mesa->status === 'scrutinized')
                                                <a href="{{ route('results.show', $mesa->id) }}" class="btn btn-outline-success" title="Ver Resultados">
                                                    <i class="fa-solid fa-square-poll-vertical"></i>
                                                </a>
                                            @else
                                                <a href="{{ route('results.create', $mesa->id) }}" class="btn btn-primary" title="Cargar Votos">
                                                    <i class="fa-solid fa-pen-to-square me-1"></i> Cargar
                                                </a>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ Auth::user()->role === 'admin' ? 5 : 4 }}" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-circle-info fa-2x mb-3"></i><br>
                                    No se encontraron mesas con los filtros actuales.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="card-footer bg-white border-top-0 d-flex justify-content-end py-3">
            {{ $mesas->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection