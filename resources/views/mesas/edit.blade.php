@extends('layouts.app') 

@section('content') 

{{-- CSS PARA SELECT INTELIGENTE --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />


<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8"> {{-- Esto centra la tarjeta y limita el ancho --}}
            
            {{-- TARJETA DE ESTILO BOOTSTRAP --}}
            <div class="card shadow border-0">
                
                {{-- ENCABEZADO DE COLOR (Usa tu variable --bs-primary) --}}
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0 fw-bold">
                        <i class="fa-solid fa-pen-to-square me-2"></i>Editar Mesa N° {{ $mesa->number }}
                    </h5>
                </div>
                
                <div class="card-body p-4"> {{-- Padding interno para que respire --}}
                    
                    <form id="editMesaForm">
                        @csrf
                        
                        {{-- SECCIÓN 1: DATOS BÁSICOS --}}
                        <h6 class="text-muted text-uppercase small fw-bold mb-3">Información General</h6>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Número</label>
                                <input type="number" name="number" class="form-control" 
                                       value="{{ $mesa->number }}" required 
                                       @disabled(Auth::user()->role !== 'admin')>
                            </div>

                            <div class="col-md-8 mb-3">
                                <label class="form-label fw-bold">Escuela</label>
                                <select name="school_id" class="form-select" required
                                @disabled(Auth::user()->role !== 'admin')>
                                    @foreach($schools as $school)
                                        <option value="{{ $school->id }}" 
                                            {{ $mesa->school_id == $school->id ? 'selected' : '' }}>
                                            {{ $school->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <hr class="my-4">

                        {{-- SECCIÓN 2: PERSONAL --}}
                        <h6 class="text-muted text-uppercase small fw-bold mb-3">Personal Asignado</h6>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Fiscal Responsable</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fa-solid fa-user"></i></span>
                                <select name="user_id" id="select_fiscal" class="form-select" required>
                                <option value=""></option>
                                @foreach($fiscals as $fiscal)
                                    <option value="{{ $fiscal->id }}">
                                        {{ $fiscal->lastname }}, {{ $fiscal->name }} (DNI: {{ $fiscal->dni }})
                                    </option>
                                @endforeach
                            </select>
                            </div>
                        </div>

                       

                        {{-- BOTONES DE ACCIÓN --}}
                        <div class="d-flex justify-content-between mt-4 pt-2 border-top">
                            {{-- Botón Eliminar a la izquierda (rojo suave) --}}
                            <button type="button" onclick="eliminarMesa()" class="btn btn-outline-danger btn-sm">
                                <i class="fa-solid fa-trash me-1"></i> Eliminar Mesa
                            </button>

                            {{-- Botones Guardar/Cancelar a la derecha --}}
                            <div class="d-flex gap-2">
                                <a href="{{ route('mesas.index') }}" class="btn btn-secondary">Cancelar</a>
                                <button type="button" onclick="actualizarMesa()" class="btn btn-primary px-4">
                                    <i class="fa-solid fa-save me-2"></i> Guardar Cambios
                                </button>
                            </div>
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
    const mesaId = "{{ $mesa->id }}";

    function actualizarMesa() {
        const form = document.getElementById('editMesaForm');
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());

        axios.put(`/mesas/${mesaId}`, data)
            .then(response => {
                alert('Mesa actualizada correctamente');
                window.location.href = '/mesas';
            })
            .catch(error => {
                console.error(error);
                alert('Error al actualizar: ' + (error.response?.data?.message || 'Revisa la consola'));
            });
    }

    function eliminarMesa() {
        if(!confirm('¿ATENCIÓN: Eliminar esta mesa borrará todos sus votos. ¿Continuar?')) return;

        axios.delete(`/mesas/${mesaId}`)
            .then(() => {
                alert('Mesa eliminada');
                window.location.href = '/mesas';
            })
            .catch(error => alert('No se pudo eliminar.'));
    }
</script>

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

@endpush