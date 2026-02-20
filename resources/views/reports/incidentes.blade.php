@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Reporte de Incidentes</h2>
        <div>
              <a href="{{ route('reports.incidentes', request()->all() + ['format' => 'excel']) }}" class="btn btn-outline-success shadow-sm">
        <i class="fa-solid fa-file-excel me-1"></i> Excel
    </a>
    <a href="{{ route('reports.incidentes', request()->all() + ['format' => 'pdf']) }}" class="btn btn-outline-danger shadow-sm">
        <i class="fa-solid fa-file-pdf me-1"></i> PDF
    </a>
</div>
    </div>

    <div class="card mb-4 bg-light">
        <div class="card-body">
            <form method="GET" action="{{ route('reports.incidentes') }}" class="row g-3">
    <div class="col-md-3">
        <label class="form-label text-muted small fw-bold text-uppercase mb-1">Prioridad</label>
        <select name="priority" class="form-select shadow-sm border-0">
            <option value="">Todas</option>
            <option value="alta" {{ request('priority') == 'alta' ? 'selected' : '' }}>Alta</option>
            <option value="media" {{ request('priority') == 'media' ? 'selected' : '' }}>Media</option>
            <option value="baja" {{ request('priority') == 'baja' ? 'selected' : '' }}>Baja</option>
        </select>
    </div>

    <div class="col-md-3">
        <label class="form-label text-muted small fw-bold text-uppercase mb-1">Estado</label>
        <select name="status" class="form-select shadow-sm border-0">
            <option value="">Todos</option>
            <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Resueltos</option>
            <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>No Resueltos</option>
        </select>
    </div>

    <div class="col-md-3">
        <label class="form-label text-muted small fw-bold text-uppercase mb-1">Fecha</label>
        <input type="date" name="fecha" value="{{ request('fecha') }}" class="form-control shadow-sm border-0">
    </div>

    <div class="col-md-3 d-flex align-items-end gap-2">
        <button type="submit" class="btn btn-primary w-100 shadow-sm">
            <i class="fa-solid fa-filter me-1"></i> Filtrar
        </button>
        <a href="{{ route('reports.incidentes') }}" class="btn btn-outline-secondary shadow-sm px-3" title="Limpiar Filtros">
            <i class="fa-solid fa-xmark"></i>
        </a>
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
                        <td class="text-center align-middle">
    <div class="d-flex align-items-center justify-content-center">
        @if($inc->is_resolved)
            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2">
                <i class="fa-solid fa-check-double me-1"></i> Resuelto
            </span>
        @else
            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-3 py-2">
                <i class="fa-solid fa-clock me-1"></i> Pendiente
            </span>

            <button type="button" 
                class="btn btn-sm btn-success shadow-sm ms-3 px-3 fw-bold btn-hover-scale" 
                data-url="{{ route('incidents.mark_resolved', $inc->id) }}"
                onclick="markAsResolved(this)"
                title="Marcar como resuelto">
                <i class="fa-solid fa-check me-1"></i> Resolver
            </button>
        @endif
    </div>
</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    {{-- Agregamos la Paginación aquí abajo --}}
    <div class="mt-4 d-flex justify-content-center">
        {{ $incidentes->appends(request()->query())->links() }}
    </div>
</div>
@endsection


@push('scripts')
<script>
    function markAsResolved(element) {
        const url = element.getAttribute('data-url');
        
        if (!confirm('¿Confirmas que esta incidencia ha sido solucionada?')) return;

        // Enviamos un POST simple, ya que así lo definimos en web.php
        axios.post(url)
            .then(response => {
                window.location.reload();
            })
            .catch(error => {
                console.error('Error detallado:', error);
                alert('Ocurrió un error. Revisa la consola (F12).');
            });
    }
</script>
@endpush