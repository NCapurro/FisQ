<!DOCTYPE html>
<html>
<head>
    <title>Reporte de Resultados</title>
    <style>
        body { font-family: sans-serif; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 5px; text-align: center; }
        th { background-color: #f0f0f0; }
        .text-left { text-align: left; }
        .header { text-align: center; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Resultados Electorales por Departamento</h1>
        <p>Generado el: {{ date('d/m/Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Departamento</th>
                <th>Electores</th>
                @foreach($parties as $party)
                    <th>{{ $party->abbreviation }}</th>
                @endforeach
                <th>Total Válidos</th>
                <th>% Part.</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reporte as $fila)
            <tr>
                <td class="text-left">{{ $fila['departamento'] }}</td>
                <td>{{ $fila['electores'] }}</td>
                
                @foreach($parties as $party)
                    <td>{{ $fila['votos_partidos'][$party->id] ?? 0 }}</td>
                @endforeach

                <td>{{ $fila['total_valido'] }}</td>
                <td>{{ $fila['participacion'] }}%</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>