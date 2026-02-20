@extends('layouts.app')

@section('content')
<div class="container-fluid px-3">
    <div class="row mb-3">
        <div class="col-md-8">
            <h2 class="fw-bold"><i class="fa-solid fa-earth-americas me-2 text-primary"></i>Mapa de Cargas en Vivo</h2>
            <p class="text-muted mb-0">Visualización geográfica de los fiscales al momento de enviar el escrutinio.</p>
        </div>
        <div class="col-md-4 text-end align-self-center">
            <div class="badge bg-primary fs-6">
                <i class="fa-solid fa-location-dot me-1"></i> {{ count($puntos) }} Mesas Geodesignadas
            </div>
        </div>
    </div>

    <div class="card shadow border-0">
        <div class="card-body p-0">
            <div id="map" style="height: 75vh; width: 100%;"></div>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css" />
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>
@endpush

@endsection
@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        
        // 1. Inicializar Mapa (Centrado en Entre Ríos aprox)
        var map = L.map('map').setView([-31.7413, -60.5116], 7);

        // 2. Capa Base (OpenStreetMap)
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(map);

        // 3. Crear el Grupo de Clusters
        var markers = L.markerClusterGroup();

        // 4. Datos desde Laravel
        var puntos = {!! json_encode($puntos) !!};

        // 5. Icono personalizado (opcional, usa el default si prefieres)
        var blueIcon = new L.Icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png',
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
            iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41]
        });

        // 6. Recorrer datos y agregar al cluster
        puntos.forEach(function(punto) {
            var marker = L.marker([punto.lat, punto.lng], {icon: blueIcon});
            
            // Popup con información útil para el auditor
            var popupContent = `
                <div class="text-center">
                    <h6 class="fw-bold text-primary mb-1">Mesa N° ${punto.mesa}</h6>
                    <span class="badge bg-secondary mb-2">${punto.departamento}</span>
                    <div class="text-start small mt-2">
                        <strong><i class="fa-solid fa-school"></i> Escuela:</strong><br>
                        ${punto.escuela}<br>
                        <hr class="my-1">
                        <strong><i class="fa-solid fa-user"></i> Fiscal:</strong> ${punto.fiscal}<br>
                        <strong><i class="fa-solid fa-clock"></i> Hora:</strong> ${punto.hora}
                    </div>
                    <a href="${punto.url}" class="btn btn-sm btn-outline-primary mt-2 w-100">Ver Acta</a>
                </div>
            `;

            marker.bindPopup(popupContent);
            markers.addLayer(marker);
        });

        // 7. Agregar el grupo de clusters al mapa
        map.addLayer(markers);

        // Si hay puntos, ajustar el zoom para verlos todos
        if (puntos.length > 0) {
            map.fitBounds(markers.getBounds());
        }
    });
</script>
@endpush