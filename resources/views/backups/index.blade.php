@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2><i class="fa-solid fa-database me-2 text-primary"></i>Copias de Seguridad</h2>
          
        </div>
        
        {{-- Formulario para crear backup --}}
        <form action="{{ route('backups.create') }}" method="POST" id="createBackupForm">
            @csrf
            <button type="button" class="btn btn-primary shadow-sm" id="btnCreate" onclick="crearBackup()">
                <span id="btnText"><i class="fa-solid fa-plus me-1"></i> Generar Nuevo Backup</span>
                <span id="btnSpinner" class="d-none">
                    <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                    Generando...
                </span>
            </button>
        </form>
    </div>

    {{-- Alertas --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-triangle-exclamation me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Tabla de Listado --}}
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Archivo</th>
                            <th>Tamaño</th>
                            <th>Fecha</th>
                            <th>Antigüedad</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($backups as $backup)
                            <tr>
                                {{-- 1. Nombre del Archivo --}}
                                <td class="ps-4 fw-bold text-dark">
                                    <i class="fa-solid fa-file-code me-2 text-secondary"></i>
                                    {{ $backup['name'] }}
                                </td>

                                {{-- 2. Tamaño --}}
                                <td>
                                    <span class="badge bg-light text-dark border">{{ $backup['size'] }}</span>
                                </td>

                                {{-- 3. Fecha (String directo del controlador) --}}
                                <td>{{ $backup['date'] }}</td>

                                {{-- 4. Antigüedad (Parseamos el string a Carbon para usar diffForHumans) --}}
                                <td class="text-muted small">
                                    {{ \Carbon\Carbon::parse($backup['date'])->diffForHumans() }}
                                </td>

                                {{-- 5. Botones de Acción --}}
                                <td class="text-end pe-4">
                                    <div class="btn-group btn-group-sm">
                                        {{-- Descargar --}}
                                        <a href="{{ route('backups.download', $backup['name']) }}" class="btn btn-outline-primary" title="Descargar">
                                            <i class="fa-solid fa-download"></i>
                                        </a>

                                        {{-- Eliminar (Si tienes la ruta definida) --}}
                                        @if(Route::has('backups.delete'))
                                        <form action="{{ route('backups.delete', $backup['name']) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este backup?');" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="Eliminar">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-server fa-2x mb-3 opacity-50"></i><br>
                                    No hay copias de seguridad disponibles.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Script para el Spinner del botón --}}
<script>
    function crearBackup() {
        // Bloqueamos botón y mostramos spinner
        const btn = document.getElementById('btnCreate');
        const txt = document.getElementById('btnText');
        const spin = document.getElementById('btnSpinner');

        if(btn && txt && spin) {
            btn.disabled = true;
            txt.classList.add('d-none');
            spin.classList.remove('d-none');
            // Enviamos form
            document.getElementById('createBackupForm').submit();
        }
    }
</script>
@endsection