<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Reporte de Fallas y Uso de Materiales</title>
    <style>
        /* Reset básico */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #fafafa;
            color: #333;
            padding: 20px;
        }

        /* Contenedor centrado para limitar el ancho y que todo quepa en una hoja */
        .container {
            max-width: 600px;
            margin: 0 auto;
        }

        /* Header: banner con imagen a lo ancho */
        header {
            width: 100%;
            margin-bottom: 20px;
        }

        header img {
            display: block;
            width: 100%;
            height: auto;
        }

        h1,
        h3 {
            text-align: center;
            margin-bottom: 15px;
        }

        /* Estilos de las tablas: más compactas para encajar en una sola hoja */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            font-size: 10px;
            /* Fuente reducida para una vista más compacta */
        }

        /* Tabla de datos generales */
        .data-table th {
            background: #eaeaea;
            padding: 6px 8px;
            width: 35%;
        }

        .data-table td {
            padding: 6px 8px;
            background: #fff;
        }

        /* Tabla para Materiales Utilizados */
        table thead {
            background: #007ACC;
            color: #fff;
        }

        table thead th {
            padding: 6px 8px;
        }

        table tbody tr {
            border-bottom: 1px solid #ddd;
        }

        table tbody tr:nth-child(even) {
            background: #f9f9f9;
        }

        table tbody tr:hover {
            background: #f1f1f1;
        }

        table th,
        table td {
            text-align: left;
        }

        /* Sección de firma */
        .firma {
            margin-top: 30px;
            padding: 10px 20px;
            border-top: 1px solid #ddd;
            font-size: 10px;
        }

        /* Footer con olas */
        footer {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            overflow: hidden;
            pointer-events: none;
        }

        footer img {
            display: block;
            width: 100%;
            height: auto;
            opacity: 0.1;
            /* Efecto transparente */
        }

        footer::after {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to bottom, rgba(250, 250, 250, 0), rgba(250, 250, 250, 0.05));
            pointer-events: none;
        }

        /* Opcional: estilos para impresión */
        @media print {
            body {
                padding: 10px;
            }

            .container {
                max-width: 100%;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <header>
            <img src="img/completa.png" alt="Banner">
        </header>

        <h1>Reporte de Fallas / Uso de Materiales</h1>

        <!-- Tabla de datos generales -->
        <table class="data-table">
            <tbody>
                <tr>
                    <th>Lugar</th>
                    <td>{{ $data['lugar'] }}</td>
                </tr>
                <tr>
                    <th>No. ECO</th>
                    <td>{{ $data['eco'] }}</td>
                </tr>
                <tr>
                    <th>Placas</th>
                    <td>{{ $data['placas'] }}</td>
                </tr>
                <tr>
                    <th>Marca</th>
                    <td>{{ $data['marca'] }}</td>
                </tr>
                <tr>
                    <th>Año</th>
                    <td>{{ $data['anio'] }}</td>
                </tr>
                <tr>
                    <th>KM</th>
                    <td>{{ $data['km'] }}</td>
                </tr>
                <tr>
                    <th>Fecha</th>
                    <td>{{ $data['fecha'] }}</td>
                </tr>
                <tr>
                    <th>Nombre del Conductor</th>
                    <td>{{ $data['nombre_conductor'] }}</td>
                </tr>
            </tbody>
        </table>

        <h3>Descripción del Servicio / Fallo</h3>
        <p>{{ $data['descripcion'] }}</p>

        <h3>Observaciones Técnicas del Trabajo Realizado</h3>
        <p>{{ $data['observaciones'] }}</p>

        <h3>Materiales Utilizados</h3>
        @php
            // Verificar si materials ya es un array o necesita ser decodificado
            $materials = $data['materials'];
            
            // Si es string, decodificar
            if (is_string($materials)) {
                $materials = json_decode($materials, true);
            }
            
            // Si no es array o está vacío, inicializar como array vacío
            if (!is_array($materials)) {
                $materials = [];
            }
        @endphp
        
        @if(count($materials) > 0)
            <table>
                <thead>
                    <tr>
                        <th>Material</th>
                        <th>Cantidad</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($materials as $material)
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

        <div class="firma">
            <p><strong>Autorizado por:</strong> {{ $data['autorizado_por'] }}</p>
            <p><strong>Revisado por:</strong> {{ $data['reviso_por'] }}</p>
        </div>
    </div>

    <footer>
        <img src="img/ser(1).png" alt="Olas">
    </footer>
</body>

</html>