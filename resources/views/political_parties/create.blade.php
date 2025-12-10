@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fa-solid fa-flag me-2"></i>Nuevo Partido Político</h5>
                </div>
                
                <div class="card-body">
                    <form id="createPartyForm">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label">Nombre del Partido</label>
                            <input type="text" name="name" class="form-control" placeholder="Ej: Unión por la Patria" required>
                        </div>

                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label">Abreviatura / Siglas</label>
                                <input type="text" name="abbreviation" class="form-control" placeholder="Ej: UP" maxlength="10" required>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Color Distintivo</label>
                                <input type="color" name="color_hex" class="form-control form-control-color w-100" value="#563d7c" title="Elige un color">
                            </div>
                        </div>

                        <div class="d-grid gap-2 mt-3">
                            <button type="button" onclick="guardarPartido()" class="btn btn-success">
                                <i class="fa-solid fa-save me-2"></i> Guardar Partido
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
    function guardarPartido() {
        const form = document.getElementById('createPartyForm');
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());

        axios.post('/political-parties', data)
            .then(response => {
                // Mensaje de éxito simple
                alert('Partido Político creado exitosamente');
                window.location.href = '/political-parties';
            })
            .catch(error => {
                console.error(error);
                if(error.response && error.response.status === 422) {
                    // Si falla la validación (ej: nombre repetido)
                    alert('Error: ' + JSON.stringify(error.response.data.errors));
                } else {
                    alert('Ocurrió un error al guardar.');
                }
            });
    }
</script>
@endsection