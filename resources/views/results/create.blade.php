@extends('layouts.app')

@section('content')
<div class="container-fluid px-4"> {{-- Usamos container-fluid para más espacio --}}
    <div class="row">
        <div class="col-lg-4 col-md-5 mb-4">
            <div class="card shadow-sm border-0 ">
                <div class="card-header bg-info text-white py-3">
                    <h5 class="mb-0 fw-bold"><i class="fa-solid fa-circle-info me-2"></i>Instrucciones</h5>
                </div>
                <div class="card-body">
                    <p class="text-dark">Por favor, siga estos pasos para asegurar la validez de los datos:</p>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item px-0 border-0">
                            <i class="fa-solid fa-check text-success me-2"></i> 
                            Copie los valores tal cual figuran en el <strong>acta oficial</strong>.
                        </li>
                        <li class="list-group-item px-0 border-0">
                            <i class="fa-solid fa-calculator text-primary me-2"></i> 
                            El sistema sumará automáticamente el "Total General".
                        </li>
                        <li class="list-group-item px-0 border-0">
                            <i class="fa-solid fa-triangle-exclamation text-warning me-2"></i> 
                            <strong>Validación:</strong> El total de votos no puede superar el padrón de la mesa.
                        </li>
                        <li class="list-group-item px-0 border-0">
                            <i class="fa-solid fa-camera text-secondary me-2"></i> 
                            Asegúrese de que la foto adjunta sea legible.
                        </li>
                    </ul>

                    {{-- BOTÓN PARA REPORTAR INCIDENCIA --}}
            <div class="d-grid gap-2 mt-2">
                <a href="{{ route('incidents.create', ['mesa_id' => $mesa->id]) }}" class="btn btn-outline-danger fw-bold shadow-sm">
                    <i class="fa-solid fa-triangle-exclamation me-2"></i>¿PROBLEMAS CON ESTA MESA?
                </a>
            </div>
                    
                    <div class="mt-4 p-3 bg-light rounded border border-info">
                        <small class="text-muted d-block italic">Nota:</small>
                        <small>Si ya cargó datos anteriormente, al guardar se sobrescribirán con los valores actuales.</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8 col-md-7">
            <div class="card shadow border-0">
                <div class="card-header bg-primary text-white text-center py-3">
                    <h4 class="mb-0 fw-bold"><i class="fa-solid fa-file-pen me-2"></i>Planilla de Escrutinio</h4>
                    <p class="mb-0 small opacity-75">
                        Mesa N° {{ $mesa->number }} - {{ $mesa->school->name }}
                    </p>
                </div>
                
                <div class="card-body p-4">
                    <form id="resultsForm">
                        @csrf
                        
                        <input type="hidden" name="latitude" id="lat">
                        <input type="hidden" name="longitude" id="lng">

                        {{-- INPUT: ELECTORES TOTALES --}}
                        <div class="card mb-4 border-warning shadow-sm">
                            <div class="card-body bg-warning bg-opacity-10 d-flex justify-content-between align-items-center">
                                <div>
                                    <label for="electoresInput" class="fw-bold text-dark mb-1">
                                        <i class="fa-solid fa-users me-2"></i>Electores Habilitados (Padrón)
                                    </label>
                                    <small class="d-block text-muted">Límite máximo permitido para esta mesa.</small>
                                </div>
                                <div style="width: 120px;">
                                    <input type="number" 
                                           name="electores_totales" 
                                           id="electoresInput"
                                           class="form-control form-control-lg text-center fw-bold border-warning"
                                           value="{{ $mesa->electores_totales ?? 350 }}" 
                                           min="0"
                                           oninput="calcularTotal()"> {{-- Llamamos aquí también para validar en tiempo real --}}
                                </div>
                            </div>
                        </div>

                        <div class="list-group list-group-flush mb-4">
                            @foreach($parties as $party)
                                @php
                                    $prevVote = $mesa->results->where('political_party_id', $party->id)->first();
                                    $val = $prevVote ? $prevVote->votes : '';
                                @endphp

                                <div class="list-group-item py-3 px-2 border-bottom">
                                    <div class="row align-items-center g-3">
                                        <div class="col-8 d-flex align-items-center">
                                            <div class="rounded-circle shadow-sm me-3 flex-shrink-0" 
                                                 style="width: 40px; height: 40px; background-color: {{ $party->color_hex }}; border: 2px solid #fff;">
                                            </div>
                                            <div>
                                                <h6 class="mb-0 fw-bold text-dark">{{ $party->name }}</h6>
                                                <small class="text-muted">{{ $party->abbreviation }}</small>
                                            </div>
                                        </div>
                                        
                                        <div class="col-4">
                                            <input type="number" 
                                                   name="votes[{{ $party->id }}]" 
                                                   class="form-control form-control-lg text-center fw-bold vote-input" 
                                                   value="{{ $val }}" 
                                                   min="0" 
                                                   placeholder="0"
                                                   oninput="calcularTotal()"
                                                   onfocus="this.select()">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="card bg-light border-0 mb-4 shadow-sm" id="totalCard">
                            <div class="card-body d-flex justify-content-between align-items-center px-4">
                                <span class="fw-bold text-uppercase text-secondary">Total General de Votos</span>
                                <span id="displayTotal" class="display-6 fw-bold text-primary">0</span>
                            </div>
                        </div>

                        {{-- FOTO DEL ACTA --}}
                        <div class="card bg-light border-0 mb-4">
                            <div class="card-body">
                                <h6 class="fw-bold text-uppercase text-muted mb-3">
                                    <i class="fa-solid fa-camera me-2"></i>Foto del Acta Oficial
                                </h6>
                                <input type="file" name="photo" id="photoInput" class="form-control" accept="image/*" capture="environment">
                                <div id="photoPreviewContainer" class="mt-3 d-none text-center">
                                    <img id="photoPreview" src="" class="img-fluid rounded shadow-sm" style="max-height: 300px;">
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="button" id="btnGuardar" onclick="enviarEscrutinio()" class="btn btn-success btn-lg fw-bold">
                                <i class="fa-solid fa-save me-2"></i> GUARDAR RESULTADOS
                            </button>
                            <a href="{{ route('mesas.index') }}" class="btn btn-outline-secondary">Cancelar</a>
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
    document.addEventListener('DOMContentLoaded', function() {
        
        // 1. Inicializamos funciones existentes
        calcularTotal();
        obtenerUbicacion();

        // 2. Lógica de Previsualización de Imagen
        const photoInput = document.getElementById('photoInput');
        const previewContainer = document.getElementById('photoPreviewContainer');
        const previewImg = document.getElementById('photoPreview');

        // Verificamos que el input exista para evitar errores
        if (photoInput) {
            photoInput.addEventListener('change', function(event) {
                const file = this.files[0]; // 'this' es el input

                if (file) {
                    const reader = new FileReader();

                    // Cuando termine de leer el archivo...
                    reader.onload = function(e) {
                        // ...asignamos el resultado a la imagen
                        previewImg.src = e.target.result;
                        // ...y mostramos el contenedor quitando la clase d-none
                        previewContainer.classList.remove('d-none');
                    }

                    // Leemos el archivo como una URL de datos (Base64)
                    reader.readAsDataURL(file);
                } else {
                    // Si el usuario cancela la selección, ocultamos la previa
                    previewImg.src = "";
                    previewContainer.classList.add('d-none');
                }
            });
        }
    });

    function obtenerUbicacion() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    document.getElementById('lat').value = pos.coords.latitude;
                    document.getElementById('lng').value = pos.coords.longitude;
                }, 
                (err) => console.warn("GPS no disponible: ", err)
            );
        }
    }

    function calcularTotal() {
        let total = 0;
        document.querySelectorAll('.vote-input').forEach(input => {
            let val = parseInt(input.value);
            if(isNaN(val) || val < 0) val = 0;
            total += val;
        });

        const displayTotal = document.getElementById('displayTotal');
        const totalCard = document.getElementById('totalCard');
        const btnGuardar = document.getElementById('btnGuardar');

        document.getElementById('displayTotal').innerText = total;

        // VALIDACIÓN VISUAL EN TIEMPO REAL
        if (total > padron) {
            displayTotal.classList.replace('text-primary', 'text-danger');
            totalCard.classList.add('border', 'border-danger');
            // Opcional: btnGuardar.disabled = true; 
        } else {
            displayTotal.classList.replace('text-danger', 'text-primary');
            totalCard.classList.remove('border', 'border-danger');
            btnGuardar.disabled = false;
        }
    }

    function enviarEscrutinio() {
        const total = document.getElementById('displayTotal').innerText;
        const padron = parseInt(document.getElementById('electoresInput').value) || 0;
        
        // VALIDACIÓN ANTES DE ENVIAR
        if (total > padron) {
            alert(`⚠️ ERROR DE CONSISTENCIA:\n\nEl total de votos (${total}) no puede ser mayor a la cantidad de electores habilitados (${padron}).\n\nPor favor, verifique los números ingresados.`);
            return; // Bloquea la ejecución
        }

        if (total === 0) {
            if(!confirm("El total de votos es 0. ¿Está seguro de guardar la planilla vacía?")) return;
        } else {
            if(!confirm(`Confirmar carga de resultados.\n\nTOTAL DE VOTOS: ${total}`)) return;
        }
        
        document.querySelectorAll('.vote-input').forEach(input => {
            // Si el valor está vacío o es solo espacios
            if (input.value.trim() === '') {
                input.value = '0'; // Forzamos el 0 visual y en el valor
            }
        });

        const form = document.getElementById('resultsForm');
        const formData = new FormData(form);

        // --- AQUÍ ESTÁ EL CAMBIO CLAVE PARA LAS RUTAS ---
        // Uso Blade para imprimir la URL exacta de 'results.store'
        // Esto genera: http://fisq.com/mesas/14/cargar-votos
        const urlStore = "{{ route('results.store', $mesa->id) }}";
        const urlShow = "{{ route('results.show', $mesa->id) }}";

        axios.post(urlStore, formData)
            .then(response => {
                alert('¡Datos guardados correctamente!');
                // Redirigimos a la ruta de "Ver Resultados"
                window.location.href = urlShow;
            })
            .catch(error => {
                console.error(error);
                let msg = "Error al guardar.";
                if(error.response?.data?.message) {
                    msg = "Error: " + error.response.data.message;
                }
                alert(msg);
            });
    }

   

</script>
@endpush