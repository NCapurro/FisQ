{{-- MENÚ DE REPORTES --}}
<li class="nav-item dropdown">
    <a id="navbarDropdownReportes" class="nav-link dropdown-toggle fw-bold" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
        <i class="fa-solid fa-chart-pie me-1"></i> Reportes
    </a>

    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownReportes">
        
        {{-- Reporte 1: Resultados --}}
        <a class="dropdown-item" href="{{ route('reports.resultados') }}">
            <i class="fa-solid fa-table-list me-2 text-primary"></i>Resultados por Depto.
        </a>

        {{-- Reporte 2: Mapa --}}
        <a class="dropdown-item" href="{{ route('reports.mapa') }}">
            <i class="fa-solid fa-map-location-dot me-2 text-success"></i>Mapa de Ganadores
        </a>

        <div class="dropdown-divider"></div>

        {{-- Reporte 3: Incidentes --}}
        <a class="dropdown-item" href="{{ route('reports.incidentes') }}">
            <i class="fa-solid fa-triangle-exclamation me-2 text-danger"></i>Incidentes y Reclamos
        </a>

    </div>
</li>