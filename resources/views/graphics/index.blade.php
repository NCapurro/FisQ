@extends('layouts.app')

@section('content')
<div class="container">
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

    <div class="card shadow border-0">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold text-secondary">Distribución de Votos</h5>
        </div>
        <div class="card-body p-4">
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
    // Variable global para controlar la instancia del gráfico y evitar duplicados
    let myChartInstance = null;

    document.addEventListener("DOMContentLoaded", function() {
        // Retraso de seguridad de 100ms para asegurar que el HTML esté renderizado
        setTimeout(initChart, 100);
    });

    function initChart() {
        const ctx = document.getElementById('votesChart');

        // Si no hay datos (no existe el canvas), salimos sin error
        if (!ctx) return;

        // 1. LIMPIEZA: Si ya existe un gráfico, lo destruimos antes de crear el nuevo
        if (myChartInstance) {
            myChartInstance.destroy();
        }

        // 2. DATOS DESDE LARAVEL
        const labels = {!! json_encode($labels) !!};
        const dataValues = {!! json_encode($data) !!};
        const colors = {!! json_encode($colors) !!};

        // 3. CONFIGURACIÓN
        myChartInstance = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Votos',
                    data: dataValues,
                    backgroundColor: colors,
                    borderColor: 'rgba(0,0,0,0.1)', // Borde sutil
                    borderWidth: 1,
                    borderRadius: 4,
                    barPercentage: 0.6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false, // CRUCIAL: Permite que el gráfico obedezca la altura del div (400px)
                animation: {
                    duration: 800
                },
                plugins: {
                    legend: {
                        display: false // Ocultamos leyenda porque los nombres ya están abajo
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.raw + '%';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#f8f9fa'
                        },
                        ticks: {
                            precision: 0 // Evita decimales en el eje Y
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }
</script>
@endpush