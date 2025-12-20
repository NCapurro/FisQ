@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fa-solid fa-school me-2"></i>Gestión de Escuelas</h2>

        <div class="d-flex gap-2">
            <a href="{{ route('schools.create') }}" class="btn btn-primary">
                <i class="fa-solid fa-plus me-1"></i> Nueva Escuela
            </a>
            
            @if(request('view_deleted'))
                <a href="{{ route('schools.index') }}" class="btn btn-secondary">
                    <i class="fa-solid fa-arrow-left me-1"></i> Volver a Activas
                </a>
            @else
                <a href="{{ route('schools.index', ['view_deleted' => 1]) }}" class="btn btn-outline-danger">
                    <i class="fa-solid fa-trash-can me-1"></i> Papelera
                </a>
            @endif
        </div>
    </div>

    <div class="card mb-4 border-0 shadow-sm bg-light">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('schools.index') }}" id="filterForm" class="row align-items-center gx-2">
                
                {{-- Mantiene el estado de la papelera al filtrar --}}
                @if(request('view_deleted'))
                    <input type="hidden" name="view_deleted" value="1">
                @endif

                <div class="col-auto">
                    <label class="fw-bold text-secondary m-0"><i class="fa-solid fa-filter me-1"></i> Filtros:</label>
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

                <div class="col-md-4">
                    <div class="input-group input-group-sm">
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Buscar escuela...">
                        <button type="submit" class="btn btn-secondary"><i class="fa-solid fa-magnifying-glass"></i></button>
                    </div>
                </div>

                @if(request('department_id') || request('search'))
                    <div class="col-auto">
                        <a href="{{ route('schools.index', request('view_deleted') ? ['view_deleted' => 1] : []) }}" class="text-danger small text-decoration-none fw-bold">
                            <i class="fa-solid fa-times-circle"></i> Limpiar
                        </a>
                    </div>
                @endif
            </form>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col" class="ps-4">Nombre</th>
                            <th scope="col">Dirección</th>
                            <th scope="col">Departamento</th>
                            <th scope="col" class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($schools as $school)
                            <tr>
                                <td class="ps-4 fw-bold">
                                    {{ $school->name }}
                                </td>
                                <td class="text-muted small">
                                    @if($school->address)
                                        <i class="fa-solid fa-location-dot me-1 text-secondary"></i> {{ $school->address }}
                                    @else
                                        <span class="fst-italic text-muted">Sin dirección</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-info text-dark">
                                        {{ $school->department->name ?? 'Sin Dpto' }}
                                    </span>
                                </td>
                                
                                <td class="text-end pe-4">
                                    @if(request('view_deleted'))
                                        <form method="POST" action="{{ route('schools.restore', $school->id) }}">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success text-white" title="Restaurar">
                                                <i class="fa-solid fa-recycle me-1"></i> Restaurar
                                            </button>
                                        </form>
                                    
                                    @else
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('schools.edit', $school->id) }}" class="btn btn-outline-primary" title="Editar">
                                                <i class="fa-solid fa-pen"></i>
                                            </a>
                                            <button type="button" onclick="eliminarEscuela({{ $school->id }}, '{{ $school->name }}')" class="btn btn-outline-danger" title="Eliminar">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-school-circle-xmark fa-2x mb-3"></i><br>
                                    No se encontraron escuelas con los filtros actuales.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer bg-white border-top-0 d-flex justify-content-end py-3">
            {{ $schools->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function eliminarEscuela(id, nombre) {
        if (!confirm('¿Estás seguro de que quieres eliminar la escuela "' + nombre + '"?')) {
            return;
        }

        axios.delete('/schools/' + id)
            .then(response => {
                // Alerta nativa simple o puedes usar SweetAlert si prefieres
                alert('Escuela eliminada correctamente');
                window.location.reload(); 
            })
            .catch(error => {
                console.error(error);
                alert('Ocurrió un error al intentar eliminar.');
            });
    }
</script>
@endpush