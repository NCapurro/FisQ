@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fa-solid fa-school me-2"></i>Nueva Escuela</h5>
                </div>
                
                <div class="card-body">
                    <form id="createSchoolForm">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label">Nombre de la Escuela</label>
                            <input type="text" name="name" class="form-control" placeholder="Ej: Escuela Normal N° 5" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Dirección (Opcional)</label>
                            <input type="text" name="address" class="form-control" placeholder="Ej: Calle San Martín 123">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Departamento / Ubicación</label>
                            <select name="department_id" class="form-select" required>
                                <option value="">Seleccione una zona...</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                @endforeach
                            </select>
                            <div class="form-text">La escuela se vinculará a esta zona geográfica.</div>
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            <button type="button" onclick="guardarEscuela()" class="btn btn-success">
                                <i class="fa-solid fa-save me-2"></i> Guardar Escuela
                            </button>
                            <a href="{{ route('schools.index') }}" class="btn btn-secondary">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function guardarEscuela() {
        const form = document.getElementById('createSchoolForm');
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());

        axios.post('/schools', data)
            .then(response => {
                alert('Escuela creada correctamente');
                window.location.href = '/schools';
            })
            .catch(error => {
                console.error(error);
                if(error.response && error.response.status === 422) {
                    // Muestra error si falta el nombre o el departamento
                    alert('Error de validación: ' + JSON.stringify(error.response.data.errors));
                } else {
                    alert('Ocurrió un error al guardar la escuela.');
                }
            });
    }
</script>
@endsection