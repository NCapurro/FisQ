@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Reporte de Incidentes</h2>
        <div>
            <a href="{{ route('reports.incidentes', request()->all() + ['format' => 'excel']) }}" class="btn btn-success">Excel</a>
            <a href="{{ route('reports.incidentes', request()->all() + ['format' => 'pdf']) }}" class="btn btn-danger" target="_blank">PDF</a>
        </div>
    </div>

    <div class="card mb-4 bg-light">
        <div class="card-body">
            <form method="GET" action="{{ route('reports.incidentes') }}" class="row g-3">
                <div class="col-md-4">
                    <label>Prioridad</label>
                    <select name="priority" class="form-select">
                        <option value="">Todas</option>
                        <option value="alta" {{ request('priority') == 'alta' ? 'selected' : '' }}>Alta</option>
                        <option value="media" {{ request('priority') == 'media' ? 'selected' : '' }}>Media</option>
                        <option value="baja" {{ request('priority') == 'baja' ? 'selected' : '' }}>Baja</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label>Fecha</label>
                    <input type="date" name="fecha" value="{{ request('fecha') }}" class="form-control">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Filtrar</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Fecha</th>
                        <th>Prioridad</th>
                        <th>Ubicación</th>
                        <th>Descripción</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($incidentes as $inc)
                    <tr>
                        <td>{{ $inc->created_at->format('d/m H:i') }}</td>
                        <td>
                            <span class="badge {{ $inc->priority == 'alta' ? 'bg-danger' : ($inc->priority == 'media' ? 'bg-warning text-dark' : 'bg-info') }}">
                                {{ ucfirst($inc->priority) }}
                            </span>
                        </td>
                        <td>
                            @if($inc->mesa)
                                Mesa {{ $inc->mesa->number }}<br>
                                <small class="text-muted">{{ $inc->mesa->school->name }}</small>
                            @else
                                General
                            @endif
                        </td>
                        <td>{{ $inc->description }}</td>
                        <td>
                            @if($inc->is_resolved)
                                <span class="badge bg-success">Resuelto</span>
                            @else
                                <span class="badge bg-secondary">Pendiente</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection