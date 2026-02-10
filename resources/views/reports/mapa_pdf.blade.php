<!DOCTYPE html>
<html>
<head>
    <title>Mapa Electoral</title>
    <style>
        body { font-family: sans-serif; }
        .map-container { width: 100%; text-align: center; margin-bottom: 30px; }
        table { width: 100%; border-collapse: collapse; font-size: 12px; }
        th, td { border: 1px solid #ccc; padding: 6px; }
        th { background-color: #eee; }
        .color-box { display: inline-block; width: 12px; height: 12px; margin-right: 5px; border: 1px solid #000; }
    </style>
</head>
<body>
    <h1 style="text-align: center;">Mapa de Ganadores por Departamento</h1>
    
    <div class="map-container">
        <svg viewBox="0 0 800 1000" width="500" height="600" xmlns="http://www.w3.org/2000/svg">
             <path id="parana" d="M100,200 h150 v150 h-150 z" fill="{{ $coloresMap['parana'] ?? '#cccccc' }}" stroke="#ffffff" stroke-width="2"/>
             <path id="concordia" d="M300,200 h100 v100 h-100 z" fill="{{ $coloresMap['concordia'] ?? '#cccccc' }}" stroke="#ffffff" stroke-width="2"/>
             </svg>
    </div>

    <h3>Detalle de Ganadores</h3>
    <table>
        <thead>
            <tr>
                <th>Departamento</th>
                <th>Partido Ganador</th>
                <th>Votos</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ganadores as $dato)
            <tr>
                <td>
                    <span class="color-box" style="background-color: {{ $dato['color'] }};"></span>
                    {{ $dato['depto'] }}
                </td>
                <td>{{ $dato['ganador'] }}</td>
                <td>{{ number_format($dato['votos'], 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>