@extends('layouts.app')

@section('content')
<div class="container">
    {{-- ENCABEZADO Y FILTRO --}}
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h2 class="fw-bold text-dark">
                <i class="fa-solid fa-chart-simple me-2 text-primary"></i>Resultados Generales
            </h2>
        </div>
        
        <div class="col-md-6">
            <form method="GET" action="{{ route('graphics.index') }}" id="filterForm" class="d-flex justify-content-md-end">
                <div class="input-group shadow-sm" style="max-width: 350px;">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="fa-solid fa-filter text-muted"></i>
                    </span>
                    <select name="department_id" class="form-select border-start-0 ps-0" onchange="document.getElementById('filterForm').submit()">
                        <option value="">-- Todos los Departamentos --</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" @selected(request('department_id') == $dept->id)>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
    </div>

    {{-- TARJETA DEL GRÁFICO --}}
    <div class="card shadow border-0">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-secondary">Distribución de Votos</h5>
            
            {{-- CHIP DE ESTADO --}}
            @if($avance >= 100)
                <span class="badge bg-success"><i class="fa-solid fa-check-double me-1"></i> Escrutinio Finalizado</span>
            @else
                <span class="badge bg-warning text-dark"><i class="fa-solid fa-spinner fa-spin me-1"></i> En Proceso</span>
            @endif
        </div>
        
        <div class="card-body p-4">
            
            {{-- 🟢 NUEVO: BARRA DE PROGRESO DE ESCUELAS ESCRUTADAS --}}
            <div class="mb-5">
                <div class="d-flex justify-content-between align-items-end mb-1">
                    <span class="fw-bold text-muted small text-uppercase">Avance del Escrutinio</span>
                    <div class="text-end">
                        <span class="fw-bold fs-5 {{ $avance == 100 ? 'text-success' : 'text-primary' }}">
                            {{ $avance }}%
                        </span>
                        <span class="text-muted small ms-1">
                            ({{ $mesasEscrutadas }} de {{ $totalMesas }} mesas)
                        </span>
                    </div>
                </div>
                <div class="progress" style="height: 12px; border-radius: 10px; background-color: #e9ecef;">
                    <div class="progress-bar {{ $avance == 100 ? 'bg-success' : 'bg-primary' }} progress-bar-striped progress-bar-animated" 
                         role="progressbar" 
                         style="width: {{ $avance }}%" 
                         aria-valuenow="{{ $avance }}" 
                         aria-valuemin="0" 
                         aria-valuemax="100">
                    </div>
                </div>
            </div>
            {{-- FIN BARRA DE PROGRESO --}}


            @if(count($labels) > 0)
                <div style="position: relative; height: 400px; width: 100%;">
                    <canvas id="votesChart"></canvas>
                </div>
            @else
                <div class="text-center py-5 text-muted opacity-50">
                    <i class="fa-solid fa-chart-pie fa-4x mb-3"></i>
                    <h4>No hay datos cargados para este criterio.</h4>
                    <p>Intente seleccionar otro departamento o cargue nuevos resultados.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endsection

@push('scripts')
<script>
    let myChartInstance = null;

    document.addEventListener("DOMContentLoaded", function() {
        setTimeout(initChart, 100);
    });

    function initChart() {
        const ctx = document.getElementById('votesChart');

        if (!ctx) return;

        if (myChartInstance) {
            myChartInstance.destroy();
        }

        const labels = {!! json_encode($labels) !!};
        const dataValues = {!! json_encode($data) !!};
        const colors = {!! json_encode($colors) !!};

        myChartInstance = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Votos',
                    data: dataValues,
                    backgroundColor: colors,
                    borderColor: 'rgba(0,0,0,0.1)',
                    borderWidth: 1,
                    borderRadius: 4,
                    barPercentage: 0.6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: { duration: 800 },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                // Muestra el valor absoluto y el porcentaje relativo del dataset si se quisiera
                                return context.parsed.y + '% Votos'; 
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f8f9fa' },
                        ticks: { precision: 0 }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    }
</script>
@endpush