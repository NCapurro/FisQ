@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fa-solid fa-box-archive me-2"></i>Nueva Mesa</h5>
                </div>
                
                <div class="card-body">
                    <form id="createMesaForm">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label">Número de Mesa</label>
                            <input type="number" name="number" class="form-control" placeholder="Ej: 101" required>
                            <div class="form-text">Debe ser un número único en todo el sistema.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Escuela</label>
                            <select name="school_id" class="form-select" required>
                                <option value="">Seleccione una escuela...</option>
                                @foreach($schools as $school)
                                    <option value="{{ $school->id }}">
                                        {{ $school->name }} ({{ $school->department->name }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            <button type="button" onclick="guardarMesa()" class="btn btn-success">
                                <i class="fa-solid fa-save me-2"></i> Guardar Mesa
                            </button>
                            <a href="{{ route('mesas.index') }}" class="btn btn-secondary">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function guardarMesa() {
        const form = document.getElementById('createMesaForm');
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());

        axios.post('/mesas', data)
            .then(response => {
                alert('Mesa creada exitosamente');
                window.location.href = '/mesas';
            })
            .catch(error => {
                console.error(error);
                if(error.response && error.response.status === 422) {
                    alert('Error: ' + JSON.stringify(error.response.data.errors));
                } else {
                    alert('Error al guardar la mesa.');
                }
            });
    }
</script>
@endsection