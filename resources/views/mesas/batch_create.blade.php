@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fa-solid fa-layer-group me-2"></i>Creación Masiva de Mesas</h5>
                </div>
                
                <div class="card-body">
                    <form id="batchMesaForm">
                        @csrf
                        
                        <div class="alert alert-info small">
                            <i class="fa-solid fa-circle-info me-1"></i>
                            Esta herramienta generará múltiples mesas automáticamente para una misma escuela.
                            Si una mesa ya existe (ej: la 205), el sistema la saltará y seguirá con la siguiente.
                        </div>

                        <div class="mb-3">
                            <label class="form-label">1. Seleccione Departamento</label>
                            <select id="departmentSelect" class="form-select" onchange="cargarEscuelas()">
                                <option value="">Seleccione...</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">2. Seleccione Escuela</label>
                            <select name="school_id" id="schoolSelect" class="form-select" disabled required>
                                <option value="">Primero elija un departamento...</option>
                            </select>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Desde la Mesa N°</label>
                                <input type="number" name="from" class="form-control" placeholder="Ej: 200" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Hasta la Mesa N°</label>
                                <input type="number" name="to" class="form-control" placeholder="Ej: 450" required>
                            </div>
                        </div>

                        <div class="d-grid gap-2 mt-3">
                            <button type="button" onclick="generarMasivo()" class="btn btn-success">
                                <i class="fa-solid fa-cogs me-2"></i> Generar Mesas
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
    // Función para llenar el segundo select
    function cargarEscuelas() {
        const deptId = document.getElementById('departmentSelect').value;
        const schoolSelect = document.getElementById('schoolSelect');

        // Limpiar opciones anteriores
        schoolSelect.innerHTML = '<option value="">Cargando escuelas...</option>';
        schoolSelect.disabled = true;

        if(!deptId) {
            schoolSelect.innerHTML = '<option value="">Primero elija un departamento...</option>';
            return;
        }

        // Llamada a la API interna que creamos
        axios.get('/api/schools/' + deptId)
            .then(response => {
                const schools = response.data;
                schoolSelect.innerHTML = '<option value="">Seleccione una escuela...</option>';
                
                if(schools.length === 0) {
                    schoolSelect.innerHTML = '<option value="">No hay escuelas en este depto.</option>';
                } else {
                    schools.forEach(school => {
                        const option = document.createElement('option');
                        option.value = school.id;
                        option.text = school.name;
                        schoolSelect.appendChild(option);
                    });
                    schoolSelect.disabled = false; // Habilitar el select
                }
            })
            .catch(error => {
                console.error(error);
                alert('Error al cargar escuelas');
            });
    }

    // Función de guardado masivo
    function generarMasivo() {
        const form = document.getElementById('batchMesaForm');
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());

        if (parseInt(data.from) >= parseInt(data.to)) {
            alert('El número "Desde" debe ser menor que "Hasta".');
            return;
        }

        if(!confirm(`Estás a punto de generar mesas desde la ${data.from} hasta la ${data.to}. ¿Confirmar?`)) {
            return;
        }

        axios.post('/mesas/creacion-masiva', data)
            .then(response => {
                alert(response.data.message);
                window.location.href = '/mesas';
            })
            .catch(error => {
                console.error(error);
                if(error.response && error.response.status === 422) {
                    alert('Error de validación: ' + JSON.stringify(error.response.data.errors));
                } else {
                    alert('Ocurrió un error al generar las mesas.');
                }
            });
    }
</script>
@endpush