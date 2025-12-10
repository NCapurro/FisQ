@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fa-solid fa-pen-to-square me-2"></i>Editar Escuela
                    </h5>
                </div>
                
                <div class="card-body">
                    <form id="editSchoolForm">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Nombre de la Escuela</label>
                            <input type="text" name="name" class="form-control" 
                                   value="{{ $school->name }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Dirección</label>
                            <input type="text" name="address" class="form-control" 
                                   value="{{ $school->address }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Departamento / Ubicación</label>
                            <select name="department_id" class="form-select" required>
                                <option value="">Seleccione una zona...</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}" 
                                        {{ $school->department_id == $dept->id ? 'selected' : '' }}>
                                        {{ $dept->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">Cambiar esto moverá la escuela a otra zona.</div>
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            <button type="button" onclick="actualizarEscuela()" class="btn btn-success">
                                <i class="fa-solid fa-save me-2"></i> Guardar Cambios
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
    function actualizarEscuela() {
        // Obtenemos el ID desde Blade
        const schoolId = "{{ $school->id }}";
        
        const form = document.getElementById('editSchoolForm');
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());

        // Usamos PUT para actualizar
        axios.put(`/schools/${schoolId}`, data)
            .then(response => {
                alert('Escuela actualizada correctamente');
                window.location.href = '/schools';
            })
            .catch(error => {
                console.error(error);
                if(error.response && error.response.status === 422) {
                    alert('Error de validación: ' + JSON.stringify(error.response.data.errors));
                } else {
                    alert('Ocurrió un error al actualizar la escuela.');
                }
            });
    }
</script>
@endsection