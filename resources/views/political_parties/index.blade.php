@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fa-solid fa-flag me-2"></i>Partidos Políticos</h2>
        
        <div class="d-flex gap-2">
            <a href="{{ route('political-parties.create') }}" class="btn btn-primary">
                <i class="fa-solid fa-plus me-1"></i> Nuevo Partido
            </a>

            @if(request('view_deleted'))
                <a href="{{ route('political-parties.index') }}" class="btn btn-secondary">
                    <i class="fa-solid fa-arrow-left me-1"></i> Volver a Activos
                </a>
            @else
                <a href="{{ route('political-parties.index', ['view_deleted' => 1]) }}" class="btn btn-outline-danger">
                    <i class="fa-solid fa-trash-can me-1"></i> Papelera
                </a>
            @endif
        </div>
    </div>
</div>

    
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 10%;">Color</th>
                        <th>Nombre</th>
                        <th>Abreviatura</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($parties as $party)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle border" 
                                         style="width: 30px; height: 30px; background-color: {{ $party->color_hex }};">
                                    </div>
                                   
                                </div>
                            </td>
                            <td class="fw-bold">{{ $party->name }}</td>
                            <td><span class="badge" style="background-color: {{ $party->color_hex }};">{{ $party->abbreviation }}</span></td>
                            <td class="text-end">
                            @if(request('view_deleted'))
                                <form method="POST" action="{{ route('political-parties.restore', $party->id) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success">
                                        <i class="fa-solid fa-recycle"></i> Restaurar
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('political-parties.edit', $party->id) }}" class="btn btn-sm btn-outline-primary me-1">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                <button onclick="eliminarPartido({{ $party->id }}, '{{$party->name}}')" class="btn btn-sm btn-danger">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">
                                <i class="fa-regular fa-folder-open fa-2x mb-2"></i><br>
                                No hay partidos políticos registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
@push('scripts')

<script>
    function eliminarPartido(id, nombre) {
        // 1. Preguntar confirmación
        if (!confirm('¿Estás seguro de que quieres eliminar el partido "' + nombre + '"?')) {
            return; // Si dice que no, no hacemos nada
        }

        // 2. Llamada a Axios (DELETE)
        axios.delete('/political-parties/' + id)
            .then(response => {
                // 3. Éxito: Mostrar alerta
                alert('Partido eliminado con éxito');
                
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

@endpush