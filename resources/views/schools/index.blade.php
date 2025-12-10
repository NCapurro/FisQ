@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fa-solid fa-school me-2"></i>Escuelas</h2>
        <a href="{{ route('schools.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-plus me-1"></i> Nueva Escuela
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nombre</th>
                        <th>Dirección</th>
                        <th>Departamento / Zona</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($schools as $school)
                        <tr>
                            <td class="fw-bold">{{ $school->name }}</td>
                            <td class="text-muted small">
                                <i class="fa-solid fa-location-dot me-1"></i>
                                {{ $school->address ?? 'Sin dirección registrada' }}
                            </td>
                            <td>
                                <span class="badge bg-info text-dark">
                                    {{ $school->department->name }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="d-flex gap-2 justify-content-end">
                                    <a href="{{ route('schools.edit', $school->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    <button type="button" onclick="eliminarEscuela({{ $school->id }}, '{{ $school->name }}')" class="btn btn-sm btn-danger">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-school-circle-xmark fa-2x mb-3"></i><br>
                                No hay escuelas cargadas todavía.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>



<script>
    function eliminarEscuela(id, nombre) {
        // 1. Preguntar confirmación
        if (!confirm('¿Estás seguro de que quieres eliminar la escuela "' + nombre + '"?')) {
            return; // Si dice que no, no hacemos nada
        }

        // 2. Llamada a Axios (DELETE)
        axios.delete('/schools/' + id)
            .then(response => {
                // 3. Éxito: Mostrar alerta
                alert('Escuela eliminada con éxito');
                
                // 4. Recargar la página para ver que ya no está
                // (Como es rápido, parecerá que solo desapareció)
                window.location.reload(); 
            })
            .catch(error => {
                console.error(error);
                alert('Ocurrió un error al intentar eliminar.');
            });
    }
</script>
@endsection