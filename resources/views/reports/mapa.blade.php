@extends('layouts.app')

@section('content')

@push('styles')
<style>
    /* Tooltip */
    #map-tooltip {
        position: fixed;
        background-color: #ffffff; /* Fondo blanco sólido */
        border: 1px solid #aaa;    /* Borde sutil */
        border-radius: 8px;        /* Bordes redondeados */
        padding: 12px 15px;        /* Espacio interno */
        box-shadow: 0 5px 15px rgba(0,0,0,0.3); /* Sombra elegante */
        pointer-events: none;      /* El mouse lo atraviesa */
        display: none;             /* Oculto por defecto */
        z-index: 999999;           /* Muy arriba de todo */
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        font-size: 0.9rem;
        min-width: 180px;
        color: #131313;
        line-height: 1.4;
    }
    
    /* Título del Tooltip */
    #map-tooltip strong { 
        display: block; 
        font-size: 1.1rem; 
        color: #000; 
        border-bottom: 2px solid #000000; /* Línea roja estilo Laravel/Bootstrap */
        margin-bottom: 8px;
        padding-bottom: 4px;
    }

    /* Datos del Tooltip */
    #map-tooltip .dato { margin-bottom: 3px; }
    #map-tooltip .etiqueta { font-weight: 600; color: #000000; margin-right: 5px; }

    /* Efectos del Mapa */
    path.map-region {
        transition: all 0.15s ease-in-out;
        cursor: pointer;
        stroke: #121212dc;
        stroke-width: 0.2px;
    }
    
    path.map-region:hover {
        opacity: 0.8;
        stroke: #000;
        stroke-width: 2px;
        filter: brightness(1.1); /* Ilumina un poco el color */
    }
</style>
@endpush



<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Mapa Electoral - Ganadores</h2>
        <a href="{{ route('reports.mapa', ['format' => 'pdf']) }}" class="btn btn-danger" target="_blank">
            <i class="fa-solid fa-file-pdf me-2"></i>Descargar Mapa PDF
        </a>
    </div>

    <div class="row">
        <div class="col-md-7">
            <div class="card shadow-sm">
                <div class="card-body text-center bg-light">
                    {{-- INICIO SVG ENTRE RIOS --}}
                   

                    <svg viewBox="60 0 170 215" style="width: 100%; height: auto; max-height: 80vh;" xmlns="http://www.w3.org/2000/svg">

                    <g stroke="#1e1e1e" stroke-width="0.5">
                    @foreach($coordenadas as $slug => $path)
                        @php
                            // Buscamos los datos de este depto (si no existen, ponemos default)
                            $info = $datosMapa[$slug] ?? [
                                'depto' => ucfirst($slug), 
                                'ganador' => 'Sin Datos', 
                                'votos' => 0, 
                                'porcentaje' => '0%', 
                                'color' => '#ccc'
                            ];
                        @endphp

                        <path 
                            d="{{ $path }}" 
                            fill="{{ $info['color'] }}" 
                            id="{{ $slug }}"
                            
                            {{-- AQUÍ LOS DATOS EN HTML --}}
                            data-nombre="{{ $info['depto'] }}"
                            data-ganador="{{ $info['ganador'] }}"
                            data-votos="{{ $info['votos'] }}"
                            data-porcentaje="{{ $info['porcentaje'] }}"
                            
                            class="map-region"
                        />
                    @endforeach
                </g>
            </svg>

                    {{-- FIN SVG --}}
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card shadow-sm">
                <div class="card-header bg-white fw-bold">Referencias</div>
                <ul class="list-group list-group-flush">
                    @foreach($datosMapa as $dato)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <span class="d-inline-block rounded-circle me-2" 
                                  style="width: 15px; height: 15px; background-color: {{ $dato['color'] }};">
                            </span>
                            {{ $dato['depto'] }}
                        </div>
                        <div class="text-end">
                            <small class="d-block fw-bold">{{ $dato['ganador'] }}</small>
                            <small class="text-muted">{{$dato['porcentaje'] }}% / {{ number_format($dato['votos'], 0, ',', '.') }} votos</small>
                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>


{{-- 2. EL TOOLTIP (HTML Oculto) --}}
<div id="map-tooltip">
    <strong id="tt-nombre">Departamento</strong>
    <div class="dato"><span class="etiqueta">Ganador:</span> <span id="tt-ganador"></span></div>
    <div class="dato"><span class="etiqueta">Votos:</span> <span id="tt-votos"></span></div>
    <div class="dato"><span class="etiqueta">Porcentaje:</span> <span id="tt-porcentaje"></span></div>
</div>


@endsection

{{-- 3. JAVASCRIPT --}}
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // 1. Obtener elementos
        const tooltip = document.getElementById('map-tooltip');
        const regiones = document.querySelectorAll('.map-region');
        
        // Elementos internos para rellenar
        const ttNombre = document.getElementById('tt-nombre');
        const ttGanador = document.getElementById('tt-ganador');
        const ttVotos = document.getElementById('tt-votos');
        const ttPorcentaje = document.getElementById('tt-porcentaje');

        // 2. TRUCO DE MAGIA: Mover el tooltip al <body>
        // Esto evita que quede atrapado dentro de la tarjeta o se vea cortado.
        if (tooltip) {
            document.body.appendChild(tooltip);
        }

        // 3. Asignar eventos a cada departamento
        regiones.forEach(region => {
            
            // ENTRAR
            region.addEventListener('mouseenter', (e) => {
                const d = e.target.dataset;
                
                // Rellenar datos
                ttNombre.innerText = d.nombre;
                ttGanador.innerText = d.ganador;
                ttVotos.innerText = d.votos;
                ttPorcentaje.innerText = d.porcentaje;
                
                // Mostrar
                tooltip.style.display = 'block';
            });

            // MOVER
            region.addEventListener('mousemove', (e) => {
                // Posicionar tooltip junto al mouse (con un pequeño margen)
                tooltip.style.left = (e.clientX + 15) + 'px';
                tooltip.style.top = (e.clientY + 15) + 'px';
            });

            // SALIR
            region.addEventListener('mouseleave', () => {
                tooltip.style.display = 'none';
            });
        });
    });
</script>
@endpush