<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Reporte de Auditoría - FisQ</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        h2 { text-align: center; color: #333; }

         .watermark {
        position: fixed;   /* Fijo para que se repita en cada página */
        top: 45%;          /* Centrado vertical (aprox) */
        left: 46%;         /* Centrado horizontal (aprox) */
        transform: translate(-50%, -50%); /* Truco para centrar exacto */
        width: 80%;        /* Que ocupe el 80% del ancho de la hoja */
        opacity: 0.07;      /* 10% de opacidad (muy sutil) */
        z-index: -1000;    /* IMPORTANTE: Lo manda al fondo, detrás de la tabla */
        }
    </style>
</head>
<body>
    <img src="{{ $logoBase64 }}" class="watermark">
    <h2>Auditoría del Sistema - FisQ</h2>
    <p>Fecha de reporte: {{ now()->format('d/m/Y H:i') }}</p>
    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Usuario</th>
                <th>Módulo</th>
                <th>Acción</th>
                <th>Descripción</th>
            </tr>
        </thead>
        <tbody>
            @foreach($logs as $log)
            <tr>
                <td>{{ $log->created_at->format('d/m/Y H:i') }}</td>
                <td>{{ $log->user->name ?? 'Sistema' }}</td>
                <td>{{ $log->module }}</td>
                <td>{{ $log->action }}</td>
                <td>{{ $log->description }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>