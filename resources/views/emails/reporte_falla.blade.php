<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Falla</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f9f9f9;
        }
        .container {
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #F6B88F;
        }
        .header h1 {
            color: #E38B5B;
            margin: 0;
        }
        .info-section {
            margin-bottom: 25px;
        }
        .info-section h3 {
            color: #E38B5B;
            margin-bottom: 10px;
            border-bottom: 1px solid #F6B88F;
            padding-bottom: 5px;
        }
        .info-row {
            display: flex;
            margin-bottom: 8px;
        }
        .info-label {
            font-weight: bold;
            width: 150px;
            color: #634D3B;
        }
        .info-value {
            flex: 1;
            color: #333;
        }
        .materials-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .materials-table th,
        .materials-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .materials-table th {
            background-color: #F6B88F;
            color: #634D3B;
            font-weight: bold;
        }
        .materials-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .description-box {
            background-color: #FCE8D5;
            padding: 15px;
            border-radius: 5px;
            margin-top: 10px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            color: #666;
            font-size: 12px;
        }
        .attachment-info {
            background-color: #E8F4FD;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
            border-left: 4px solid #007ACC;
        }
        .attachment-info i {
            color: #007ACC;
            margin-right: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Reporte de Falla #{{ $data['id_reporte'] }}</h1>
            <p>Sistema FontTrack</p>
        </div>

        <div class="info-section">
            <h3>Información del Vehículo</h3>
            <div class="info-row">
                <span class="info-label">Lugar:</span>
                <span class="info-value">{{ $data['lugar'] }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">No. ECO:</span>
                <span class="info-value">{{ $data['eco'] }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Placas:</span>
                <span class="info-value">{{ $data['placas'] }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Marca:</span>
                <span class="info-value">{{ $data['marca'] }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Año:</span>
                <span class="info-value">{{ $data['anio'] }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Kilómetros:</span>
                <span class="info-value">{{ $data['km'] }}</span>
            </div>
        </div>

        <div class="info-section">
            <h3>Información del Reporte</h3>
            <div class="info-row">
                <span class="info-label">Fecha:</span>
                <span class="info-value">{{ $data['fecha'] }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Conductor:</span>
                <span class="info-value">{{ $data['nombre_conductor'] }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Autorizado por:</span>
                <span class="info-value">{{ $data['autorizado_por'] }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Revisado por:</span>
                <span class="info-value">{{ $data['reviso_por'] }}</span>
            </div>
        </div>

        <div class="info-section">
            <h3>Descripción del Servicio / Fallo</h3>
            <div class="description-box">
                {{ $data['descripcion'] }}
            </div>
        </div>

        <div class="info-section">
            <h3>Observaciones Técnicas</h3>
            <div class="description-box">
                {{ $data['observaciones'] }}
            </div>
        </div>

        <div class="info-section">
            <h3>Materiales Utilizados</h3>
            @if(count($data['materials']) > 0)
                <table class="materials-table">
                    <thead>
                        <tr>
                            <th>Material</th>
                            <th>Cantidad</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['materials'] as $material)
                            <tr>
                                <td>{{ $material['descripcion'] ?? 'Material no especificado' }}</td>
                                <td>{{ $material['cantidad'] ?? '0' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p>No se registraron materiales</p>
            @endif
        </div>

        <div class="attachment-info">
            <strong>📎 Archivo Adjunto:</strong> En este correo encontrarás el reporte completo en formato PDF.
        </div>

        <div class="footer">
            <p>Este es un correo automático generado por el sistema FontTrack.</p>
            <p>Para cualquier consulta, por favor contacta al administrador del sistema.</p>
        </div>
    </div>
</body>
</html>