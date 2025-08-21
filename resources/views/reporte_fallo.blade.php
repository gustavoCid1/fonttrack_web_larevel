<!DOCTYPE html>
<html>

<head>
    <!-- 
    Plantilla de Reporte PDF - FontTrack
    @author Jesús Felipe Avilez
    @author Gustavo Angel Cid Flores
    version 2.0.0
    Descripción: Vista para generar reportes PDF de fallas y uso de materiales
    Incluye múltiples estrategias para obtener datos de materiales
    y formateo optimizado para impresión en una sola página


    -->
    <meta charset="UTF-8">
    <title>Nueva Vista - Reporte de Fallas y Uso de Materiales</title>

    <style>
        /* Reset CSS básico para consistencia entre navegadores */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Estilos base del documento */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #fafafa;
            color: #333;
            padding: 20px;
        }

        /* Contenedor principal centrado para limitar el ancho del contenido */
        .container {
            max-width: 600px;
            margin: 0 auto;
        }

        /* Encabezado con banner de imagen */
        header {
            width: 100%;
            margin-bottom: 20px;
        }

        header img {
            display: block;
            width: 100%;
            height: auto;
            max-height: 150px;
        }

        /* Títulos principales */
        h1,
        h3 {
            text-align: center;
            margin-bottom: 15px;
        }

        /* Estilos base para todas las tablas */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            font-size: 10px;
        }

        /* Tabla de datos generales del reporte */
        .data-table th {
            background: #eaeaea;
            padding: 6px 8px;
            width: 35%;
            border: 1px solid #ddd;
        }

        .data-table td {
            padding: 6px 8px;
            background: #fff;
            border: 1px solid #ddd;
        }

        /* Tabla de materiales utilizados */
        table thead {
            background: #007ACC;
            color: #fff;
        }

        table thead th {
            padding: 6px 8px;
            border: 1px solid #ddd;
        }

        table tbody tr {
            border-bottom: 1px solid #ddd;
        }

        /* Filas alternas para mejor legibilidad */
        table tbody tr:nth-child(even) {
            background: #f9f9f9;
        }

        table tbody td {
            padding: 6px 8px;
            border: 1px solid #ddd;
        }

        table th,
        table td {
            text-align: left;
        }

        /* Sección de firmas y autorizaciones */
        .firma {
            margin-top: 30px;
            padding: 10px 20px;
            border-top: 1px solid #ddd;
            font-size: 10px;
        }

        /* Footer con imagen de fondo (olas decorativas) */
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
            max-height: 100px;
        }

        /* Overlay gradient para el footer */
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

        /* Estilos específicos para la sección de materiales */
        .material-summary {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 8px;
            margin-bottom: 10px;
            font-size: 9px;
        }

        .material-summary strong {
            color: #007ACC;
        }

        .material-row {
            border-bottom: 1px solid #ddd;
        }

        .material-row:last-child {
            border-bottom: none;
        }

        /* Mensaje cuando no hay materiales registrados */
        .no-materials {
            text-align: center;
            color: #666;
            font-style: italic;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        /* Estilos específicos para impresión */
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
        <!-- Encabezado del reporte con banner -->
        <header>
            @if(file_exists(public_path('img/completa.png')))
                <img src="{{ public_path('img/completa.png') }}" alt="Banner">
            @else
                <!-- Fallback cuando no existe la imagen del banner -->
                <div
                    style="height: 100px; background: #007ACC; color: white; display: flex; align-items: center; justify-content: center; font-size: 24px; font-weight: bold;">
                    FONTTRACK - SISTEMA DE REPORTES
                </div>
            @endif
        </header>

        <!-- Título principal del reporte -->
        <h1>Reporte de Fallas / Uso de Materiales</h1>

        <!-- Tabla con datos generales del vehículo y reporte -->
        <table class="data-table">
            <tbody>
                <tr>
                    <th>Lugar</th>
                    <td>{{ $data['lugar'] ?? 'No especificado' }}</td>
                </tr>
                <tr>
                    <th>No. ECO</th>
                    <td>{{ $data['eco'] ?? '' }}</td>
                </tr>
                <tr>
                    <th>Placas</th>
                    <td>{{ $data['placas'] ?? '' }}</td>
                </tr>
                <tr>
                    <th>Marca</th>
                    <td>{{ $data['marca'] ?? '' }}</td>
                </tr>
                <tr>
                    <th>Año</th>
                    <td>{{ $data['anio'] ?? '' }}</td>
                </tr>
                <tr>
                    <th>KM</th>
                    <td>{{ $data['km'] ?? '' }}</td>
                </tr>
                <tr>
                    <th>Fecha</th>
                    <td>{{ $data['fecha'] ?? '' }}</td>
                </tr>
                <tr>
                    <th>Nombre del Conductor</th>
                    <td>{{ $data['nombre_conductor'] ?? '' }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Sección de descripción del servicio o falla -->
        <h3>Descripción del Servicio / Fallo</h3>
        <p>{{ $data['descripcion'] ?? 'Sin descripción' }}</p>

        <!-- Sección de observaciones técnicas -->
        <h3>Observaciones Técnicas del Trabajo Realizado</h3>
        <p>{{ $data['observaciones'] ?? 'Sin observaciones' }}</p>

        <!-- Sección de materiales utilizados -->
        <h3>Materiales Utilizados</h3>

        @php
            /*
             * Sistema de procesamiento de materiales con múltiples estrategias
             * 
             * Este bloque implementa un sistema robusto para obtener los materiales
             * utilizados desde diferentes fuentes de datos, con fallbacks automáticos
             * para garantizar que siempre se muestren los materiales correctos.
             */

            $materialesFormateados = [];

            // ESTRATEGIA 1: Usar el modelo Falla con su método de materiales formateados
            if (isset($data['falla']) && method_exists($data['falla'], 'getMaterialesFormateadosAttribute')) {
                $materialesFormateados = $data['falla']->materiales_formateados;
            }

            // ESTRATEGIA 2: Procesar el array 'materials' del data
            if (empty($materialesFormateados) && isset($data['materials']) && !empty($data['materials'])) {
                $materials = $data['materials'];

                // Convertir JSON string a array si es necesario
                if (is_string($materials)) {
                    $materials = json_decode($materials, true);
                }

                // Procesar cada material del array
                if (is_array($materials)) {
                    $materialesFormateados = collect($materials)->map(function ($material) {
                        // Obtener ID del material con múltiples nombres posibles
                        $materialId = $material['id'] ?? $material['id_material'] ?? $material['material_id'] ?? null;

                        // Priorizar descripción del array, luego buscar en base de datos
                        $descripcion = 'Material no especificado';
                        if (!empty($material['descripcion'])) {
                            $descripcion = $material['descripcion'];
                        } elseif ($materialId) {
                            $materialModel = \App\Models\Material::find($materialId);
                            if ($materialModel && $materialModel->descripcion) {
                                $descripcion = $materialModel->descripcion;
                            }
                        } elseif (!empty($material['nombre'])) {
                            $descripcion = $material['nombre'];
                        } elseif (!empty($material['name'])) {
                            $descripcion = $material['name'];
                        }

                        // Obtener cantidad con múltiples nombres posibles
                        $cantidad = $material['cantidad'] ?? $material['qty'] ?? $material['quantity'] ?? 0;
                        $materialModel = $materialId ? \App\Models\Material::find($materialId) : null;

                        // Retornar material formateado con todos los datos necesarios
                        return [
                            'id' => $materialId,
                            'descripcion' => $descripcion,
                            'cantidad' => $cantidad,
                            'clave' => $materialModel?->clave_material ?? 'N/A',
                            'costo_unitario' => $materialModel?->costo_promedio ?? 0,
                            'costo_total' => ($materialModel?->costo_promedio ?? 0) * $cantidad
                        ];
                    })->toArray();
                }
            }

            // ESTRATEGIA 3: Usar materials directamente del modelo Falla
            if (empty($materialesFormateados) && isset($data['falla']) && !empty($data['falla']->materials)) {
                $materials = $data['falla']->materials;
                if (is_array($materials)) {
                    $materialesFormateados = collect($materials)->map(function ($material) {
                        $materialId = $material['id'] ?? $material['id_material'] ?? null;

                        // Obtener descripción del material
                        $descripcion = 'Material no especificado';
                        if (!empty($material['descripcion'])) {
                            $descripcion = $material['descripcion'];
                        } elseif ($materialId) {
                            $materialModel = \App\Models\Material::find($materialId);
                            if ($materialModel && $materialModel->descripcion) {
                                $descripcion = $materialModel->descripcion;
                            }
                        }

                        $cantidad = $material['cantidad'] ?? 0;
                        $materialModel = $materialId ? \App\Models\Material::find($materialId) : null;

                        return [
                            'descripcion' => $descripcion,
                            'cantidad' => $cantidad,
                            'clave' => $materialModel?->clave_material ?? 'N/A',
                            'costo_unitario' => $materialModel?->costo_promedio ?? 0,
                            'costo_total' => ($materialModel?->costo_promedio ?? 0) * $cantidad
                        ];
                    })->toArray();
                }
            }

            // ESTRATEGIA 4: Fallback a campos legacy (compatibilidad con versiones anteriores)
            if (empty($materialesFormateados)) {
                $materialLegacy = $data['material'] ?? (isset($data['falla']) ? $data['falla']->material : null);
                $cantidadLegacy = $data['cantidad'] ?? (isset($data['falla']) ? $data['falla']->cantidad : 0);

                if (!empty($materialLegacy)) {
                    $materialesFormateados = [
                        [
                            'id' => null,
                            'descripcion' => $materialLegacy,
                            'cantidad' => $cantidadLegacy,
                            'clave' => 'N/A',
                            'costo_unitario' => 0,
                            'costo_total' => 0
                        ]
                    ];
                }
            }
        @endphp

        @if(count($materialesFormateados) > 0)
            <!-- Tabla de materiales utilizados -->
            <table>
                <thead>
                    <tr>
                        <th>Clave</th>
                        <th>Material</th>
                        <th>Cantidad</th>
                        <!-- Mostrar columnas de costo solo si hay materiales con costo -->
                        @if(collect($materialesFormateados)->where('costo_unitario', '>', 0)->count() > 0)
                            <th>Costo Unit.</th>
                            <th>Costo Total</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    <!-- Iterar sobre cada material formateado -->
                    @foreach($materialesFormateados as $material)
                        <tr class="material-row">
                            <td>{{ $material['clave'] ?? 'N/A' }}</td>
                            <td><strong>{{ $material['descripcion'] }}</strong></td>
                            <td>{{ $material['cantidad'] }}</td>
                            <!-- Mostrar costos solo si hay al menos un material con costo -->
                            @if(collect($materialesFormateados)->where('costo_unitario', '>', 0)->count() > 0)
                                <td>${{ number_format($material['costo_unitario'] ?? 0, 2) }}</td>
                                <td>${{ number_format($material['costo_total'] ?? 0, 2) }}</td>
                            @endif
                        </tr>
                    @endforeach

                    <!-- Fila de totales (solo si hay costos) -->
                    @if(collect($materialesFormateados)->where('costo_unitario', '>', 0)->count() > 0)
                        <tr style="background: #f8f9fa; font-weight: bold;">
                            <td
                                colspan="{{ collect($materialesFormateados)->where('costo_unitario', '>', 0)->count() > 0 ? '3' : '2' }}">
                                TOTAL</td>
                            <td>{{ collect($materialesFormateados)->sum('cantidad') }}</td>
                            @if(collect($materialesFormateados)->where('costo_unitario', '>', 0)->count() > 0)
                                <td>${{ number_format(collect($materialesFormateados)->sum('costo_total'), 2) }}</td>
                            @endif
                        </tr>
                    @endif
                </tbody>
            </table>
        @else
            <!-- Mensaje cuando no hay materiales registrados -->
            <div class="no-materials">
                <p><strong>No se registraron materiales en este reporte</strong></p>
            </div>
        @endif

        <!-- Sección de firmas y autorizaciones -->
        <div class="firma">
            <p><strong>Autorizado por:</strong> {{ $data['autorizado_por'] ?? '' }}</p>
            <p><strong>Revisado por:</strong> {{ $data['reviso_por'] ?? '' }}</p>
            @if(isset($data['fecha_generacion']))
                <p><strong>Fecha de generación:</strong> {{ $data['fecha_generacion'] }}</p>
            @endif
            @if(isset($data['usuario_genera']))
                <p><strong>Generado por:</strong> {{ $data['usuario_genera'] }}</p>
            @endif
        </div>
    </div>

    <!-- Footer decorativo con imagen de olas (si existe) -->
    @if(file_exists(public_path('img/ser(1).png')))
        <footer>
            <img src="{{ public_path('img/ser(1).png') }}" alt="Olas">
        </footer>
    @endif
</body>

</html>