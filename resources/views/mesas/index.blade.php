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
                <form method="GET" action="{{ route('mesas.index') }}" id="filterForm" class="row align-items-center">
                    <div class="col-auto">
                        <label class="fw-bold text-secondary m-0"><i class="fa-solid fa-filter me-1"></i> Filtrar por:</label>
                    </div>
                    <div class="col-md-4">
                        <select name="department_id" class="form-select form-select-sm" onchange="document.getElementById('filterForm').submit()">
                            <option value="">-- Todos los Departamentos --</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    @if(request('department_id'))
                        <div class="col-auto">
                            <a href="{{ route('mesas.index') }}" class="text-decoration-none text-muted small">
                                <i class="fa-solid fa-times-circle"></i> Limpiar filtro
                            </a>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    @endif

    <div class="row">
        @forelse($mesas as $mesa)
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm h-100 border-start border-5 
                    {{ $mesa->status === 'scrutinized' ? 'border-success' : ($mesa->status === 'asigned' ? 'border-warning' : 'border-secondary') }}">
                    
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <h5 class="card-title fw-bold">Mesa N° {{ $mesa->number }}</h5>
                            <div>
                                @if(request('view_deleted'))
                                    <form action="{{ route('mesas.restore', $mesa->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success text-white me-2" title="Restaurar Mesa">
                                            <i class="fa-solid fa-recycle"></i> 
                                        </button>
                                    </form>
                                @else
                                    @if(Auth::user()->role === 'admin')
                                        <a href="{{ route('mesas.edit', $mesa->id) }}" class="text-secondary me-2">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                    @endif
                                @endif
                                <span class="badge {{ $mesa->status === 'scrutinized' ? 'bg-success' : ($mesa->status === 'asigned' ? 'bg-warning text-dark' : 'bg-secondary') }}">
                                    {{ $mesa->status === 'scrutinized' ? 'Escrutada' : ($mesa->status === 'asigned' ? 'Asignada' : 'Pendiente') }}
                                </span>
                            </div>
                        </div>
                        @if(request('view_deleted'))
                            <div class="alert alert-warning mt-2">
                                <i class="fa-solid fa-triangle-exclamation me-2"></i>
                                Esta mesa está en la papelera.
                            </div>
                        @else
                            <p class="card-text text-muted mb-1">
                                <i class="fa-solid fa-school me-1"></i> {{ $mesa->school->name }}
                            </p>

                            @if(Auth::user()->role === 'admin')
                                <small class="text-muted d-block mb-2">
                                    <i class="fa-solid fa-user-tie me-1"></i> Fiscal: {{ $mesa->fiscal->name ?? 'Sin asignar' }}
                                </small>
                            @endif

                            <hr>

                            <div class="d-grid gap-2">
                                @if($mesa->status === 'scrutinized')
                                    <a href="{{ route('results.show', $mesa->id) }}" class="btn btn-outline-success">
                                        <i class="fa-solid fa-square-poll-vertical me-1"></i> Ver Resultados
                                    </a>
                                @else
                                    <a href="{{ route('results.create', $mesa->id) }}" class="btn btn-primary">
                                        <i class="fa-solid fa-pen-to-square me-1"></i> Cargar Votos
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info text-center">
                    <i class="fa-solid fa-circle-info me-2"></i>
                    No hay mesas asignadas a tu usuario todavía.
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection