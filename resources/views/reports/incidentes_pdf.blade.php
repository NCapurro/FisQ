<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Log de Incidentes</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border-bottom: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f8f9fa; border-top: 2px solid #333; border-bottom: 2px solid #333; }
        .priority-alta { color: red; font-weight: bold; }
        .priority-media { color: orange; }
        .badge { padding: 3px 6px; border-radius: 4px; color: white; font-size: 9px; }
        .bg-resolved { background-color: green; }
        .bg-pending { background-color: gray; }

        .watermark {
        position: fixed;   /* Fijo para que se repita en cada página */
        top: 50%;          /* Centrado vertical (aprox) */
        left: 45%;         /* Centrado horizontal (aprox) */
        transform: translate(-50%, -50%); /* Truco para centrar exacto */
        width: 80%;        /* Que ocupe el 80% del ancho de la hoja */
        opacity: 0.06;      /* 10% de opacidad (muy sutil) */
        z-index: -1000;    /* IMPORTANTE: Lo manda al fondo, detrás de la tabla */
        }
    </style>
</head>
<body>
    <img src="{{ $logoBase64 }}" class="watermark">
    <h2>Registro de Incidentes y Reclamos</h2>
    <p>Fecha de reporte: {{ date('d/m/Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th width="15%">Fecha</th>
                <th width="10%">Prioridad</th>
                <th width="25%">Ubicación</th>
                <th width="40%">Descripción</th>
                <th width="10%">Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($incidentes as $inc)
            <tr>
                <td>{{ $inc->created_at->format('d/m/Y H:i') }}</td>
                <td class="priority-{{ $inc->priority }}">
                    {{ strtoupper($inc->priority) }}
                </td>
                <td>
                    @if($inc->mesa)
                        Mesa {{ $inc->mesa->number }}<br>
                        {{ $inc->mesa->school->name }}
                    @else
                        General
                    @endif
                </td>
                <td>{{ $inc->description }}</td>
                <td>
                    <span class="badge {{ $inc->is_resolved ? 'bg-resolved' : 'bg-pending' }}">
                        {{ $inc->is_resolved ? 'RESUELTO' : 'PENDIENTE' }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>