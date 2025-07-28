<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('img/logo.png') }}">
    <title>Lista de Materiales - FontTrack</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
<style>
        /* Base styles */
        body {
            font-size: 0.9em;
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #FCE8D5 0%, #F6B88F 100%);
            color: #634D3B;
            text-align: center;
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }

        h2 {
            font-size: 1.8em;
            background: linear-gradient(45deg, #E38B5B, #F6B88F);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: bold;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }

        /* Reusable classes */
        .gradient-primary {
            background: linear-gradient(135deg, #F6B88F, #E38B5B);
        }

        .gradient-secondary {
            background: linear-gradient(135deg, #E38B5B, #D1784C);
        }

        .shadow-hover {
            transition: all 0.3s ease;
        }

        .shadow-hover:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(227, 139, 91, 0.4);
        }

        /* Loading overlay */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(252, 232, 213, 0.95);
            backdrop-filter: blur(10px);
            z-index: 9999;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .loading-overlay.show {
            opacity: 1;
            visibility: visible;
        }

        .bubble-loader {
            position: relative;
            width: 300px;
            height: 60px;
            margin-bottom: 30px;
        }

        .bubble-progress {
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            background: linear-gradient(45deg, #E38B5B, #F6B88F);
            border-radius: 30px;
            width: 0%;
            transition: width 0.3s ease;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(227, 139, 91, 0.3);
        }

        .bubble-progress::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
            animation: shimmer 2s infinite;
        }

        @keyframes shimmer {
            0% { left: -100%; }
            100% { left: 100%; }
        }

        .bubble-container {
            position: absolute;
            top: 50%;
            left: 0;
            width: 100%;
            height: 20px;
            transform: translateY(-50%);
            overflow: hidden;
        }

        .bubble {
            position: absolute;
            width: 12px;
            height: 12px;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 50%;
            animation: floatBubble 3s infinite ease-in-out;
        }

        .bubble:nth-child(1) { left: 10%; animation-delay: 0s; }
        .bubble:nth-child(2) { left: 25%; animation-delay: 0.5s; }
        .bubble:nth-child(3) { left: 40%; animation-delay: 1s; }
        .bubble:nth-child(4) { left: 55%; animation-delay: 1.5s; }
        .bubble:nth-child(5) { left: 70%; animation-delay: 2s; }
        .bubble:nth-child(6) { left: 85%; animation-delay: 2.5s; }

        @keyframes floatBubble {
            0%, 100% { 
                transform: translateY(0) scale(1);
                opacity: 0.8;
            }
            50% { 
                transform: translateY(-15px) scale(1.2);
                opacity: 1;
            }
        }

        .progress-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 30px;
            border: 2px solid rgba(227, 139, 91, 0.3);
            box-shadow: inset 0 2px 10px rgba(0,0,0,0.1);
        }

        .loading-text {
            font-size: 1.2em;
            font-weight: bold;
            color: #E38B5B;
            margin-top: 20px;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.6; }
        }

        .loading-percentage {
            font-size: 2em;
            font-weight: bold;
            color: #634D3B;
            margin-bottom: 10px;
        }

        /* Modal animation */
        @keyframes modalBubbleIn {
            0% { 
                transform: scale(0.3) translateY(100px) rotate(-10deg); 
                opacity: 0; 
                filter: blur(10px);
            }
            50% { 
                transform: scale(1.05) translateY(-10px) rotate(2deg); 
                opacity: 0.8; 
                filter: blur(2px);
            }
            70% { 
                transform: scale(0.95) translateY(5px) rotate(-1deg); 
                opacity: 0.9; 
                filter: blur(1px);
            }
            100% { 
                transform: scale(1) translateY(0) rotate(0deg); 
                opacity: 1; 
                filter: blur(0px);
            }
        }

        .modal.show .modal-dialog {
            animation: modalBubbleIn 0.7s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }

        .modal-content {
            overflow: hidden;
            border-radius: 20px;
            box-shadow: 0 25px 80px rgba(0, 0, 0, 0.4);
            border: 3px solid rgba(246, 184, 143, 0.3);
            background: linear-gradient(135deg, #fff 0%, #FCE8D5 100%);
        }

        .modal-backdrop {
            background: linear-gradient(45deg, rgba(0,0,0,0.5), rgba(227, 139, 91, 0.2));
            backdrop-filter: blur(8px);
        }

        /* Navbar */
        .navbar {
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
            padding: 12px 20px;
            border-bottom: 4px solid #D1784C;
            position: relative;
            overflow: hidden;
        }

        .navbar::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            animation: navbarShimmer 4s infinite;
        }

        @keyframes navbarShimmer {
            0% { left: -100%; }
            100% { left: 100%; }
        }

        .navbar .logo {
            transition: all 0.4s ease;
            filter: drop-shadow(2px 2px 4px rgba(0,0,0,0.2));
        }

        .navbar .logo:hover {
            transform: scale(1.05);
            filter: brightness(1.1) drop-shadow(4px 4px 8px rgba(0,0,0,0.3));
        }

        .navbar .navbar-brand {
            color: #634D3B;
            font-weight: bold;
            font-size: 1.4em;
            transition: all 0.3s ease;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }

        .navbar .navbar-brand:hover {
            color: #fff;
            transform: scale(1.05);
            text-shadow: 2px 2px 8px rgba(0,0,0,0.3);
        }

        .navbar .navbar-nav .nav-link {
            color: #634D3B;
            padding: 12px 18px;
            font-weight: bold;
            transition: all 0.3s ease;
            border-radius: 8px;
            position: relative;
            overflow: hidden;
        }

        .navbar .navbar-nav .nav-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s ease;
        }

        .navbar .navbar-nav .nav-link:hover::before {
            left: 100%;
        }

        .navbar .navbar-nav .nav-link:hover {
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }

        /* Container */
        .container {
            background: linear-gradient(135deg, #fff 0%, #FCE8D5 100%);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
            margin: 20px auto;
            border: 2px solid rgba(246, 184, 143, 0.3);
            position: relative;
            overflow: hidden;
        }

        .container::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(246, 184, 143, 0.1) 0%, transparent 70%);
            animation: rotate 20s linear infinite;
        }

        @keyframes rotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Search container */
        .search-container {
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
            position: relative;
            overflow: hidden;
        }

        .search-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            animation: searchShimmer 3s infinite;
        }

        @keyframes searchShimmer {
            0% { left: -100%; }
            100% { left: 100%; }
        }

        /* Input field styles - corregidos */
        .form-control {
            border: 2px solid #E0C4AA;
            border-radius: 8px;
            padding: 8px 12px;
            font-size: 14px;
            transition: all 0.3s ease;
            background: #fffaf6;
        }

        .form-control:focus {
            border-color: #E38B5B;
            outline: none;
            box-shadow: 0 0 0 3px rgba(227, 139, 91, 0.2);
            background: #fff;
        }

        .input-group {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        .input-group .form-control {
            border-right: none;
            border-radius: 8px 0 0 8px;
        }

        .input-group .btn {
            border-left: none;
            border-radius: 0 8px 8px 0;
            border: 2px solid #E0C4AA;
            border-left: none;
        }

        /* Buttons */
        .btn, .btn-info, .btn-warning, .btn-danger {
            border: none;
            border-radius: 12px;
            padding: 14px 28px;
            font-weight: bold;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn { background: linear-gradient(135deg, #E38B5B, #D1784C); color: #fff; }
        .btn-info { background: linear-gradient(135deg, #88C0D0, #6AA8C0); }
        .btn-warning { background: linear-gradient(135deg, #E5A34D, #D1903D); }
        .btn-danger { background: linear-gradient(135deg, #D9534F, #C9302C); }

        .btn::before, .btn-info::before, .btn-warning::before, .btn-danger::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
            transition: left 0.5s ease;
        }

        .btn:hover::before, .btn-info:hover::before, .btn-warning:hover::before, .btn-danger:hover::before {
            left: 100%;
        }

        .btn:hover, .btn-info:hover, .btn-warning:hover, .btn-danger:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(227, 139, 91, 0.4);
        }

        /* Table */
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
            position: relative;
        }

        .table th::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 2px;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.5), transparent);
        }

        .table td {
            padding: 18px;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
        }

        .table tbody tr {
            transition: all 0.3s ease;
            border-bottom: 1px solid rgba(246, 184, 143, 0.3);
        }

        .table tbody tr:hover {
            background: linear-gradient(135deg, rgba(246, 184, 143, 0.2), rgba(227, 139, 91, 0.1));
            transform: scale(1.02);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            padding: 30px;
            list-style: none;
            margin: 0;
        }

        .pagination li a, .pagination li span {
            text-decoration: none;
            padding: 14px 20px;
            background: linear-gradient(135deg, #E38B5B, #D1784C);
            color: white;
            border-radius: 12px;
            font-weight: bold;
            transition: all 0.3s ease;
            border: 2px solid transparent;
            box-shadow: 0 4px 12px rgba(227, 139, 91, 0.3);
        }

        .pagination li a:hover {
            background: linear-gradient(135deg, #D1784C, #B8663A);
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(227, 139, 91, 0.4);
        }

        .pagination .active span {
            background: linear-gradient(135deg, #F6B88F, #E38B5B);
            color: #fff;
            border: 2px solid #634D3B;
            transform: scale(1.1);
            box-shadow: 0 6px 20px rgba(246, 184, 143, 0.5);
        }

        /* User profile */
        .user-profile {
            display: flex;
            align-items: center;
            cursor: pointer;
            padding: 8px 15px;
            border-radius: 25px;
            transition: all 0.3s ease;
        }

        .user-profile:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: scale(1.05);
        }

        .user-profile img {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            margin-right: 12px;
            border: 3px solid #fff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }

        .user-profile img:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
        }

        .user-profile .user-name {
            font-weight: bold;
            color: #634D3B;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
        }

        .user-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            background: linear-gradient(135deg, #fff 0%, #FCE8D5 100%);
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
            padding: 15px 0;
            min-width: 200px;
            z-index: 1000;
            display: none;
            border: 2px solid rgba(246, 184, 143, 0.3);
        }

        .user-dropdown.show {
            display: block;
            animation: dropdownFadeIn 0.4s ease;
        }

        @keyframes dropdownFadeIn {
            from { 
                opacity: 0; 
                transform: translateY(-15px) scale(0.95); 
            }
            to { 
                opacity: 1; 
                transform: translateY(0) scale(1); 
            }
        }

        .user-dropdown a {
            display: block;
            padding: 12px 20px;
            color: #634D3B;
            text-decoration: none;
            transition: all 0.3s ease;
            border-radius: 8px;
            margin: 0 10px;
        }

        .user-dropdown a:hover {
            background: linear-gradient(135deg, rgba(227, 139, 91, 0.2), rgba(246, 184, 143, 0.3));
            color: #E38B5B;
            transform: translateX(5px);
        }

        /* Secret command indicator */
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

        .secret-indicator:hover {
            opacity: 1;
            transform: scale(1.1);
        }

        .secret-indicator.active {
            animation: secretPulse 1s infinite;
            opacity: 1;
        }

        @keyframes secretPulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.2); }
        }

        /* Secret sequence display */
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

        /* Admin access button */
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

        .admin-access-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(102, 126, 234, 0.4);
            color: white;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .navbar {
                padding: 10px 18px;
            }

            .navbar .navbar-brand {
                font-size: 1.2em;
            }

            .navbar .navbar-nav .nav-link {
                font-size: 0.9em;
                padding: 10px 15px;
            }
            
            .user-profile {
                margin-top: 15px;
            }
            
            .user-dropdown {
                position: static;
                margin-top: 10px;
            }

            .container {
                padding: 20px;
                margin: 10px;
            }

            .search-container {
                padding: 20px;
            }

            .bubble-loader {
                width: 250px;
                height: 50px;
            }

            .loading-percentage {
                font-size: 1.5em;
            }

            .secret-indicator {
                width: 45px;
                height: 45px;
                font-size: 1rem;
                bottom: 15px;
                right: 15px;
            }
        }
    </style>
</head>
<body>
    <!-- Secret sequence display -->
    <div class="secret-sequence" id="secretSequence">
        Secuencia: <span id="sequenceDisplay"></span>
    </div>

    <!-- Admin access button -->
    <button class="admin-access-btn" id="adminAccessBtn" onclick="location.href='/materials'">
        <i class="fas fa-crown"></i> Acceso Administrador
    </button>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <img src="{{ asset('img/FontTrack.png') }}" alt="logo" width="100" height="70" class="me-2 logo">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="ms-auto d-flex align-items-center">
                    <img src="{{ Auth::user()->foto_usuario_url ?? asset('img/usuario_default.png') }}" alt="Foto de perfil" width="45" height="45" class="rounded-circle me-2">
                    <span>{{ Auth::user()->nombre }}</span>
                    <div class="dropdown ms-2">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="bi bi-box-arrow-right"></i> Cerrar sesión
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Modal para subir archivos del Cardex -->
    <div class="modal fade" id="modalCardex" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="formCardex" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">¿Archivos del Kardex?</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="id_lugar" class="form-label">Selecciona el Lugar:</label>
                            <select id="id_lugar" name="id_lugar" class="form-select" required>
                                <option value="">-- Selecciona un lugar --</option>
                                @foreach($lugares as $lugar)
                                    <option value="{{ $lugar->id_lugar }}">{{ $lugar->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="archivo_cardex" class="form-label">Seleccionar Archivo Excel:</label>
                            <input type="file" id="archivo_cardex" name="archivo_cardex" class="form-control" accept=".xlsx,.xls" required>
                            <div class="form-text">El archivo debe contener: Clave Material, Descripción, Genérico, Clasificación, Existencia, Costo Promedio.</div>
                        </div>
                        <div class="alert alert-info">
                            <strong>Formato requerido:</strong>
                            <ul class="mb-0">
                                <li>Clave Material</li>
                                <li>Descripción</li>
                                <li>Genérico</li>
                                <li>Clasificación</li>
                                <li>Existencia</li>
                                <li>Costo Promedio</li>
                            </ul>
                        </div>
                        <div id="progreso" class="mb-3" style="display: none;">
                            <div class="progress">
                                <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                            </div>
                            <small class="text-muted">Procesando archivo...</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary" id="btnSubirCardex">Subir Archivo</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Contenido principal de Materiales -->
    <div class="container mt-4">
        <h2 class="mb-3">Lista de Materiales</h2>
        <div class="row mb-3">
            <div class="col-md-6">
                <div class="btn-group">
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalCardex">📁 Subir Kardex</button>
                    <button class="btn btn-warning" id="btnReporteFallas">📋 Reporte de Fallas</button>
                </div>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('materiales.export') }}" class="btn btn-outline-primary me-2">📤 Exportar Excel</a>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalMaterial" id="btnNuevoMaterial">Registrar Material</button>
            </div>
        </div>
        
        <!-- Barra de búsqueda -->
        <div class="row mb-3">
            <div class="col-md-8">
                <form class="d-flex" action="{{ route('materiales.index') }}" method="GET">
                    <input class="form-control me-2" type="search" name="query" placeholder="Buscar material" value="{{ request('query') }}">
                    <button class="btn btn-outline-success" type="submit"><i class="bi bi-search"></i></button>
                </form>
            </div>
        </div>

        <!-- Tabla de materiales -->
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
                    @foreach($materiales as $material)
                        <tr data-id="{{ $material->id_material }}">
                            <td>{{ $material->id_material }}</td>
                            <td>{{ $material->clave_material }}</td>
                            <td>{{ $material->descripcion }}</td>
                            <td>{{ $material->generico }}</td>
                            <td>{{ $material->clasificacion }}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-success btnAumentar" data-id="{{ $material->id_material }}" title="Aumentar existencia">
                                        <i class="bi bi-plus"></i>
                                    </button>
                                    <span class="btn btn-light disabled">{{ $material->existencia }}</span>
                                    <button class="btn btn-danger btnDisminuir" data-id="{{ $material->id_material }}" title="Reportar falla">
                                        <i class="bi bi-dash"></i>
                                    </button>
                                </div>
                            </td>
                            <td>{{ $material->costo_promedio }}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-info btnVer" data-id="{{ $material->id_material }}" data-bs-toggle="modal" data-bs-target="#modalMaterial">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <button class="btn btn-warning btnEditar" data-id="{{ $material->id_material }}" data-bs-toggle="modal" data-bs-target="#modalMaterial">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-danger btnEliminar" data-id="{{ $material->id_material }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Paginación -->
        <div class="d-flex justify-content-center">
            {{ $materiales->appends(['query' => request('query')])->links('pagination::bootstrap-5') }}
        </div>
    </div>

    <!-- Modal Material (Registro/Edición/Ver) -->
    <div class="modal fade" id="modalMaterial" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="formMaterial">
                    @csrf
                    <input type="hidden" id="materialId">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalMaterialTitle"></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body" id="modalMaterialBody"></div>
                    <div class="modal-footer" id="modalMaterialFooter"></div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Confirmación de Eliminación -->
    <div class="modal fade" id="modalEliminar" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmar Eliminación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>¿Estás seguro de que deseas eliminar este material?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="btnConfirmarEliminar">Eliminar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Aumentar Existencia -->
    <div class="modal fade" id="modalAumentar" tabindex="-1">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <form id="formAumentar">
                    @csrf
                    <input type="hidden" id="materialIdAumentar">
                    <div class="modal-header">
                        <h5 class="modal-title">Aumentar Existencia</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p><strong>Material:</strong> <span id="materialNombreAumentar"></span></p>
                        <p><strong>Existencia actual:</strong> <span id="existenciaActualAumentar"></span></p>
                        <div class="mb-3">
                            <label for="cantidadAumentar" class="form-label">Cantidad a aumentar:</label>
                            <input type="number" id="cantidadAumentar" name="cantidad" class="form-control" min="1" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-success">Aumentar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal de Reporte de Fallas / Uso de Materiales -->
    <div class="modal fade" id="modalFalla" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="formFalla">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">REPORTE DE FALLAS / USO DE MATERIALES</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="id_lugar_falla" class="form-label">Selecciona el Lugar:</label>
                            <select id="id_lugar_falla" name="id_lugar" class="form-select" required>
                                <option value="">-- Selecciona un lugar --</option>
                                @foreach($lugares as $lugar)
                                    <option value="{{ $lugar->id_lugar }}">{{ $lugar->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">No. ECO</label>
                                <input type="text" name="eco" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Placas</label>
                                <input type="text" name="placas" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Marca</label>
                                <input type="text" name="marca" class="form-control">
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Año</label>
                                <input type="text" name="ano" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">KM</label>
                                <input type="text" name="km" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Fecha</label>
                                <input type="date" name="fecha" class="form-control">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Nombre del Conductor</label>
                            <input type="text" name="nombre_conductor" class="form-control">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Descripción Servicio / Fallo</label>
                            <textarea name="descripcion" rows="3" class="form-control"></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Observaciones Técnicas del Trabajo Realizado</label>
                            <textarea name="observaciones" rows="3" class="form-control"></textarea>
                        </div>
                        
                        <h6 class="mt-3">Materiales a utilizar</h6>
                        <div class="mb-3">
                            <label for="materialBuscar" class="form-label">Buscar Material</label>
                            <input type="text" id="materialBuscar" class="form-control" list="materialesList" placeholder="Ingrese clave o descripción">
                            <datalist id="materialesList">
                                @foreach($materiales as $material)
                                    <option data-id="{{ $material->id_material }}" value="{{ $material->clave_material }} - {{ $material->descripcion }}"></option>
                                @endforeach
                            </datalist>
                            <button type="button" id="btnAgregarMaterial" class="btn btn-secondary mt-2">Agregar Material</button>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-sm" id="selectedMaterialsTable">
                                <thead>
                                    <tr>
                                        <th>Material</th>
                                        <th>Cantidad</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Nombre y firma de quien reporta</label>
                                <select name="usuario_reporta_id" id="usuarioReporta" class="form-select" required>
                                    <option value="">-- Selecciona el usuario que reporta --</option>
                                </select>
                                <input type="hidden" name="nombre_usuario_reporta" id="nombreUsuarioReporta">
                                <input type="hidden" name="correo_usuario_reporta" id="correoUsuarioReporta">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nombre y firma de quien revisó</label>
                                <input type="text" name="nombre_usuario_revisa" id="nombreUsuarioRevisa" class="form-control" readonly>
                                <input type="hidden" name="correo_usuario_revisa" id="correoUsuarioRevisa">
                                <input type="password" name="reviso_por" id="reviso_por" class="form-control mt-2" placeholder="Ingresa tu contraseña para firmar" required>
                                <small id="passwordError" class="text-danger" style="display: none;">Contraseña incorrecta</small>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Correo para enviar reporte</label>
                            <input type="email" name="correo_destino" class="form-control" placeholder="correo@ejemplo.com">
                        </div>
                        
                        <input type="hidden" name="materials" id="materialsData">
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="btnVerPDF" class="btn btn-info" disabled>Ver PDF</button>
                        <button type="button" id="btnEnviarReporte" class="btn btn-success" disabled>Enviar</button>
                        <button type="button" id="btnGuardarPDF" class="btn btn-primary" disabled>Guardar PDF</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" id="btnGuardarEnviar" class="btn btn-primary" disabled>Guardar y enviar PDF</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para confirmar contraseña antes de generar PDF -->
    <div class="modal fade" id="modalConfirmarPassword" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmar Identidad</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>¡ATENCIÓN!</strong> Al continuar, el registro se guardará permanentemente y no podrá ser eliminado.
                    </div>
                    <div class="mb-3">
                        <label for="passwordConfirm" class="form-label">Para generar el PDF, confirma tu contraseña:</label>
                        <input type="password" id="passwordConfirm" class="form-control" placeholder="Ingresa tu contraseña actual" required>
                    </div>
                    <div id="errorPassword" class="alert alert-danger d-none">
                        <i class="fas fa-times-circle"></i>
                        Contraseña incorrecta. Inténtalo de nuevo.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="btnConfirmarPassword" class="btn btn-warning">
                        <i class="fas fa-lock"></i> Confirmar y Generar PDF
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para visualizar el PDF generado -->
    <div class="modal fade" id="modalVerPDF" tabindex="-1">
        <div class="modal-dialog modal-lg" style="max-width: 90%;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Vista Previa del PDF</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="height: 80vh;">
                    <iframe id="iframePDF" src="" frameborder="0" style="width: 100%; height: 100%;"></iframe>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Form de logout oculto -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function () {
            // Secret sequence configuration
            let secretSequence = [];
            const targetSequence = ['ArrowUp', 'ArrowDown', 'KeyK', 'ArrowDown', 'ArrowUp'];
            const sequenceDisplay = {
                'ArrowUp': '↑',
                'ArrowDown': '↓',
                'ArrowLeft': '←',
                'ArrowRight': '→',
                'KeyK': 'K'
            };
            
            // Secret sequence tracking
            $(document).keydown(function(e) {
                // Only track specific keys
                if (['ArrowUp', 'ArrowDown', 'ArrowLeft', 'ArrowRight', 'KeyK'].includes(e.code)) {
                    secretSequence.push(e.code);
                    
                    // Show current sequence
                    let displaySequence = secretSequence.map(key => sequenceDisplay[key] || key).join(' ');
                    $('#sequenceDisplay').text(displaySequence);
                    $('#secretSequence').addClass('show');
                    
                    // Hide sequence display after 2 seconds
                    setTimeout(() => {
                        $('#secretSequence').removeClass('show');
                    }, 2000);
                    
                    // Keep only last 5 elements
                    if (secretSequence.length > 5) {
                        secretSequence.shift();
                    }
                    
                    // Check if sequence matches
                    if (secretSequence.length === 5 && 
                        JSON.stringify(secretSequence) === JSON.stringify(targetSequence)) {
                        
                        // Show admin access button
                        $('#adminAccessBtn').addClass('show');
                        
                        // Show success message
                        $('#sequenceDisplay').text('¡Acceso Desbloqueado! ✓');
                        $('#secretSequence').addClass('show');
                        
                        // Clear sequence after success
                        secretSequence = [];
                        
                        // Show alert instead of redirect (for demo purposes)
                        setTimeout(() => {
                            $('#secretSequence').removeClass('show');
                            alert('🎉 ¡Acceso de Administrador Desbloqueado!\n\nEn el sistema real, esto te redirigiría a:\n/materials (Vista completa de administrador)');
                        }, 2000);
                    }
                }
            });

            // Set CSRF token for all AJAX requests
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Variable for temporary report ID and password verification
            let reporteId = null;
            let passwordVerified = false;

            // Generic AJAX error handler
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

            // Dynamic material modal
            function openMaterialModal(mode, data = {}) {
                const modal = $('#modalMaterial');
                const title = $('#modalMaterialTitle');
                const body = $('#modalMaterialBody');
                const footer = $('#modalMaterialFooter');
                
                if (mode === 'view') {
                    title.text('Detalles del Material');
                    body.html(`
                        <div class="mb-3"><strong>Clave:</strong> ${data.clave_material}</div>
                        <div class="mb-3"><strong>Descripción:</strong> ${data.descripcion}</div>
                        <div class="mb-3"><strong>Genérico:</strong> ${data.generico}</div>
                        <div class="mb-3"><strong>Clasificación:</strong> ${data.clasificacion}</div>
                        <div class="mb-3"><strong>Existencia:</strong> ${data.existencia}</div>
                        <div class="mb-3"><strong>Costo ($):</strong> ${data.costo_promedio}</div>
                    `);
                    footer.html('<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>');
                } else if (mode === 'edit' || mode === 'create') {
                    title.text(mode === 'edit' ? 'Editar Material' : 'Registrar Material');
                    body.html(`
                        <div class="mb-3">
                            <label for="clave" class="form-label">Clave:</label>
                            <input type="text" id="clave" name="clave_material" class="form-control" value="${data.clave_material || ''}" required>
                        </div>
                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción:</label>
                            <input type="text" id="descripcion" name="descripcion" class="form-control" value="${data.descripcion || ''}" required>
                        </div>
                        <div class="mb-3">
                            <label for="generico" class="form-label">Genérico:</label>
                            <input type="text" id="generico" name="generico" class="form-control" value="${data.generico || ''}" required>
                        </div>
                        <div class="mb-3">
                            <label for="clasificacion" class="form-label">Clasificación:</label>
                            <input type="text" id="clasificacion" name="clasificacion" class="form-control" value="${data.clasificacion || ''}" required>
                        </div>
                        <div class="mb-3">
                            <label for="existencia" class="form-label">Existencia:</label>
                            <input type="text" id="existencia" name="existencia" class="form-control" value="${data.existencia || ''}" readonly>
                            <div class="form-text">La existencia se modifica con los botones + y - en la tabla</div>
                        </div>
                        <div class="mb-3">
                            <label for="costo" class="form-label">Costo ($):</label>
                            <input type="text" id="costo" name="costo_promedio" class="form-control" value="${data.costo_promedio || ''}" required>
                        </div>
                    `);
                    footer.html(`
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary" id="btnGuardarMaterial">Guardar</button>
                    `);
                    $('#materialId').val(data.id_material || '');
                }
                modal.modal('show');
            }

            // Load users for selected location
            function cargarUsuariosDelLugar(idLugar) {
                console.log('Cargando usuarios para lugar ID:', idLugar);
                if (!idLugar) {
                    $('#usuarioReporta').html('<option value="">-- Selecciona el usuario que reporta --</option>');
                    return;
                }

                $.ajax({
                    url: `/lugares/${idLugar}/usuarios`,
                    method: 'GET',
                    success: function(response) {
                        let options = '<option value="">-- Selecciona el usuario que reporta --</option>';
                        if (response.usuarios && Array.isArray(response.usuarios) && response.usuarios.length > 0) {
                            response.usuarios.forEach(function(usuario) {
                                options += `<option value="${usuario.id}" data-nombre="${usuario.name}" data-correo="${usuario.email}">
                                              ${usuario.name} (${usuario.email})
                                            </option>`;
                            });
                        } else {
                            options += '<option value="" disabled>No hay usuarios disponibles</option>';
                        }
                        $('#usuarioReporta').html(options);
                    },
                    error: function(xhr) {
                        console.error('Error al cargar usuarios:', xhr.status, xhr.responseText);
                        $('#usuarioReporta').html('<option value="" disabled>Error al cargar usuarios</option>');
                        alert('Error al cargar usuarios: ' + (xhr.responseJSON?.message || xhr.statusText));
                    }
                });
            }

            // Event for location change
            $('#id_lugar_falla').on('change', function() {
                let idLugar = $(this).val();
                console.log('Lugar seleccionado:', idLugar);
                cargarUsuariosDelLugar(idLugar);
            });

            // Event for user selection
            $('#usuarioReporta').on('change', function() {
                let selectedOption = $(this).find('option:selected');
                console.log('Usuario seleccionado:', selectedOption.val());
                $('#nombreUsuarioReporta').val(selectedOption.data('nombre') || '');
                $('#correoUsuarioReporta').val(selectedOption.data('correo') || '');
            });

            // Password validation for reviso_por
            $('#reviso_por').on('input', function() {
                let password = $(this).val();
                if (password.length > 0) {
                    $.post('/verificar-password', { password: password })
                        .done(function(response) {
                            if (response.success) {
                                passwordVerified = true;
                                $('#passwordError').hide();
                                $('#nombreUsuarioRevisa').val('{{ Auth::user()->nombre }}');
                                $('#correoUsuarioRevisa').val('{{ Auth::user()->email }}');
                                $('#btnVerPDF, #btnEnviarReporte, #btnGuardarPDF, #btnGuardarEnviar').prop('disabled', false);
                            } else {
                                passwordVerified = false;
                                $('#passwordError').text('Contraseña incorrecta').show();
                                $('#nombreUsuarioRevisa').val('');
                                $('#correoUsuarioRevisa').val('');
                                $('#btnVerPDF, #btnEnviarReporte, #btnGuardarPDF, #btnGuardarEnviar').prop('disabled', true);
                            }
                        })
                        .fail(function(xhr) {
                            passwordVerified = false;
                            $('#passwordError').text('Error al verificar la contraseña').show();
                            $('#nombreUsuarioRevisa').val('');
                            $('#correoUsuarioRevisa').val('');
                            $('#btnVerPDF, #btnEnviarReporte, #btnGuardarPDF, #btnGuardarEnviar').prop('disabled', true);
                        });
                } else {
                    passwordVerified = false;
                    $('#passwordError').hide();
                    $('#nombreUsuarioRevisa').val('');
                    $('#correoUsuarioRevisa').val('');
                    $('#btnVerPDF, #btnEnviarReporte, #btnGuardarPDF, #btnGuardarEnviar').prop('disabled', true);
                }
            });

            // Add material to selection table
            function agregarMaterial(materialInfo) {
                let existe = false;
                $("#selectedMaterialsTable tbody tr").each(function () {
                    let id = $(this).data('id');
                    if (id == materialInfo.id) {
                        let cantidadInput = $(this).find('input.cantidad');
                        cantidadInput.val(parseInt(cantidadInput.val()) + 1);
                        existe = true;
                    }
                });
                if (!existe) {
                    let fila = `<tr data-id="${materialInfo.id}">
                        <td>${materialInfo.descripcion}</td>
                        <td><input type="number" class="form-control cantidad" value="1" min="1" style="width:80px"></td>
                        <td>
                          <button type="button" class="btn btn-sm btn-success btnInc">+</button>
                          <button type="button" class="btn btn-sm btn-danger btnDec">-</button>
                          <button type="button" class="btn btn-sm btn-secondary btnRemove">X</button>
                        </td>
                      </tr>`;
                    $("#selectedMaterialsTable tbody").append(fila);
                }
                actualizarMaterialsData();
            }

            // Update materials data JSON
            function actualizarMaterialsData() {
                let materiales = [];
                $("#selectedMaterialsTable tbody tr").each(function () {
                    let id = $(this).data('id');
                    let descripcion = $(this).find('td:first').text();
                    let cantidad = $(this).find('input.cantidad').val();
                    materiales.push({ id: id, descripcion: descripcion, cantidad: cantidad });
                });
                $('#materialsData').val(JSON.stringify(materiales));
            }

            // Material table button events
            $("#selectedMaterialsTable").on("click", ".btnDec", function () {
                let input = $(this).closest('tr').find('input.cantidad');
                if (parseInt(input.val()) > 1) {
                    input.val(parseInt(input.val()) - 1);
                    actualizarMaterialsData();
                }
            });
            $("#selectedMaterialsTable").on("click", ".btnRemove", function () {
                $(this).closest('tr').remove();
                actualizarMaterialsData();
            });

            // Add material from search
            $("#btnAgregarMaterial").click(function () {
                let valor = $("#materialBuscar").val().trim();
                if (valor === "") {
                    alert("Por favor, ingrese el nombre o clave del material");
                    return;
                }
                let option = $('#materialesList option').filter(function () {
                    return $(this).val() === valor;
                }).first();
                if (option.length) {
                    let id = option.data('id');
                    let descripcion = option.val();
                    agregarMaterial({ id: id, descripcion: descripcion });
                    $("#materialBuscar").val("");
                } else {
                    alert("Material no encontrado en la lista");
                }
            });

            // Open failure report modal
            $('.btnDisminuir, #btnReporteFallas').click(function () {
                if ($(this).hasClass('btnDisminuir')) {
                    let fila = $(this).closest('tr');
                    let materialId = $(this).data('id');
                    let materialDescripcion = fila.find('td:nth-child(3)').text();
                    $("#selectedMaterialsTable tbody").html("");
                    agregarMaterial({ id: materialId, descripcion: materialDescripcion });
                } else {
                    $("#selectedMaterialsTable tbody").html("");
                    actualizarMaterialsData();
                }
                reporteId = null;
                $("#modalFalla").modal("show");
                passwordVerified = false;
                $('#btnVerPDF, #btnEnviarReporte, #btnGuardarPDF, #btnGuardarEnviar').prop('disabled', true);
                $('#reviso_por').val('');
                $('#passwordError').hide();
                $('#id_lugar_falla').val('').trigger('change');
                $('#nombreUsuarioRevisa').val('');
                $('#correoUsuarioRevisa').val('');
            });

            // Ver PDF button
            $("#btnVerPDF").click(function () {
                if (!passwordVerified) {
                    $('#modalConfirmarPassword').modal('show');
                    $('#passwordConfirm').val('').focus();
                    $('#errorPassword').addClass('d-none');
                    return;
                }
                if (!$('#id_lugar_falla').val()) {
                    alert('Por favor selecciona un lugar');
                    return;
                }
                if (!$('#usuarioReporta').val()) {
                    alert('Por favor selecciona el usuario que reporta');
                    return;
                }
                
                actualizarMaterialsData();
                let formData = $("#formFalla").serialize();
                
                $.post('/fallas', formData, function (response) {
                    reporteId = response.data.id_falla;
                    $('#modalConfirmarPassword').modal('hide');
                    $("#iframePDF").attr("src", "/fallas/pdf/" + reporteId);
                    $("#modalVerPDF").modal("show");
                    $('#btnVerPDF').prop('disabled', true)
                                   .removeClass('btn-info')
                                   .addClass('btn-success')
                                   .html('<i class="fas fa-check"></i> PDF Generado');
                    alert("Registro guardado exitosamente. El reporte no puede ser eliminado.");
                }).fail(function (xhr) {
                    $('#modalConfirmarPassword').modal('hide');
                    handleAjaxError(xhr);
                });
            });

            // Send report button
            $("#btnEnviarReporte").click(function () {
                if (!passwordVerified) {
                    alert('Por favor, verifica tu contraseña primero.');
                    return;
                }
                if (!reporteId) {
                    alert('Por favor, guarda el reporte primero.');
                    return;
                }
                const correoDestino = $('[name="correo_destino"]').val();
                if (!correoDestino) {
                    alert('Por favor, ingresa un correo destino.');
                    return;
                }
                
                $.post(`/fallas/enviar/${reporteId}`, { correo_destino: correoDestino })
                    .done(function () {
                        alert('Reporte enviado correctamente');
                    })
                    .fail(function (xhr) {
                        handleAjaxError(xhr);
                    });
            });

            // Download PDF button
            $("#btnGuardarPDF").click(function () {
                if (!passwordVerified) {
                    alert('Por favor, verifica tu contraseña primero.');
                    return;
                }
                if (!reporteId) {
                    alert('Por favor, guarda el reporte primero.');
                    return;
                }
                window.location.href = `/fallas/pdf/${reporteId}`;
            });

            // Confirm password and generate PDF
            $('#btnConfirmarPassword').click(function() {
                let password = $('#passwordConfirm').val();
                
                if (!password) {
                    alert('Por favor ingresa tu contraseña');
                    return;
                }

                $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Verificando...');

                $.post('/verificar-password', { password: password })
                    .done(function(response) {
                        if (response.success) {
                            passwordVerified = true;
                            $('#modalConfirmarPassword').modal('hide');
                            $('#btnVerPDF').trigger('click');
                            $('#btnEnviarReporte, #btnGuardarPDF, #btnGuardarEnviar').prop('disabled', false);
                        } else {
                            $('#errorPassword').removeClass('d-none');
                            $('#passwordConfirm').val('').focus();
                            $('#btnConfirmarPassword').prop('disabled', false).html('<i class="fas fa-lock"></i> Confirmar y Generar PDF');
                        }
                    })
                    .fail(function(xhr) {
                        $('#errorPassword').removeClass('d-none');
                        $('#passwordConfirm').val('').focus();
                        $('#btnConfirmarPassword').prop('disabled', false).html('<i class="fas fa-lock"></i> Confirmar y Generar PDF');
                        console.error('Error al confirmar contraseña:', xhr.responseText);
                    });
            });

            // Submit failure report form
            $('#formFalla').submit(function(e) {
                e.preventDefault();
                if (!passwordVerified || !$('#correoUsuarioRevisa').val()) {
                    alert('Por favor, verifica tu contraseña para completar los datos de revisión.');
                    return;
                }
                actualizarMaterialsData();
                const formData = $(this).serialize();
                
                if (reporteId) {
                    const correo = $('[name="correo_destino"]').val();
                    if (correo) {
                        $.post(`/fallas/enviar/${reporteId}`, { correo_destino: correo })
                            .done(() => alert("Correo enviado exitosamente"))
                            .fail(() => alert("Error al enviar correo"));
                    }
                    $('#modalFalla').modal('hide');
                    location.reload();
                    return;
                }
                
                $.post('/fallas', formData, function (response) {
                    alert(response.message);
                    const correo = $('[name="correo_destino"]').val();
                    if (correo) {
                        $.post(`/fallas/enviar/${response.data.id_falla}`, { correo_destino: correo })
                            .done(() => alert("Correo enviado"))
                            .fail(() => alert("Error al enviar correo"));
                    }
                    $('#modalFalla').modal('hide');
                    location.reload();
                }).fail(handleAjaxError);
            });

            // Material modal events
            $('.btnEditar').click(function () {
                let id = $(this).data('id');
                $.get(`/materiales/${id}/edit`, function (response) {
                    openMaterialModal('edit', response.data);
                });
            });

            $('.btnVer').click(function () {
                let id = $(this).data('id');
                $.get(`/materiales/${id}`, function (response) {
                    openMaterialModal('view', response.data);
                });
            });

            $('#btnNuevoMaterial').click(function () {
                openMaterialModal('create');
            });

            $('#formMaterial').submit(function (e) {
                e.preventDefault();
                const id = $('#materialId').val();
                const url = id ? `/materiales/${id}` : '/materiales';
                const method = id ? 'PUT' : 'POST';
                $.ajax({
                    url: url,
                    type: method,
                    data: $(this).serialize(),
                    success: function (response) {
                        alert(response.message);
                        $('#modalMaterial').modal('hide');
                        location.reload();
                    },
                    error: handleAjaxError
                });
            });

            // Delete material
            $('.btnEliminar').click(function () {
                const id = $(this).data('id');
                $('#btnConfirmarEliminar').data('id', id);
                $('#modalEliminar').modal('show');
            });

            $('#btnConfirmarEliminar').click(function () {
                const id = $(this).data('id');
                $.ajax({
                    url: `/materiales/${id}`,
                    type: 'DELETE', 'DELETE',
                    success: function (response) {
                        alert(response.message);
                        $('#modalEliminar').modal('hide');
                        location.reload();
                    },
                    error: handleAjaxError
                });
            });

            // Increase stock
            $('.btnAumentar').click(function () {
                const id = $(this).data('id');
                const fila = $(this).closest('tr');
                const nombre = fila.find('td:nth-child(3)').text();
                const existencia = fila.find('td:nth-child(6) span').text();
                $('#materialIdAumentar').val(id);
                $('#materialNombreAumentar').text(nombre);
                $('#existenciaActualAumentar').text(existencia);
                $('#modalAumentar').modal('show');
            });

            $('#formAumentar').submit(function (e) {
                e.preventDefault();
                const id = $('#materialIdAumentar').val();
                $.ajax({
                    url: `/materiales/${id}/aumentar`,
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function (response) {
                        alert(response.message);
                        $('#modalAumentar').modal('hide');
                        location.reload();
                    },
                    error: handleAjaxError
                });
            });

            // Cardex file upload
            $('#formCardex').submit(function (e) {
                e.preventDefault();
                const formData = new FormData(this);
                $('#progreso').show();
                $('.progress-bar').css('width', '0%');
                $.ajax({
                    url: '/materiales/import',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    xhr: function () {
                        const xhr = new window.XMLHttpRequest();
                        xhr.upload.addEventListener('progress', function (e) {
                            if (e.lengthComputable) {
                                const percent = (e.loaded / e.total) * 100;
                                $('.progress-bar').css('width', percent + '%');
                            }
                        }, false);
                        return xhr;
                    },
                    success: function (response) {
                        alert(response.message);
                        $('#modalCardex').modal('hide');
                        location.reload();
                    },
                    error: handleAjaxError,
                    complete: function () {
                        $('#progreso').hide();
                        $('.progress-bar').css('width', '0%');
                    }
                });
            });

            // Reset modals when closed
            $('#modalFalla').on('hidden.bs.modal', function() {
                $('#formFalla')[0].reset();
                $("#selectedMaterialsTable tbody").html("");
                $('#usuarioReporta').html('<option value="">-- Selecciona el usuario que reporta --</option>');
                $('#btnVerPDF').prop('disabled', false)
                               .removeClass('btn-success')
                               .addClass('btn-info')
                               .html('Ver PDF');
                $('#btnEnviarReporte, #btnGuardarPDF, #btnGuardarEnviar').prop('disabled', true);
                passwordVerified = false;
                $('#reviso_por').val('');
                $('#passwordError').hide();
                $('#nombreUsuarioRevisa').val('');
                $('#correoUsuarioRevisa').val('');
                reporteId = null;
            });

            $('#modalCardex').on('hidden.bs.modal', function() {
                $('#formCardex')[0].reset();
                $('#progreso').hide();
                $('.progress-bar').css('width', '0%');
            });
        });
    </script>
</body>
</html>