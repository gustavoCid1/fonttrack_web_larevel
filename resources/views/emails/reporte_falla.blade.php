<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reporte de Fallas y Uso de Materiales - FontTrack</title>
    <style>
        /* Reset básico */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: #fafafa;
            color: #333;
            padding: 20px;
        }

        /* Contenedor centrado para limitar el ancho y que todo quepa en una hoja */
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        /* Header: banner con logo */
        header {
            width: 100%;
            margin-bottom: 20px;
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 3px solid #F6B88F;
        }

        header h1 {
            color: #E38B5B;
            font-size: 24px;
            margin-bottom: 10px;
        }

        header p {
            color: #634D3B;
            font-size: 14px;
            font-weight: bold;
        }

        h1, h3 {
            text-align: center;
            margin-bottom: 15px;
            color: #E38B5B;
        }

        h3 {
            border-bottom: 1px solid #F6B88F;
            padding-bottom: 5px;
            margin-top: 20px;
        }

        /* Tabla de datos generales */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            font-size: 12px;
        }

        .data-table th {
            background: #eaeaea;
            padding: 8px 10px;
            width: 35%;
            border: 1px solid #ddd;
            font-weight: bold;
            color: #634D3B;
            text-align: left;
        }

        .data-table td {
            padding: 8px 10px;
            background: #fff;
            border: 1px solid #ddd;
            color: #333;
        }

        /* Tabla para Materiales Utilizados */
        .materials-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 12px;
        }

        .materials-table thead {
            background: #F6B88F;
            color: #634D3B;
        }

        .materials-table thead th {
            padding: 8px 10px;
            border: 1px solid #ddd;
            font-weight: bold;
            text-align: left;
        }

        .materials-table tbody tr {
            border-bottom: 1px solid #ddd;
        }

        .materials-table tbody tr:nth-child(even) {
            background: #f9f9f9;
        }

        .materials-table tbody tr:last-child {
            background: #f0f8ff;
            font-weight: bold;
        }

        .materials-table tbody td {
            padding: 8px 10px;
            border: 1px solid #ddd;
        }

        /* Secciones de descripción */
        .description-section {
            margin-bottom: 20px;
        }

        .description-box {
            background-color: #FCE8D5;
            padding: 15px;
            border-radius: 5px;
            margin-top: 10px;
            border-left: 4px solid #F6B88F;
            font-size: 12px;
            line-height: 1.4;
        }

        /* ✅ NUEVOS ESTILOS para materiales */
        .material-summary {
            background: #E8F4FD;
            border: 1px solid #B3D9FF;
            border-radius: 5px;
            padding: 12px;
            margin-bottom: 15px;
            font-size: 12px;
        }

        .material-summary strong {
            color: #007ACC;
        }

        .no-materials {
            background-color: #FFF3CD;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #FFEB3B;
            text-align: center;
            font-style: italic;
        }

        /* Sección de firma */
        .firma {
            margin-top: 30px;
            padding: 15px 20px;
            border-top: 2px solid #F6B88F;
            font-size: 12px;
            background-color: #FCE8D5;
            border-radius: 5px;
        }

        .firma p {
            margin-bottom: 8px;
            color: #634D3B;
            font-weight: bold;
        }

        /* Información del adjunto */
        .attachment-info {
            background-color: #E8F4FD;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
            border-left: 4px solid #007ACC;
            font-size: 12px;
        }

        .attachment-info strong {
            color: #007ACC;
        }

        /* Footer */
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            color: #666;
            font-size: 11px;
        }

        /* Responsive para emails */
        @media screen and (max-width: 600px) {
            .container {
                padding: 15px;
                margin: 10px;
            }
            
            .data-table th,
            .data-table td {
                padding: 6px 8px;
                font-size: 11px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Reporte de Fallas / Uso de Materiales #{{ $data['id_reporte'] ?? ($data['falla']->id ?? 'N/A') }}</h1>
            <p>Sistema FontTrack</p>
        </header>

        <!-- Tabla de datos generales -->
        <table class="data-table">
            <tbody>
                <tr>
                    <th>Lugar</th>
                    <td>{{ $data['lugar'] ?? 'No especificado' }}</td>
                </tr>
                <tr>
                    <th>No. ECO</th>
                    <td>{{ $data['eco'] ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Placas</th>
                    <td>{{ $data['placas'] ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Marca</th>
                    <td>{{ $data['marca'] ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Año</th>
                    <td>{{ $data['anio'] ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>KM</th>
                    <td>{{ $data['km'] ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Fecha</th>
                    <td>{{ $data['fecha'] ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Nombre del Conductor</th>
                    <td>{{ $data['nombre_conductor'] ?? 'N/A' }}</td>
                </tr>
            </tbody>
        </table>

        <div class="description-section">
            <h3>Descripción del Servicio / Fallo</h3>
            <div class="description-box">
                {{ $data['descripcion'] ?? 'Sin descripción' }}
            </div>
        </div>

        <div class="description-section">
            <h3>Observaciones Técnicas del Trabajo Realizado</h3>
            <div class="description-box">
                {{ $data['observaciones'] ?? 'Sin observaciones' }}
            </div>
        </div>

        <h3>Materiales Utilizados</h3>
        
        @php
            // ✅ ESTRATEGIA MULTI-NIVEL para obtener materiales (CORRIGE EL PROBLEMA EN EMAIL)
            $materialesFormateados = [];
            
            // ESTRATEGIA 1: Usar el modelo Falla con su método corregido
            if (isset($data['falla']) && method_exists($data['falla'], 'getMaterialesFormateadosAttribute')) {
                $materialesFormateados = $data['falla']->materiales_formateados;
            }
            
            // ESTRATEGIA 2: Procesar materials del array data
            if (empty($materialesFormateados) && isset($data['materials']) && !empty($data['materials'])) {
                $materials = $data['materials'];
                if (is_string($materials)) {
                    $materials = json_decode($materials, true);
                }
                
                if (is_array($materials)) {
                    $materialesFormateados = collect($materials)->map(function ($material) {
                        $materialId = $material['id'] ?? $material['id_material'] ?? $material['material_id'] ?? null;
                        
                        // ✅ PRIORIZAR descripción del array, luego buscar en BD
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
                        
                        $cantidad = $material['cantidad'] ?? $material['qty'] ?? $material['quantity'] ?? 0;
                        $materialModel = $materialId ? \App\Models\Material::find($materialId) : null;
                        
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
            
            // ESTRATEGIA 3: Usar materials directamente de la falla si existe
            if (empty($materialesFormateados) && isset($data['falla']) && !empty($data['falla']->materials)) {
                $materials = $data['falla']->materials;
                if (is_array($materials)) {
                    $materialesFormateados = collect($materials)->map(function ($material) {
                        $materialId = $material['id'] ?? $material['id_material'] ?? null;
                        
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
            
            // ESTRATEGIA 4: Fallback a campos legacy
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

            <table class="materials-table">
                <thead>
                    <tr>
                        <th>🔧 Material</th>
                        <th>📦 Cantidad</th>
                        @if(collect($materialesFormateados)->where('costo_unitario', '>', 0)->count() > 0)
                            <th>💰 Costo Unit.</th>
                            <th>💵 Costo Total</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($materialesFormateados as $material)
                        <tr>
                            <td>
                                <strong>{{ $material['descripcion'] }}</strong>
                                @if($material['clave'] !== 'N/A')
                                    <br><small style="color: #666;">Clave: {{ $material['clave'] }}</small>
                                @endif
                            </td>
                            <td style="text-align: center; font-weight: bold;">{{ $material['cantidad'] }}</td>
                            @if(collect($materialesFormateados)->where('costo_unitario', '>', 0)->count() > 0)
                                <td style="text-align: right;">${{ number_format($material['costo_unitario'] ?? 0, 2) }}</td>
                                <td style="text-align: right; font-weight: bold;">${{ number_format($material['costo_total'] ?? 0, 2) }}</td>
                            @endif
                        </tr>
                    @endforeach
                    
                    @if(collect($materialesFormateados)->sum('costo_total') > 0)
                        <tr style="background: #F0F8FF; font-weight: bold; border-top: 2px solid #007ACC;">
                            <td>🎯 <strong>TOTAL</strong></td>
                            <td style="text-align: center; font-weight: bold;">{{ collect($materialesFormateados)->sum('cantidad') }}</td>
                            @if(collect($materialesFormateados)->where('costo_unitario', '>', 0)->count() > 0)
                                <td style="text-align: right;">-</td>
                                <td style="text-align: right; font-weight: bold; color: #007ACC;">${{ number_format(collect($materialesFormateados)->sum('costo_total'), 2) }}</td>
                            @endif
                        </tr>
                    @endif
                </tbody>
            </table>
        @else
            <div class="no-materials">
                <p><strong>⚠️ No se registraron materiales en este reporte</strong></p>
            </div>
        @endif

        <div class="firma">
            <p><strong>✅ Autorizado por:</strong> {{ $data['autorizado_por'] ?? 'N/A' }}</p>
            <p><strong>👁️ Revisado por:</strong> {{ $data['reviso_por'] ?? 'N/A' }}</p>
            @if(isset($data['fecha_generacion']))
                <p><strong>📅 Fecha de generación:</strong> {{ $data['fecha_generacion'] }}</p>
            @endif
            @if(isset($data['usuario_envia']))
                <p><strong>📤 Enviado por:</strong> {{ $data['usuario_envia'] }}</p>
            @endif
        </div>

        <div class="attachment-info">
            <strong>📎 Archivo Adjunto:</strong> En este correo encontrarás el reporte completo en formato PDF con el diseño oficial de FontTrack.
            <br><br>
            <strong>💡 Tip:</strong> El archivo PDF contiene la misma información pero en formato optimizado para impresión.
        </div>

        <div class="footer">
            <p><strong>🚀 Sistema FontTrack</strong> - Gestión de Mantenimiento</p>
            <p>Este es un correo automático generado por el sistema FontTrack.</p>
            <p>Para cualquier consulta, por favor contacta al administrador del sistema.</p>
        </div>
    </div>
</body>
</html>