@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fa-solid fa-pen-to-square me-2"></i>Editar Partido
                    </h5>
                </div>
                
                <div class="card-body">
                    <form id="editPartyForm">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label">Nombre del Partido</label>
                            <input type="text" name="name" class="form-control" 
                                   value="{{ $party->name }}" required>
                        </div>

                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label">Abreviatura / Siglas</label>
                                <input type="text" name="abbreviation" class="form-control" 
                                       value="{{ $party->abbreviation }}" maxlength="10" required>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Color</label>
                                <input type="color" name="color_hex" class="form-control form-control-color w-100" 
                                       value="{{ $party->color_hex }}" title="Elige un color">
                            </div>
                        </div>

                        <div class="d-grid gap-2 mt-3">
                            <button type="button" onclick="actualizarPartido()" class="btn btn-success">
                                <i class="fa-solid fa-save me-2"></i> Guardar Cambios
                            </button>
                            <a href="{{ route('political-parties.index') }}" class="btn btn-secondary">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function actualizarPartido() {
        // 1. Obtenemos el ID del partido desde Blade para usarlo en la URL
        const partyId = "{{ $party->id }}";
        
        const form = document.getElementById('editPartyForm');
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());

        // 2. Usamos PUT para actualizar
        axios.put(`/political-parties/${partyId}`, data)
            .then(response => {
                alert('Partido actualizado correctamente');
                window.location.href = '/political-parties';
            })
            .catch(error => {
                console.error(error);
                if(error.response && error.response.status === 422) {
                    // Muestra errores de validación (ej: nombre ya existe en otro partido)
                    alert('Error de validación: ' + JSON.stringify(error.response.data.errors));
                } else {
                    alert('Ocurrió un error al actualizar.');
                }
            });
    }
</script>
@endsection