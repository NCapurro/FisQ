@extends('layouts.app')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

<style>
    /* Estética armónica para Select2 (estilo Large/Grande) */
    .select2-container--bootstrap-5 .select2-selection {
        min-height: calc(1.5em + 1rem + 2px) !important;
        padding: 0.5rem 1rem !important;
        font-size: 1.25rem !important;
        border-radius: 0.5rem !important;
        border: 1px solid #ced4da;
    }

    /* Ajuste del buscador interno para que sea fluido */
    .select2-container--bootstrap-5 .select2-search__field {
        border-radius: 0.25rem !important;
    }

    /* Colores para las etiquetas de prioridad en el formulario */
    .btn-check:checked + .btn-outline-success { background-color: #198754; color: white; }
    .btn-check:checked + .btn-outline-warning { background-color: #ffc107; color: black; }
    .btn-check:checked + .btn-outline-danger { background-color: #dc3545; color: white; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
    <div class="row">
        <div class="col-lg-4 col-md-5 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-info text-white py-3">
                    <h5 class="mb-0 fw-bold"><i class="fa-solid fa-circle-info me-2"></i>Instrucciones</h5>
                </div>
                <div class="card-body">
                    <p class="text-dark">Siga estos pasos para reportar un problema:</p>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item px-0 border-0">
                            <i class="fa-solid fa-1 text-info me-2"></i> Seleccione o busque la <strong>Mesa</strong>.
                        </li>
                        <li class="list-group-item px-0 border-0">
                            <i class="fa-solid fa-2 text-info me-2"></i> Elija el nivel de <strong>Urgencia</strong>.
                        </li>
                        <li class="list-group-item px-0 border-0">
                            <i class="fa-solid fa-3 text-info me-2"></i> Describa brevemente el inconveniente.
                        </li>
                    </ul>
                    <div class="alert alert-light border mt-3 mb-0 small">
                        Un coordinador revisará su reporte a la brevedad.
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8 col-md-7">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-danger text-white py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="fa-solid fa-triangle-exclamation me-2"></i>Reportar Incidencia
                    </h5>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('incidents.store') }}" method="POST" id="incidentForm">
                        @csrf

                        <div class="mb-4">
                            <label for="mesa_id" class="form-label fw-bold text-secondary">
                                <i class="fa-solid fa-box-archive me-1"></i> Mesa afectada
                            </label>
                            <select name="mesa_id" id="select-mesa" class="form-select @error('mesa_id') is-invalid @enderror">
                                <option value="">Escriba el número de mesa...</option>
                                @foreach($mesas as $m)
                                    <option value="{{ $m->id }}" {{ (isset($mesa_id) && $mesa_id == $m->id) || old('mesa_id') == $m->id ? 'selected' : '' }}>
                                        Mesa N° {{ $m->number }} - {{ $m->school->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('mesa_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-secondary d-block">
                                <i class="fa-solid fa-signal me-1"></i> Nivel de Urgencia
                            </label>
                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="priority" id="p_baja" value="baja">
                                <label class="btn btn-outline-success py-2" for="p_baja">Baja</label>

                                <input type="radio" class="btn-check" name="priority" id="p_media" value="media" checked>
                                <label class="btn btn-outline-warning py-2" for="p_media">Media</label>

                                <input type="radio" class="btn-check" name="priority" id="p_alta" value="alta">
                                <label class="btn btn-outline-danger py-2" for="p_alta">Alta</label>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label fw-bold text-secondary">
                                <i class="fa-solid fa-comment-dots me-1"></i> Descripción del problema
                            </label>
                            <textarea name="description" id="description" rows="4" 
                                class="form-control @error('description') is-invalid @enderror" 
                                placeholder="Ej: Faltan boletas, inconvenientes con autoridades de mesa...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-danger btn-lg fw-bold">
                                <i class="fa-solid fa-paper-plane me-2"></i>ENVIAR REPORTE
                            </button>
                            <a href="{{ url()->previous() }}" class="btn btn-link text-secondary text-decoration-none text-center">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        // Inicialización de Select2 con tema Bootstrap 5
        $('#select-mesa').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Escriba el nro de mesa...',
            allowClear: true,
            language: {
                noResults: function() { return "No se encontró la mesa"; }
            }
        });

        // TRUCO: Cuando se abre el Select2, enfoca el buscador inmediatamente.
        // Esto permite que el usuario empiece a escribir sin clicks extras.
        $(document).on('select2:open', () => {
            document.querySelector('.select2-search__field').focus();
        });
    });
</script>
@endpush