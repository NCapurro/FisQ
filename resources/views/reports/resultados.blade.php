@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Reporte de Resultados por Departamento</h2>
        <div>
            <a href="{{ route('reports.resultados', ['format' => 'excel']) }}" class="btn btn-success">
                <i class="fa-solid fa-file-excel me-2"></i>Exportar Excel
            </a>
            <a href="{{ route('reports.resultados', ['format' => 'pdf']) }}" class="btn btn-danger" target="_blank">
                <i class="fa-solid fa-file-pdf me-2"></i>Exportar PDF
            </a>
        </div>
    </div>

    <div class="card shadow border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle mb-0">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th>Departamento</th>
                            <th class="text-center">Electores</th>
                            <th class="text-center">Votantes</th>
                            @foreach($parties as $party)
                                <th class="text-center" style="border-bottom: 3px solid {{ $party->color_hex }}">
                                    {{ $party->abbreviation }}
                                </th>
                            @endforeach
                            <th class="text-center bg-dark text-white">Total Válidos</th>
                            <th class="text-center bg-secondary text-white">% Part.</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reporte as $fila)
                        <tr>
                            <td class="fw-bold">{{ $fila['departamento'] }}</td>
                            <td class="text-center">{{ number_format($fila['electores'], 0, ',', '.') }}</td>
                            <td class="text-center">{{ number_format($fila['votantes'], 0, ',', '.') }}</td>
                            
                            @foreach($parties as $party)
                                <td class="text-center">
                                    {{ number_format($fila['votos_partidos'][$party->id] ?? 0, 0, ',', '.') }}
                                </td>
                            @endforeach

                            <td class="text-center fw-bold">{{ number_format($fila['total_valido'], 0, ',', '.') }}</td>
                            
                            <td class="text-center">
                                <span class="badge {{ $fila['participacion'] < 60 ? 'bg-danger' : 'bg-success' }}">
                                    {{ $fila['participacion'] }}%
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection