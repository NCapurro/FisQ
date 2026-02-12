<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Mapa Electoral</title>
    <link rel="icon" href="{{ asset('img/fisqlogo.png') }}" type="image/png">
    <style>
        body { font-family: sans-serif; text-align: center; }
        .map-container { width: 100%; margin: 0 auto; margin-top: 0px; }
        table { width: 100%; border-collapse: collapse; font-size: 12px; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 6px; }
        th { background-color: #eee; }
        .color-box { display: inline-block; width: 10px; height: 10px; border: 1px solid #000; margin-right: 5px; }
        h2 {
        margin-top: 0px; 
        margin-bottom: 10px;
        padding-top: 0px;
        }
        /* Estilo para la marca de agua */
        .watermark {
        position: fixed;   /* Fijo para que se repita en cada página */
        top: 50%;          /* Centrado vertical (aprox) */
        left: 45%;         /* Centrado horizontal (aprox) */
        transform: translate(-50%, -50%); /* Truco para centrar exacto */
        width: 90%;        /* Que ocupe el 80% del ancho de la hoja */
        opacity: 0.10;      /* 10% de opacidad (muy sutil) */
        z-index: -1000;    /* IMPORTANTE: Lo manda al fondo, detrás de la tabla */
        }
    </style>
</head>
<body>
    {{-- MARCA DE AGUA (FONDO) --}}
    <img src="{{ $logoBase64 }}" class="watermark">
    <h2>Mapa de Ganadores</h2>

    <div class="map-container">
        <img src="{{ $mapaImagen }}" style="width: 90%; max-height: 432px; display:block; margin: 0 auto;">
    </div>

    <h3>Detalle de Resultados</h3>
    <table>
        <thead>
            <tr>
                <th>Departamento</th>
                <th>Partido Ganador</th>
                <th>Votos</th>
                <th>%</th>
            </tr>
        </thead>
        <tbody>
            @foreach($datosMapa as $dato)
            <tr>
                <td style="text-align: left;">
                    <span class="color-box" style="background-color: {{ $dato['color'] }};"></span>
                    {{ $dato['depto'] }}
                </td>
                <td>{{ $dato['ganador'] }}</td>
                <td style="text-align: center;">{{ number_format($dato['votos'], 0, ',', '.') }}</td>
                <td style="text-align: center;">{{ $dato['porcentaje'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>