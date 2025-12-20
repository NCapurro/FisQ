@extends('layouts.app')

@section('content')
<div class="container">
    <h2><i class="fa-solid fa-list-check me-2"></i>Auditoría del Sistema</h2>
    
    <table class="table table-striped mt-4">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Usuario</th>
                <th>Módulo</th>
                <th>Acción</th>
                <th>Descripción</th>
            </tr>
        </thead>
        <tbody>
            @foreach($logs as $log)
                <tr>
                    <td>{{ $log->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        @if($log->user)
                            <span class="fw-bold">{{ $log->user->name }}</span>
                        @else
                            <span class="text-muted">Sistema / Usuario Borrado</span>
                        @endif
                    </td>
                    <td><span class="badge bg-secondary">{{ $log->module }}</span></td>
                    <td>
                        @if($log->action == 'crear') <span class="text-success fw-bold">Creación</span>
                        @elseif($log->action == 'eliminar') <span class="text-danger fw-bold">Eliminación</span>
                        @elseif($log->action == 'restaurar') <span class="text-info fw-bold">Restauración</span>
                        @else {{ $log->action }} @endif
                    </td>
                    <td>{{ $log->description }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Paginación --}}
    <div class="d-flex justify-content-center">
        {{ $logs->links() }}
    </div>
</div>
@endsection