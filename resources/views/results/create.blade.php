@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
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

                        <div class="alert alert-info border-info d-flex align-items-center mb-4">
                            <i class="fa-solid fa-circle-info me-3 text-info fs-3"></i>
                            <div>
                                <strong>Instrucciones:</strong> Copie los valores del acta. 
                                El sistema sumará automáticamente. Si ya cargó datos antes, edítelos y guarde nuevamente.
                            </div>
                        </div>

                        <div class="list-group list-group-flush mb-4">
                            @foreach($parties as $party)
                                {{-- Lógica para recuperar valor si ya existe (Edición) --}}
                                @php
                                    $prevVote = $mesa->results->where('political_party_id', $party->id)->first();
                                    $val = $prevVote ? $prevVote->votes : ''; // Dejamos vacío si es 0 para que se vea el placeholder
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

                        <div class="card bg-light border-0 mb-4">
                            <div class="card-body d-flex justify-content-between align-items-center px-4">
                                <span class="fw-bold text-uppercase text-secondary">Total General</span>
                                <span id="displayTotal" class="display-6 fw-bold text-primary">0</span>
                            </div>
                        </div>

                        <div class="card bg-light border-0 mb-4">
                            <div class="card-body">
                                <h6 class="fw-bold text-uppercase text-muted mb-3">
                                    <i class="fa-solid fa-camera me-2"></i>Foto del Acta Oficial
                                </h6>
                                
                                <input type="file" 
                                       name="photo" 
                                       id="photoInput" 
                                       class="form-control" 
                                       accept="image/*" 
                                       capture="environment">
                                
                                <div class="form-text">
                                    Por favor, tome una foto clara y legible del acta firmada.
                                </div>

                                <div id="photoPreviewContainer" class="mt-3 d-none text-center">
                                    <p class="small text-muted mb-1">Vista previa:</p>
                                    <img id="photoPreview" src="" class="img-fluid rounded shadow-sm" style="max-height: 300px;">
                                </div>
                            </div>
                        </div>


                        <div class="d-grid gap-2">
                            <button type="button" onclick="enviarEscrutinio()" class="btn btn-success btn-lg fw-bold">
                                <i class="fa-solid fa-save me-2"></i> GUARDAR RESULTADOS
                            </button>
                            <a href="{{ route('mesas.index') }}" class="btn btn-outline-secondary">
                                Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

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
        document.getElementById('displayTotal').innerText = total;
    }

    function enviarEscrutinio() {
        const total = document.getElementById('displayTotal').innerText;
        
        if(!confirm(`Confirmar carga de resultados.\n\nTOTAL DE VOTOS: ${total}`)) {
            return;
        }

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
@endsection