@extends('layouts.app')

@section('content')

{{-- CARGA DE CSS --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-warning text-dark fw-bold">
                    <i class="fa-solid fa-user-tag me-2"></i> Asignar Mesa N° {{ $mesa->number }}
                </div>
                
                <div class="card-body">
                    <div class="alert alert-info mb-4">
                        <i class="fa-solid fa-circle-info me-2"></i>
                        Estás reasignando la mesa de la escuela: <strong>{{ $mesa->school->name }}</strong>.
                        <br>Solo se muestran fiscales del departamento: <strong>{{ $mesa->school->department->name }}</strong>.
                    </div>

                    <form action="{{ route('mesas.update_assignment', $mesa->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="select_fiscal" class="form-label fw-bold">Seleccionar Nuevo Fiscal</label>
                            
                            
                            <select name="user_id" id="select_fiscal" class="form-select" required>
                                <option value=""></option>
                                @foreach($fiscals as $fiscal)
                                    <option value="{{ $fiscal->id }}">
                                        {{ $fiscal->lastname }}, {{ $fiscal->name }} (DNI: {{ $fiscal->dni }})
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">Escribe el nombre, apellido o DNI para buscar en la lista.</div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('mesas.index') }}" class="btn btn-secondary">
                                <i class="fa-solid fa-arrow-left me-1"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-warning fw-bold">
                                <i class="fa-solid fa-save me-1"></i> Confirmar Asignación
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- CARGA DE JS --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/i18n/es.js"></script>

<script>
    // Usamos jQuery standard
    $(document).ready(function() {
        $('#select_fiscal').select2({
            theme: 'bootstrap-5',
            language: "es",
            placeholder: 'Escribe para buscar un fiscal...',
            width: '100%',
            allowClear: true
        });
    });
</script>
@endsection