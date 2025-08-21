{{-- 
    Vista principal de materiales para usuarios normales
    
    Esta vista permite a los usuarios ver materiales disponibles, buscar materiales
    y vehículos con autocompletado AJAX, crear reportes de uso de materiales
    y gestionar su perfil de usuario. Incluye funcionalidades avanzadas como
    secuencias secretas para acceso de administradores y notificaciones dinámicas.
    
    @author Gustavo Angel Cid Flores
    @version 2.0.0
--}}

<!DOCTYPE html>
<html lang="es">
<head>
    {{-- Meta tags y configuración base --}}
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('img/logo.png') }}">
    <title>Lista de Materiales - FontTrack</title>
    
    {{-- Enlaces a frameworks CSS y librerías externas --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">

{{-- Redirección automática para usuarios no autenticados --}}
@guest
<script>
    window.location.href = '/login';
</script>
@endguest

<style>
    /* === ESTILOS GENERALES DEL SISTEMA === */
    body {
        font-size: 0.9em;
        font-family: 'Arial', sans-serif;
        background: linear-gradient(135deg, #FCE8D5 0%, #F6B88F 100%);
        color: #634D3B;
        text-align: center;
        margin: 0;
        padding: 0;
        min-height: 100vh;
        padding-top: 0;
    }

    /* Títulos con gradientes corporativos */
    h2 {
        font-size: 1.8em;
        background: linear-gradient(45deg, #E38B5B, #F6B88F);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        font-weight: bold;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
    }

    /* === NAVBAR CON EFECTOS VISUALES === */
    .navbar {
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
        padding: 15px 20px;
        border-bottom: 4px solid #D1784C;
        position: relative;
        overflow: visible;
        min-height: 90px;
        z-index: 1060;
    }

    /* Logo con animaciones de hover */
    .navbar .logo {
        transition: all 0.4s ease;
        filter: drop-shadow(2px 2px 4px rgba(0,0,0,0.2));
    }

    .navbar .logo:hover {
        transform: scale(1.05);
        filter: brightness(1.1) drop-shadow(4px 4px 8px rgba(0,0,0,0.3));
    }

    /* === DROPDOWN DE USUARIO === */
    .user-dropdown-container {
        position: relative;
        z-index: 1070;
    }

    /* Botón de perfil con gradientes y efectos */
    .user-profile-btn {
        display: flex;
        align-items: center;
        background: linear-gradient(135deg, #9d5b37ff 0%, #ba896bff 100%);
        border: none;
        border-radius: 25px;
        padding: 12px 20px;
        color: white;
        transition: all 0.3s ease;
        box-shadow: 0 6px 20px rgba(249, 97, 8, 0.4);
        position: relative;
        z-index: 1070;
    }

    .user-profile-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 30px rgba(234, 170, 102, 0.87);
        color: white;
    }

    /* Avatar de usuario con bordes */
    .user-avatar-new {
        border: 3px solid white;
        margin-right: 12px;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    }

    /* Menú dropdown personalizado */
    .dropdown-menu-custom {
        background: linear-gradient(135deg, #fff 0%, #f8f9ff 100%);
        border: none;
        border-radius: 15px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
        padding: 10px;
        min-width: 250px;
        z-index: 1080;
        position: absolute;
        margin-top: 5px;
    }

    /* Items del dropdown con animaciones */
    .dropdown-item-custom {
        padding: 12px 20px;
        margin: 3px 0;
        border-radius: 10px;
        transition: all 0.3s ease;
        border-left: 4px solid transparent;
        display: flex;
        align-items: center;
    }

    .dropdown-item-custom:hover {
        background: linear-gradient(135deg, #b06853ff, #d59f65ff);
        color: white;
        transform: translateX(5px);
        border-left-color: white;
    }

    .dropdown-item-custom i {
        margin-right: 12px;
        width: 20px;
        text-align: center;
    }

    /* === CONTENEDOR PRINCIPAL === */
    .container {
        background: linear-gradient(135deg, #fff 0%, #FCE8D5 100%);
        padding: 40px;
        border-radius: 20px;
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        margin: 30px auto;
        border: 2px solid rgba(246, 184, 143, 0.3);
        position: relative;
        overflow: hidden;
        z-index: 1;
    }

    /* === BOTONES CON GRADIENTES === */
    .btn {
        border: none;
        border-radius: 12px;
        padding: 14px 28px;
        font-weight: bold;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    /* Colores de botones por tipo */
    .btn { background: linear-gradient(135deg, #E38B5B, #D1784C); color: #fff; }
    .btn-info { background: linear-gradient(135deg, #88C0D0, #6AA8C0); }
    .btn-warning { background: linear-gradient(135deg, #E5A34D, #D1903D); }
    .btn-danger { background: linear-gradient(135deg, #D9534F, #C9302C); }
    .btn-success { background: linear-gradient(135deg, #28a745, #20c997); }

    .btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(227, 139, 91, 0.4);
    }

    /* === TABLA DE MATERIALES === */
    .table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }

    .table th {
        padding: 20px;
        font-weight: bold;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.2);
    }

    .table td {
        padding: 18px;
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(10px);
    }

    /* Efectos hover en filas de tabla */
    .table tbody tr {
        transition: all 0.3s ease;
        border-bottom: 1px solid rgba(246, 184, 143, 0.3);
    }

    .table tbody tr:hover {
        background: linear-gradient(135deg, rgba(246, 184, 143, 0.2), rgba(227, 139, 91, 0.1));
        transform: scale(1.02);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    /* === ESTILOS PARA BÚSQUEDA DE MATERIALES === */
    .material-search-container {
        position: relative;
    }

    /* Input de búsqueda de materiales */
    .material-search-input {
        background: linear-gradient(135deg, #fff 0%, #f0f8ff 100%);
        border: 2px solid #667eea;
        border-radius: 15px;
        padding: 15px 20px;
        font-size: 16px;
        transition: all 0.3s ease;
        width: 100%;
    }

    .material-search-input:focus {
        outline: none;
        border-color: #764ba2;
        box-shadow: 0 0 20px rgba(102, 126, 234, 0.3);
        transform: scale(1.02);
    }

    /* Dropdown de resultados de búsqueda de materiales */
    .search-results-dropdown {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: none;
        border-radius: 15px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
        max-height: 300px;
        overflow-y: auto;
        z-index: 1000;
        display: none;
        margin-top: 5px;
    }

    /* Items individuales de resultados de materiales */
    .search-result-item {
        padding: 15px 20px;
        cursor: pointer;
        border-bottom: 1px solid #f0f0f0;
        transition: all 0.3s ease;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .search-result-item:hover {
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
        transform: translateX(5px);
    }

    .search-result-item:last-child {
        border-bottom: none;
    }

    /* Código de material con estilos especiales */
    .material-code {
        font-weight: bold;
        color: #667eea;
    }

    .search-result-item:hover .material-code {
        color: white;
    }

    /* Indicador de stock de material */
    .material-stock {
        background: #e8f2ff;
        padding: 3px 8px;
        border-radius: 10px;
        font-size: 12px;
        color: #667eea;
    }

    .search-result-item:hover .material-stock {
        background: rgba(255, 255, 255, 0.2);
        color: white;
    }

    /* Detalles y metadatos de materiales */
    .material-details {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
    }

    .material-meta {
        font-size: 12px;
        color: #666;
    }

    .search-result-item:hover .material-meta {
        color: rgba(255, 255, 255, 0.8);
    }

    /* === ESTILOS PARA BÚSQUEDA DE VEHÍCULOS === */
    .vehicle-search-container {
        position: relative;
    }

    /* Input de búsqueda de vehículos */
    .vehicle-search-input {
        background: linear-gradient(135deg, #fff 0%, #fff5f0 100%);
        border: 2px solid #ff7f50;
        border-radius: 15px;
        padding: 15px 20px;
        font-size: 16px;
        transition: all 0.3s ease;
        width: 100%;
    }

    .vehicle-search-input:focus {
        outline: none;
        border-color: #ff6347;
        box-shadow: 0 0 20px rgba(255, 127, 80, 0.3);
        transform: scale(1.02);
    }

    /* Dropdown de resultados de vehículos */
    .vehicle-results-dropdown {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: none;
        border-radius: 15px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
        max-height: 250px;
        overflow-y: auto;
        z-index: 1000;
        display: none;
        margin-top: 5px;
    }

    /* Items de resultados de vehículos */
    .vehicle-result-item {
        padding: 12px 20px;
        cursor: pointer;
        border-bottom: 1px solid #f0f0f0;
        transition: all 0.3s ease;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .vehicle-result-item:hover {
        background: linear-gradient(135deg, #ff7f50, #ff6347);
        color: white;
        transform: translateX(5px);
    }

    .vehicle-result-item:last-child {
        border-bottom: none;
    }

    /* Número ECO del vehículo */
    .vehicle-eco {
        font-weight: bold;
        color: #ff7f50;
        font-size: 1.1em;
    }

    .vehicle-result-item:hover .vehicle-eco {
        color: white;
    }

    /* Información y metadatos del vehículo */
    .vehicle-info {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
    }

    .vehicle-meta {
        font-size: 12px;
        color: #666;
        margin-top: 3px;
    }

    .vehicle-result-item:hover .vehicle-meta {
        color: rgba(255, 255, 255, 0.8);
    }

    /* Estado del vehículo */
    .vehicle-status {
        background: #e8f5e8;
        padding: 3px 8px;
        border-radius: 10px;
        font-size: 11px;
        color: #28a745;
        text-transform: capitalize;
    }

    .vehicle-result-item:hover .vehicle-status {
        background: rgba(255, 255, 255, 0.2);
        color: white;
    }

    /* Botón para agregar material */
    .btn-add-material {
        background: linear-gradient(135deg, #28a745, #20c997);
        color: white;
        border: none;
        border-radius: 15px;
        padding: 12px 25px;
        font-weight: bold;
        transition: all 0.3s ease;
        box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
    }

    .btn-add-material:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(40, 167, 69, 0.4);
        color: white;
    }

    /* === MODALES === */
    .modal-content {
        border-radius: 20px;
        box-shadow: 0 25px 80px rgba(0, 0, 0, 0.4);
        border: 3px solid rgba(246, 184, 143, 0.3);
        background: linear-gradient(135deg, #fff 0%, #FCE8D5 100%);
    }

    .gradient-primary {
        background: linear-gradient(135deg, #F6B88F, #E38B5B);
    }

    /* === NOTIFICACIONES === */
    .notification-success {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: linear-gradient(135deg, #28a745, #20c997);
        color: white;
        padding: 30px 40px;
        border-radius: 20px;
        font-size: 1.3rem;
        font-weight: bold;
        z-index: 10000;
        box-shadow: 0 20px 60px rgba(40, 167, 69, 0.4);
        backdrop-filter: blur(10px);
        border: 2px solid rgba(255, 255, 255, 0.3);
        opacity: 0;
        visibility: hidden;
        transform: translate(-50%, -50%) scale(0.5);
        transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    }

    .notification-success.show {
        opacity: 1;
        visibility: visible;
        transform: translate(-50%, -50%) scale(1);
    }

    .notification-success i {
        font-size: 2rem;
        margin-right: 15px;
    }

    /* === FUNCIONALIDADES SECRETAS === */
    .secret-indicator {
        position: fixed;
        bottom: 20px;
        right: 20px;
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #E38B5B, #D1784C);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.2rem;
        box-shadow: 0 8px 25px rgba(227, 139, 91, 0.4);
        transition: all 0.3s ease;
        z-index: 1000;
        opacity: 0.7;
        cursor: pointer;
    }

    /* Secuencia secreta para acceso admin */
    .secret-sequence {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: rgba(227, 139, 91, 0.95);
        color: white;
        padding: 20px 30px;
        border-radius: 15px;
        font-size: 1.2rem;
        font-weight: bold;
        z-index: 10000;
        display: none;
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
        backdrop-filter: blur(10px);
    }

    .secret-sequence.show {
        display: block;
        animation: fadeInScale 0.5s ease;
    }

    @keyframes fadeInScale {
        from { 
            opacity: 0; 
            transform: translate(-50%, -50%) scale(0.5); 
        }
        to { 
            opacity: 1; 
            transform: translate(-50%, -50%) scale(1); 
        }
    }

    /* Botón de acceso admin secreto */
    .admin-access-btn {
        position: fixed;
        top: 20px;
        left: 20px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        border-radius: 15px;
        padding: 12px 20px;
        font-weight: bold;
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        transition: all 0.3s ease;
        z-index: 1000;
        opacity: 0;
        visibility: hidden;
        transform: translateY(-20px);
    }

    .admin-access-btn.show {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    /* === RESPONSIVE DESIGN === */
    @media (max-width: 768px) {
        .navbar {
            padding: 15px 20px;
            min-height: auto;
        }
        
        .container {
            padding: 20px;
            margin: 20px auto 10px auto;
        }

        .user-profile-btn span {
            display: none;
        }
    }
</style>
</head>
<body>
    {{-- Indicadores y funcionalidades secretas --}}
    <div class="secret-indicator" id="secretIndicator" title="Comandos secretos: ↑↓k↓↑">
        <i class="fas fa-keyboard"></i>
    </div>

    <div class="secret-sequence" id="secretSequence">
        Secuencia: <span id="sequenceDisplay"></span>
    </div>

    <button class="admin-access-btn" id="adminAccessBtn" onclick="location.href='/materials'">
        <i class="fas fa-crown"></i> Acceso Administrador
    </button>

    {{-- Notificación de éxito global --}}
    <div class="notification-success" id="notificationSuccess">
        <i class="fas fa-check-circle"></i> 
        <span id="notificationMessage">¡Notificación enviada correctamente!</span>
    </div>

    {{-- Navbar principal con dropdown de usuario --}}
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <img src="{{ asset('img/FontTrack.png') }}" alt="logo" width="100" height="70" class="me-2 logo">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="ms-auto">
                    @auth
                        <div class="user-dropdown-container">
                            <div class="dropdown">
                                <button class="user-profile-btn dropdown-toggle" type="button" 
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                    <img src="{{ Auth::user()->foto_usuario_url ?? asset('img/usuario_default.png') }}" 
                                         alt="Foto de perfil" width="40" height="40" class="rounded-circle user-avatar-new">
                                    <span>{{ Auth::user()->nombre ?? 'Usuario' }}</span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-custom">
                                    <li>
                                        <a class="dropdown-item dropdown-item-custom" href="#" 
                                           data-bs-toggle="modal" data-bs-target="#modalCambiarNombre">
                                            <i class="fas fa-user-edit"></i> Cambiar nombre
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item dropdown-item-custom" href="#" 
                                           data-bs-toggle="modal" data-bs-target="#modalCambiarPassword">
                                            <i class="fas fa-key"></i> Cambiar contraseña
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item dropdown-item-custom" href="#" 
                                           data-bs-toggle="modal" data-bs-target="#modalCambiarFoto">
                                            <i class="fas fa-camera"></i> Cambiar imagen de perfil
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item dropdown-item-custom" href="#" 
                                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                            <i class="fas fa-sign-out-alt"></i> Cerrar sesión
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') ?? '#' }}" class="btn btn-outline-primary me-2">Iniciar Sesión</a>
                        @if(Route::has('register'))
                            <a href="{{ route('register') }}" class="btn btn-primary">Registrarse</a>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    {{-- Contenido principal de la página --}}
    <div class="container mt-4">
        <h2 class="mb-3">Lista de Materiales</h2>
        
        {{-- Botones de acción principales --}}
        <div class="row mb-3">
            <div class="col-md-6">
                <div class="btn-group">
                    <button class="btn btn-warning" id="btnReporteFallas">📋 Reportar Uso de Materiales</button>
                </div>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('materiales.export') }}" class="btn btn-outline-primary me-2">📤 Exportar Excel</a>
            </div>
        </div>
        
        {{-- Barra de búsqueda --}}
        <div class="row mb-3">
            <div class="col-md-8">
                <form class="d-flex" action="{{ route('materiales.index') }}" method="GET">
                    <input class="form-control me-2" type="search" name="query" placeholder="Buscar material" value="{{ request('query') }}">
                    <button class="btn btn-outline-success" type="submit"><i class="bi bi-search"></i></button>
                </form>
            </div>
        </div>

        {{-- Tabla de materiales --}}
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Clave</th>
                        <th>Descripción</th>
                        <th>Genérico</th>
                        <th>Clasificación</th>
                        <th>Existencia</th>
                        <th>Costo ($)</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($materiales) && $materiales->count() > 0)
                        @foreach($materiales as $material)
                            <tr data-id="{{ $material->id_material }}">
                                <td>{{ $material->id_material }}</td>
                                <td>{{ $material->clave_material }}</td>
                                <td>{{ $material->descripcion }}</td>
                                <td>{{ $material->generico }}</td>
                                <td>{{ $material->clasificacion }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-danger btnDisminuir" data-id="{{ $material->id_material }}" title="Reportar uso">
                                            <i class="bi bi-dash"></i>
                                        </button>
                                        <span class="btn btn-light disabled">{{ $material->existencia }}</span>
                                    </div>
                                </td>
                                <td>{{ $material->costo_promedio }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-info btnVer" data-id="{{ $material->id_material }}" data-bs-toggle="modal" data-bs-target="#modalMaterial">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="8" class="text-center">No hay materiales disponibles</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        
        {{-- Paginación de resultados --}}
        <div class="d-flex justify-content-center">
            @if(isset($materiales) && method_exists($materiales, 'links'))
                {{ $materiales->appends(['query' => request('query')])->links('pagination::bootstrap-5') }}
            @endif
        </div>
    </div>

    {{-- Modal para ver detalles de material --}}
    <div class="modal fade" id="modalMaterial" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalMaterialTitle"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="modalMaterialBody"></div>
                <div class="modal-footer" id="modalMaterialFooter"></div>
            </div>
        </div>
    </div>

    {{-- Modal principal de reporte con autocompletado --}}
    <div class="modal fade" id="modalReporte" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="formReporte">
                    @csrf
                    <div class="modal-header gradient-primary text-white">
                        <h5 class="modal-title">📋 Reporte de Uso de Materiales</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        {{-- Selección de lugar --}}
                        <div class="mb-3">
                            <label for="id_lugar_reporte" class="form-label fw-bold">Selecciona el Lugar:</label>
                            <select id="id_lugar_reporte" name="id_lugar" class="form-select" required>
                                <option value="">-- Selecciona un lugar --</option>
                                @if(isset($lugares))
                                    @foreach($lugares as $lugar)
                                        <option value="{{ $lugar->id_lugar }}" 
                                            @if(Auth::user()->id_lugar == $lugar->id_lugar) selected @endif>
                                            {{ $lugar->nombre }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        {{-- Búsqueda de vehículos con autocompletado --}}
                        <div class="mb-4">
                            <h6 class="text-primary mb-3">🚗 Información del Vehículo</h6>
                            <div class="vehicle-search-container mb-3">
                                <label for="vehicleSearch" class="form-label fw-bold">Buscar Vehículo (ECO, Placas, Marca, Modelo)</label>
                                <input type="text" id="vehicleSearch" class="form-control vehicle-search-input" 
                                       placeholder="🔍 Escriba para buscar vehículos..." autocomplete="off">
                                <div class="vehicle-results-dropdown" id="vehicleResultsDropdown"></div>
                            </div>
                        </div>
                        
                        {{-- Campos de datos del vehículo --}}
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">No. ECO</label>
                                <input type="text" name="eco" id="ecoField" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Placas</label>
                                <input type="text" name="placas" id="placasField" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Marca</label>
                                <input type="text" name="marca" id="marcaField" class="form-control">
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Año</label>
                                <input type="text" name="ano" id="anoField" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">KM</label>
                                <input type="text" name="km" id="kmField" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Fecha</label>
                                <input type="date" name="fecha" class="form-control" value="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                        
                        {{-- Campos adicionales del reporte --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nombre del Conductor</label>
                            <input type="text" name="nombre_conductor" id="conductorField" class="form-control">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Descripción del Servicio / Trabajo Realizado</label>
                            <textarea name="descripcion" rows="3" class="form-control" placeholder="Describe el trabajo realizado o la falla atendida"></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Observaciones Técnicas</label>
                            <textarea name="observaciones" rows="3" class="form-control" placeholder="Observaciones adicionales del trabajo"></textarea>
                        </div>
                        
                        {{-- Búsqueda de materiales --}}
                        <h6 class="mt-4 mb-3 text-primary">📦 Materiales Utilizados</h6>
                        <div class="material-search-container mb-3">
                            <label for="materialSearch" class="form-label fw-bold">Buscar Material (ID, Clave, Descripción, Genérico, Clasificación)</label>
                            <input type="text" id="materialSearch" class="form-control material-search-input" 
                                   placeholder="🔍 Escriba para buscar materiales por cualquier campo..." autocomplete="off">
                            <div class="search-results-dropdown" id="searchResultsDropdown"></div>
                            <button type="button" id="btnAgregarMaterial" class="btn btn-add-material mt-3" disabled>
                                <i class="fas fa-plus-circle"></i> Agregar Material Seleccionado
                            </button>
                        </div>
                        
                        {{-- Tabla de materiales seleccionados --}}
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered" id="selectedMaterialsTable">
                                <thead class="table-primary">
                                    <tr>
                                        <th>Material</th>
                                        <th>Cantidad</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                        
                        {{-- Selección de usuario reportante --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold">Usuario que reporta</label>
                            <select name="usuario_reporta_id" id="usuarioReporta" class="form-select" required>
                                <option value="">-- Selecciona el usuario que reporta --</option>
                            </select>
                            <input type="hidden" name="nombre_usuario_reporta" id="nombreUsuarioReporta">
                            <input type="hidden" name="correo_usuario_reporta" id="correoUsuarioReporta">
                        </div>
                        
                        {{-- Campos ocultos para datos --}}
                        <input type="hidden" name="materials" id="materialsData">
                        <input type="hidden" name="vehicle_id" id="vehicleIdField">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                        <button type="submit" id="btnEnviarReporte" class="btn btn-success">
                            <i class="fas fa-paper-plane"></i> Enviar Notificación
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal para cambiar nombre --}}
    <div class="modal fade" id="modalCambiarNombre" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header gradient-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-user-edit"></i> Cambiar Nombre</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="formCambiarNombre">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nuevoNombre" class="form-label fw-bold">Nuevo Nombre</label>
                            <input type="text" class="form-control" id="nuevoNombre" name="nombre" 
                                   value="{{ Auth::user()->nombre ?? '' }}" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal para cambiar contraseña --}}
    <div class="modal fade" id="modalCambiarPassword" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header gradient-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-key"></i> Cambiar Contraseña</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="formCambiarPassword">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="passwordActual" class="form-label fw-bold">Contraseña Actual</label>
                            <input type="password" class="form-control" id="passwordActual" name="current_password" required>
                        </div>
                        <div class="mb-3">
                            <label for="passwordNueva" class="form-label fw-bold">Nueva Contraseña</label>
                            <input type="password" class="form-control" id="passwordNueva" name="password" required minlength="6">
                        </div>
                        <div class="mb-3">
                            <label for="passwordConfirmar" class="form-label fw-bold">Confirmar Nueva Contraseña</label>
                            <input type="password" class="form-control" id="passwordConfirmar" name="password_confirmation" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Cambiar Contraseña
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal para cambiar foto --}}
    <div class="modal fade" id="modalCambiarFoto" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header gradient-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-camera"></i> Cambiar Imagen de Perfil</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="formCambiarFoto" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body text-center">
                        <div class="mb-3">
                            <img src="{{ Auth::user()->foto_usuario_url ?? asset('img/usuario_default.png') }}" 
                                 alt="Foto actual" class="rounded-circle mb-3" width="150" height="150" id="previewFoto" 
                                 style="border: 4px solid #667eea;">
                        </div>
                        <div class="mb-3">
                            <label for="nuevaFoto" class="form-label fw-bold">Seleccionar nueva imagen</label>
                            <input type="file" class="form-control" id="nuevaFoto" name="foto_usuario" 
                                   accept="image/jpeg,image/png" required>
                            <small class="form-text text-muted">Formatos permitidos: JPG, PNG. Tamaño máximo: 2MB</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-upload"></i> Subir Imagen
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Formulario oculto para logout --}}
    <form id="logout-form" action="{{ route('logout') ?? '#' }}" method="POST" style="display: none;">
        @csrf
    </form>

    {{-- Scripts de JavaScript --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function () {
            // Variables globales para funcionalidades
            let selectedMaterial = null;
            let selectedVehicle = null;
            let searchTimeout = null;
            let vehicleSearchTimeout = null;

            console.log('Sistema de materiales y vehículos inicializado');

            // Funcionalidad de secuencia secreta para acceso admin
            let secretSequence = [];
            const targetSequence = ['ArrowUp', 'ArrowDown', 'KeyK', 'ArrowDown', 'ArrowUp'];
            const sequenceDisplay = {
                'ArrowUp': '↑', 'ArrowDown': '↓', 'ArrowLeft': '←', 'ArrowRight': '→', 'KeyK': 'k'
            };
            
            // Detector de secuencia secreta
            $(document).keydown(function(e) {
                if (['ArrowUp', 'ArrowDown', 'ArrowLeft', 'ArrowRight', 'KeyK'].includes(e.code)) {
                    secretSequence.push(e.code);
                    
                    $('#secretIndicator').addClass('active');
                    setTimeout(() => $('#secretIndicator').removeClass('active'), 200);
                    
                    let displaySequence = secretSequence.map(key => sequenceDisplay[key] || key).join(' ');
                    $('#sequenceDisplay').text(displaySequence);
                    $('#secretSequence').addClass('show');
                    
                    setTimeout(() => $('#secretSequence').removeClass('show'), 2000);
                    
                    if (secretSequence.length > 5) {
                        secretSequence.shift();
                    }
                    
                    // Verificar si la secuencia es correcta
                    if (secretSequence.length === 5 && 
                        JSON.stringify(secretSequence) === JSON.stringify(targetSequence)) {
                        
                        $('#adminAccessBtn').addClass('show');
                        $('#sequenceDisplay').text('¡Acceso Desbloqueado! ✓');
                        $('#secretSequence').addClass('show');
                        
                        secretSequence = [];
                        
                        setTimeout(() => $('#secretSequence').removeClass('show'), 3000);
                        setTimeout(() => window.location.href = '/materials', 2000);
                    }
                }
            });

            // Configurar token CSRF para todas las peticiones AJAX
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Funciones de utilidad para manejo de errores y notificaciones
            function handleAjaxError(xhr) {
                const res = xhr.responseJSON;
                if (res?.errors) {
                    alert('Errores:\n' + Object.values(res.errors).flat().join('\n'));
                } else if (res?.message) {
                    alert('Error: ' + res.message);
                } else {
                    alert('Error: ' + xhr.responseText);
                }
            }

            function showNotificationSuccess(message) {
                $('#notificationMessage').text(message);
                $('#notificationSuccess').addClass('show');
                setTimeout(() => $('#notificationSuccess').removeClass('show'), 4000);
            }

            // Función para abrir modal de material con detalles
            function openMaterialModal(mode, data = {}) {
                const modal = $('#modalMaterial');
                const title = $('#modalMaterialTitle');
                const body = $('#modalMaterialBody');
                const footer = $('#modalMaterialFooter');
                
                title.text('Detalles del Material');
                body.html(`
                    <div class="mb-3"><strong>ID:</strong> ${data.id_material}</div>
                    <div class="mb-3"><strong>Clave:</strong> ${data.clave_material}</div>
                    <div class="mb-3"><strong>Descripción:</strong> ${data.descripcion}</div>
                    <div class="mb-3"><strong>Genérico:</strong> ${data.generico}</div>
                    <div class="mb-3"><strong>Clasificación:</strong> ${data.clasificacion}</div>
                    <div class="mb-3"><strong>Existencia:</strong> ${data.existencia}</div>
                    <div class="mb-3"><strong>Costo ($):</strong> ${data.costo_promedio}</div>
                `);
                footer.html('<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>');
                modal.modal('show');
            }

            // Función de búsqueda de materiales con AJAX
            function searchMaterials(query) {
                console.log('Iniciando búsqueda de materiales:', query);
                
                if (query.length < 2) {
                    $('#searchResultsDropdown').hide().empty();
                    $('#btnAgregarMaterial').prop('disabled', true);
                    selectedMaterial = null;
                    return;
                }

                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    $.ajax({
                        url: '/materiales/search',
                        method: 'GET',
                        data: { q: query },
                        success: function(materiales) {
                            console.log('Materiales encontrados:', materiales);
                            
                            const results = $('#searchResultsDropdown');
                            results.empty();
                            
                            if (Array.isArray(materiales) && materiales.length > 0) {
                                materiales.forEach((material) => {
                                    const item = $(`
                                        <div class="search-result-item" 
                                             data-id="${material.id_material}" 
                                             data-clave="${material.clave_material}" 
                                             data-descripcion="${material.descripcion}">
                                            <div class="material-details">
                                                <div class="material-code">${material.clave_material}</div>
                                                <div>${material.descripcion}</div>
                                                <div class="material-meta">
                                                    ID: ${material.id_material} | 
                                                    Genérico: ${material.generico || 'N/A'} | 
                                                    Clasificación: ${material.clasificacion || 'N/A'}
                                                </div>
                                            </div>
                                            <div class="material-stock">Stock: ${material.existencia}</div>
                                        </div>
                                    `);
                                    results.append(item);
                                });
                                results.show();
                            } else {
                                results.html('<div class="search-result-item">No se encontraron materiales</div>').show();
                            }
                        },
                        error: function(xhr) {
                            console.error('Error en búsqueda de materiales:', xhr);
                            $('#searchResultsDropdown').html('<div class="search-result-item" style="color: red;">Error en la búsqueda</div>').show();
                        }
                    });
                }, 300);
            }

            // Función de búsqueda de vehículos con AJAX
            function searchVehicles(query) {
                console.log('Iniciando búsqueda de vehículos:', query);
                
                if (query.length < 2) {
                    $('#vehicleResultsDropdown').hide().empty();
                    selectedVehicle = null;
                    return;
                }

                clearTimeout(vehicleSearchTimeout);
                vehicleSearchTimeout = setTimeout(() => {
                    $.ajax({
                        url: '/vehiculos/search',
                        method: 'GET',
                        data: { q: query },
                        success: function(vehiculos) {
                            console.log('Vehículos encontrados:', vehiculos);
                            
                            const results = $('#vehicleResultsDropdown');
                            results.empty();
                            
                            if (Array.isArray(vehiculos) && vehiculos.length > 0) {
                                vehiculos.forEach((vehiculo) => {
                                    const item = $(`
                                        <div class="vehicle-result-item" 
                                             data-id="${vehiculo.id}" 
                                             data-eco="${vehiculo.eco}"
                                             data-placas="${vehiculo.placas || ''}"
                                             data-marca="${vehiculo.marca || ''}"
                                             data-modelo="${vehiculo.modelo || ''}"
                                             data-anio="${vehiculo.anio || ''}"
                                             data-kilometraje="${vehiculo.kilometraje || ''}"
                                             data-conductor="${vehiculo.conductor_habitual || ''}">
                                            <div class="vehicle-info">
                                                <div class="vehicle-eco">ECO: ${vehiculo.eco}</div>
                                                <div class="vehicle-meta">
                                                    ${vehiculo.placas ? 'Placas: ' + vehiculo.placas : ''} |
                                                    ${vehiculo.marca ? vehiculo.marca : ''} ${vehiculo.modelo ? vehiculo.modelo : ''}
                                                    ${vehiculo.anio ? '| Año: ' + vehiculo.anio : ''}
                                                    ${vehiculo.kilometraje ? '| KM: ' + vehiculo.kilometraje.toLocaleString() : ''}
                                                    ${vehiculo.conductor_habitual ? '| Conductor: ' + vehiculo.conductor_habitual : ''}
                                                </div>
                                            </div>
                                            <div class="vehicle-status">${vehiculo.estatus}</div>
                                        </div>
                                    `);
                                    results.append(item);
                                });
                                results.show();
                            } else {
                                results.html('<div class="vehicle-result-item">No se encontraron vehículos</div>').show();
                            }
                        },
                        error: function(xhr) {
                            console.error('Error en búsqueda de vehículos:', xhr);
                            $('#vehicleResultsDropdown').html('<div class="vehicle-result-item" style="color: red;">Error en la búsqueda</div>').show();
                        }
                    });
                }, 300);
            }

            // Event listeners para búsquedas
            $('#materialSearch').on('input', function() {
                const query = $(this).val().trim();
                searchMaterials(query);
            });

            $('#vehicleSearch').on('input', function() {
                const query = $(this).val().trim();
                searchVehicles(query);
            });

            // Selección de material desde dropdown
            $(document).on('click', '.search-result-item', function() {
                const id = $(this).data('id');
                if (id) {
                    selectedMaterial = {
                        id: id,
                        clave: $(this).data('clave'),
                        descripcion: $(this).data('descripcion')
                    };
                    
                    $('#materialSearch').val(`${selectedMaterial.clave} - ${selectedMaterial.descripcion}`);
                    $('#searchResultsDropdown').hide();
                    $('#btnAgregarMaterial').prop('disabled', false);
                    
                    console.log('Material seleccionado:', selectedMaterial);
                }
            });

            // Selección de vehículo y autocompletado de campos
            $(document).on('click', '.vehicle-result-item', function() {
                const vehicleData = {
                    id: $(this).data('id'),
                    eco: $(this).data('eco'),
                    placas: $(this).data('placas'),
                    marca: $(this).data('marca'),
                    modelo: $(this).data('modelo'),
                    anio: $(this).data('anio'),
                    kilometraje: $(this).data('kilometraje'),
                    conductor: $(this).data('conductor')
                };

                selectedVehicle = vehicleData;
                
                // Autocompletar todos los campos del vehículo
                $('#ecoField').val(vehicleData.eco || '');
                $('#placasField').val(vehicleData.placas || '');
                $('#marcaField').val(vehicleData.marca || '');
                $('#anoField').val(vehicleData.anio || '');
                $('#kmField').val(vehicleData.kilometraje || '');
                $('#conductorField').val(vehicleData.conductor || '');
                $('#vehicleIdField').val(vehicleData.id || '');
                
                $('#vehicleSearch').val(`ECO: ${vehicleData.eco} - ${vehicleData.marca || ''} ${vehicleData.modelo || ''}`);
                $('#vehicleResultsDropdown').hide();
                
                console.log('Vehículo seleccionado y campos autocompletados:', vehicleData);
            });

            // Ocultar dropdowns al hacer click fuera
            $(document).on('click', function(e) {
                if (!$(e.target).closest('#materialSearch, #searchResultsDropdown').length) {
                    $('#searchResultsDropdown').hide();
                }
                if (!$(e.target).closest('#vehicleSearch, #vehicleResultsDropdown').length) {
                    $('#vehicleResultsDropdown').hide();
                }
            });

            // Funciones de gestión de materiales seleccionados
            function addMaterial(materialInfo) {
                let exists = false;
                $("#selectedMaterialsTable tbody tr").each(function () {
                    let id = $(this).data('id');
                    if (id == materialInfo.id) {
                        let quantityInput = $(this).find('input.cantidad');
                        quantityInput.val(parseInt(quantityInput.val()) + 1);
                        exists = true;
                    }
                });
                
                if (!exists) {
                    let row = `<tr data-id="${materialInfo.id}">
                        <td>${materialInfo.descripcion}</td>
                        <td><input type="number" class="form-control cantidad" value="1" min="1" style="width:80px"></td>
                        <td>
                          <button type="button" class="btn btn-sm btn-success btnInc" title="Aumentar"><i class="fas fa-plus"></i></button>
                          <button type="button" class="btn btn-sm btn-warning btnDec" title="Disminuir"><i class="fas fa-minus"></i></button>
                          <button type="button" class="btn btn-sm btn-danger btnRemove" title="Eliminar"><i class="fas fa-trash"></i></button>
                        </td>
                      </tr>`;
                    $("#selectedMaterialsTable tbody").append(row);
                }
                updateMaterialsData();
            }

            function updateMaterialsData() {
                let materials = [];
                $("#selectedMaterialsTable tbody tr").each(function () {
                    let id = $(this).data('id');
                    let description = $(this).find('td:first').text();
                    let quantity = $(this).find('input.cantidad').val();
                    materials.push({ id: id, descripcion: description, cantidad: quantity });
                });
                $('#materialsData').val(JSON.stringify(materials));
            }

            // Botón para agregar material seleccionado
            $("#btnAgregarMaterial").click(function () {
                if (selectedMaterial) {
                    addMaterial({
                        id: selectedMaterial.id,
                        descripcion: `${selectedMaterial.clave} - ${selectedMaterial.descripcion}`
                    });
                    
                    $('#materialSearch').val('');
                    selectedMaterial = null;
                    $('#searchResultsDropdown').hide();
                    $('#btnAgregarMaterial').prop('disabled', true);
                    
                    console.log('Material agregado exitosamente');
                } else {
                    alert("Por favor, selecciona un material de la lista de búsqueda");
                }
            });

            // Acciones en tabla de materiales seleccionados
            $("#selectedMaterialsTable").on("click", ".btnInc", function () {
                let input = $(this).closest('tr').find('input.cantidad');
                input.val(parseInt(input.val()) + 1);
                updateMaterialsData();
            });

            $("#selectedMaterialsTable").on("click", ".btnDec", function () {
                let input = $(this).closest('tr').find('input.cantidad');
                if (parseInt(input.val()) > 1) {
                    input.val(parseInt(input.val()) - 1);
                    updateMaterialsData();
                }
            });

            $("#selectedMaterialsTable").on("click", ".btnRemove", function () {
                $(this).closest('tr').remove();
                updateMaterialsData();
            });

            // Cargar usuarios por lugar
            function loadUsersByPlace(placeId) {
                console.log('Cargando usuarios para lugar:', placeId);
                
                if (!placeId) {
                    $('#usuarioReporta').html('<option value="">-- Selecciona el usuario que reporta --</option>');
                    return;
                }

                $.ajax({
                    url: `/lugares/${placeId}/usuarios`,
                    method: 'GET',
                    beforeSend: function() {
                        $('#usuarioReporta').html('<option value="">Cargando usuarios...</option>');
                    },
                    success: function(response) {
                        console.log('Usuarios cargados:', response);
                        
                        let options = '<option value="">-- Selecciona el usuario que reporta --</option>';
                        if (response.usuarios && Array.isArray(response.usuarios) && response.usuarios.length > 0) {
                            response.usuarios.forEach(function(usuario) {
                                options += `<option value="${usuario.id}" data-nombre="${usuario.name}" data-correo="${usuario.email}">
                                              ${usuario.name} (${usuario.email})
                                            </option>`;
                            });
                        } else {
                            options += '<option value="" disabled>No hay usuarios disponibles en este lugar</option>';
                        }
                        $('#usuarioReporta').html(options);
                    },
                    error: function(xhr) {
                        console.error('Error al cargar usuarios:', xhr);
                        $('#usuarioReporta').html(`<option value="" disabled>Error al cargar usuarios</option>`);
                    }
                });
            }

            // Limpiar campos de vehículo al cambiar lugar
            function loadVehiclesByPlace(placeId) {
                console.log('Lugar cambiado, limpiar búsqueda de vehículos para lugar:', placeId);
                $('#vehicleSearch').val('');
                $('#ecoField').val('');
                $('#placasField').val('');
                $('#marcaField').val('');
                $('#anoField').val('');
                $('#kmField').val('');
                $('#conductorField').val('');
                $('#vehicleIdField').val('');
                $('#vehicleResultsDropdown').hide();
                selectedVehicle = null;
            }

            // Event listeners para cambios en formulario
            $('#id_lugar_reporte').on('change', function() {
                let placeId = $(this).val();
                loadUsersByPlace(placeId);
                loadVehiclesByPlace(placeId);
            });

            $('#usuarioReporta').on('change', function() {
                let selectedOption = $(this).find('option:selected');
                $('#nombreUsuarioReporta').val(selectedOption.data('nombre') || '');
                $('#correoUsuarioReporta').val(selectedOption.data('correo') || '');
            });

            // Cargar usuarios automáticamente al abrir modal
            $('#modalReporte').on('shown.bs.modal', function() {
                const currentPlace = $('#id_lugar_reporte').val();
                if (currentPlace) {
                    loadUsersByPlace(currentPlace);
                }
            });

            // Botones de reporte
            $('.btnDisminuir, #btnReporteFallas').click(function () {
                if ($(this).hasClass('btnDisminuir')) {
                    let row = $(this).closest('tr');
                    let materialId = $(this).data('id');
                    let materialCode = row.find('td:nth-child(2)').text();
                    let materialDescription = row.find('td:nth-child(3)').text();
                    $("#selectedMaterialsTable tbody").html("");
                    addMaterial({ 
                        id: materialId, 
                        descripcion: `${materialCode} - ${materialDescription}` 
                    });
                } else {
                    $("#selectedMaterialsTable tbody").html("");
                    updateMaterialsData();
                }
                $("#modalReporte").modal("show");
            });

            // Envío del formulario de reporte
            $('#formReporte').submit(function(e) {
                e.preventDefault();
                
                if (!$('#id_lugar_reporte').val()) {
                    alert('Por favor selecciona un lugar');
                    return;
                }
                if (!$('#usuarioReporta').val()) {
                    alert('Por favor selecciona el usuario que reporta');
                    return;
                }
                
                updateMaterialsData();
                const formData = $(this).serialize();
                
                $('#btnEnviarReporte').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Enviando...');
                
                $.post('/notificaciones', formData)
                    .done(function(response) {
                        if (response.success) {
                            $('#modalReporte').modal('hide');
                            showNotificationSuccess(response.message || '¡Notificación enviada correctamente!');
                            
                            $('#formReporte')[0].reset();
                            $("#selectedMaterialsTable tbody").html("");
                            $('#usuarioReporta').html('<option value="">-- Selecciona el usuario que reporta --</option>');
                            selectedMaterial = null;
                            selectedVehicle = null;
                            $('#btnAgregarMaterial').prop('disabled', true);
                        } else {
                            alert('Error: ' + (response.message || 'Error desconocido'));
                        }
                    })
                    .fail(function(xhr) {
                        handleAjaxError(xhr);
                    })
                    .always(function() {
                        $('#btnEnviarReporte').prop('disabled', false).html('<i class="fas fa-paper-plane"></i> Enviar Notificación');
                    });
            });

            // Ver detalles de material
            $('.btnVer').click(function () {
                let id = $(this).data('id');
                $.get(`/materiales/${id}`, function (response) {
                    openMaterialModal('view', response.data);
                });
            });

            // Limpiar formulario al cerrar modal
            $('#modalReporte').on('hidden.bs.modal', function() {
                $('#formReporte')[0].reset();
                $("#selectedMaterialsTable tbody").html("");
                $('#usuarioReporta').html('<option value="">-- Selecciona el usuario que reporta --</option>');
                $('#btnEnviarReporte').prop('disabled', false).html('<i class="fas fa-paper-plane"></i> Enviar Notificación');
                
                // Limpiar campos de búsqueda y vehículo
                $('#materialSearch').val('');
                $('#vehicleSearch').val('');
                $('#searchResultsDropdown').hide();
                $('#vehicleResultsDropdown').hide();
                
                $('#ecoField').val('');
                $('#placasField').val('');
                $('#marcaField').val('');
                $('#anoField').val('');
                $('#kmField').val('');
                $('#conductorField').val('');
                $('#vehicleIdField').val('');
                
                selectedMaterial = null;
                selectedVehicle = null;
                $('#btnAgregarMaterial').prop('disabled', true);
            });

            // Preview de foto antes de subir
            $('#nuevaFoto').change(function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('#previewFoto').attr('src', e.target.result);
                    };
                    reader.readAsDataURL(file);
                }
            });

            // Formularios de gestión de usuario
            $('#formCambiarNombre').submit(function(e) {
                e.preventDefault();
                
                const formData = $(this).serialize();
                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();
                
                submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');
                
                $.post('/usuario/update-name', formData)
                    .done(function(response) {
                        $('#modalCambiarNombre').modal('hide');
                        showNotificationSuccess(response.message);
                        setTimeout(() => location.reload(), 2000);
                    })
                    .fail(handleAjaxError)
                    .always(function() {
                        submitBtn.prop('disabled', false).html(originalText);
                    });
            });

            $('#formCambiarPassword').submit(function(e) {
                e.preventDefault();
                
                const newPassword = $('#passwordNueva').val();
                const confirmPassword = $('#passwordConfirmar').val();
                
                if (newPassword !== confirmPassword) {
                    alert('Las contraseñas no coinciden');
                    return;
                }
                
                const formData = $(this).serialize();
                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();
                
                submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Cambiando...');
                
                $.post('/usuario/update-password', formData)
                    .done(function(response) {
                        $('#modalCambiarPassword').modal('hide');
                        $('#formCambiarPassword')[0].reset();
                        showNotificationSuccess(response.message);
                    })
                    .fail(handleAjaxError)
                    .always(function() {
                        submitBtn.prop('disabled', false).html(originalText);
                    });
            });

            $('#formCambiarFoto').submit(function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();
                
                submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Subiendo...');
                
                $.ajax({
                    url: '/usuario/update-photo',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false
                })
                .done(function(response) {
                    $('#modalCambiarFoto').modal('hide');
                    showNotificationSuccess(response.message);
                    if (response.foto_url) {
                        $('.user-avatar-new, #previewFoto').attr('src', response.foto_url);
                    }
                })
                .fail(handleAjaxError)
                .always(function() {
                    submitBtn.prop('disabled', false).html(originalText);
                });
            });

            console.log('Sistema completamente funcional - Búsqueda de materiales y vehículos con autocompletado lista');
        });
    </script>
</body>
</html>