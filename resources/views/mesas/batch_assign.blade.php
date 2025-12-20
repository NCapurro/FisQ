@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fa-solid fa-users-gear me-2"></i>Asignación Masiva de Fiscales</h5>
                </div>
                
                <div class="card-body">
                    <form id="assignMesaForm">
                        @csrf
                        
                        <div class="alert alert-warning small border-warning">
                            <i class="fa-solid fa-triangle-exclamation me-1"></i>
                            <strong>Atención:</strong> Esta acción sobrescribirá cualquier asignación anterior en el rango seleccionado.
                            Las mesas que ya estén "Escrutadas" no se modificarán por seguridad.
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">1. Seleccione Fiscal Responsable</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fa-solid fa-user-tie"></i></span>
                                <select name="user_id" class="form-select" required>
                                    <option value="">Seleccione un fiscal...</option>
                                    @foreach($fiscals as $fiscal)
                                        <option value="{{ $fiscal->id }}">
                                            {{ $fiscal->lastname }}, {{ $fiscal->name }} (DNI: {{ $fiscal->dni }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <hr>

                        <div class="mb-3">
                            <label class="form-label fw-bold">2. Defina el Rango de Mesas</label>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small text-muted">Desde la Mesa N°</label>
                                    <input type="number" name="from" class="form-control" placeholder="Ej: 101" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small text-muted">Hasta la Mesa N°</label>
                                    <input type="number" name="to" class="form-control" placeholder="Ej: 105" required>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 mt-3">
                            <button type="button" onclick="asignarMasivo()" class="btn btn-warning">
                                <i class="fa-solid fa-check-double me-2"></i> Asignar Fiscal a estas Mesas
                            </button>
                            <a href="{{ route('mesas.index') }}" class="btn btn-secondary">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')

<script>
    function asignarMasivo() {
        const form = document.getElementById('assignMesaForm');
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());

        // Validaciones básicas de frontend
        if (!data.user_id) {
            alert('Por favor seleccione un fiscal.');
            return;
        }
        if (parseInt(data.from) >= parseInt(data.to)) {
            alert('El número "Desde" debe ser menor que "Hasta".');
            return;
        }

        if(!confirm(`Se asignarán las mesas ${data.from} a ${data.to} al fiscal seleccionado. ¿Confirmar?`)) {
            return;
        }

        axios.post('/mesas/asignacion-masiva', data)
            .then(response => {
                alert(response.data.message);
                window.location.href = '/mesas';
            })
            .catch(error => {
                console.error(error);
                if(error.response) {
                     // Si el servidor devuelve mensaje (ej: error 422), lo mostramos
                    alert('Error: ' + (error.response.data.message || JSON.stringify(error.response.data.errors)));
                } else {
                    alert('Ocurrió un error de conexión.');
                }
            });
    }
</script>
@endpush