<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('img/logo.png') }}">
    <title>Lista de Materiales - FontTrack</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        :root {
            --primary-orange: #F6B88F;
            --secondary-orange: #E38B5B;
            --dark-brown: #634D3B;
            --light-cream: #FCE8D5;
            --accent-blue: #88C0D0;
            --accent-yellow: #E5A34D;
            --accent-red: #D9534F;
            --shadow-light: rgba(0, 0, 0, 0.1);
            --shadow-medium: rgba(0, 0, 0, 0.15);
            --shadow-heavy: rgba(0, 0, 0, 0.3);
        }

        * {
            box-sizing: border-box;
        }

        /* Animaciones globales */
        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-8px);
            }
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInScale {
            from {
                opacity: 0;
                transform: scale(0.95);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.6;
            }
        }

        @keyframes shimmer {
            0% {
                left: -100%;
            }

            100% {
                left: 100%;
            }
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Animación para la campana */
        @keyframes bellRing {

            0%,
            100% {
                transform: rotate(0deg);
            }

            25% {
                transform: rotate(15deg);
            }

            75% {
                transform: rotate(-15deg);
            }
        }

        /* Body & Background */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, var(--light-cream) 0%, #f8f1e8 100%);
            color: var(--dark-brown);
            margin: 0;
            padding: 0;
            min-height: 100vh;
            position: relative;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image:
                radial-gradient(circle at 20% 50%, rgba(246, 184, 143, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(227, 139, 91, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 40% 80%, rgba(136, 192, 208, 0.05) 0%, transparent 50%);
            pointer-events: none;
            z-index: -1;
        }

        /* Navbar mejorada */
        .navbar {
            background: linear-gradient(135deg, var(--primary-orange) 0%, var(--secondary-orange) 100%);
            box-shadow: 0 8px 32px var(--shadow-medium);
            padding: 15px 25px;
            border-bottom: none;
            position: sticky;
            top: 0;
            z-index: 1020;
            backdrop-filter: blur(10px);
        }

        .navbar::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.05) 100%);
            pointer-events: none;
        }

        .navbar .logo {
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.2));
        }

        .navbar .logo:hover {
            transform: scale(1.08) rotate(2deg);
            filter: drop-shadow(0 8px 16px rgba(0, 0, 0, 0.3)) brightness(1.1);
        }

        .navbar .nav-link {
            color: white !important;
            font-size: 1.1em;
            font-weight: 600;
            padding: 12px 20px !important;
            margin: 0 5px;
            border-radius: 12px;
            transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            position: relative;
            overflow: hidden;
        }

        .navbar .nav-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .navbar .nav-link:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateY(-2px) scale(1.05);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }

        .navbar .nav-link:hover::before {
            left: 100%;
        }

        /* ✅ NUEVO: Contenedor para campana y usuario */
        .navbar-right-controls {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        /* Campana de notificaciones */
        .notification-bell {
            position: relative;
            display: inline-block;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .notification-bell:hover {
            transform: scale(1.1);
        }

        .notification-bell.has-notifications {
            animation: bellRing 2s infinite;
        }

        .notification-bell .bell-icon {
            font-size: 1.8rem;
            color: white;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.3));
        }

        .notification-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: linear-gradient(135deg, #ff4757, #ff3742);
            color: white;
            border-radius: 50%;
            width: 25px;
            height: 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: bold;
            box-shadow: 0 2px 8px rgba(255, 71, 87, 0.4);
            animation: pulse 2s infinite;
        }

        .notification-badge.hidden {
            display: none;
        }

        /* User profile */
        .user-profile {
            display: flex;
            align-items: center;
            position: relative;
            cursor: pointer;
            padding: 8px 15px;
            border-radius: 15px;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
        }

        .user-profile:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }

        .user-profile img {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            margin-right: 12px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            transition: all 0.3s ease;
            object-fit: cover;
        }

        .user-profile:hover img {
            border-color: white;
            transform: scale(1.1);
        }

        .user-name {
            font-weight: 600;
            color: white;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
        }

        .user-dropdown {
            position: absolute;
            top: 120%;
            right: 0;
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 60px var(--shadow-heavy);
            padding: 15px 0;
            min-width: 200px;
            display: none;
            z-index: 1000;
            border: 1px solid rgba(227, 139, 91, 0.2);
        }

        .user-dropdown.show {
            display: block;
            animation: fadeInScale 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }

        .user-dropdown a {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: var(--dark-brown);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .user-dropdown a i {
            margin-right: 10px;
            font-size: 1.1rem;
        }

        .user-dropdown a:hover {
            background: linear-gradient(135deg, var(--primary-orange), var(--secondary-orange));
            color: white;
            transform: translateX(5px);
        }

        /* Container mejorado */
        .container {
            background: rgba(255, 255, 255, 0.95);
            margin: 30px auto;
            padding: 40px;
            max-width: 1400px;
            border-radius: 25px;
            box-shadow: 0 20px 60px var(--shadow-light);
            border: 1px solid rgba(246, 184, 143, 0.2);
            backdrop-filter: blur(10px);
            animation: slideInUp 0.6s ease-out;
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
            background: radial-gradient(circle, rgba(246, 184, 143, 0.05) 0%, transparent 70%);
            animation: float 6s ease-in-out infinite;
            pointer-events: none;
        }

        h2 {
            font-size: 2.5em;
            font-weight: 700;
            background: linear-gradient(135deg, var(--secondary-orange), var(--primary-orange));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 2rem;
            text-align: center;
            position: relative;
            z-index: 1;
        }

        h2::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: linear-gradient(135deg, var(--secondary-orange), var(--primary-orange));
            border-radius: 2px;
        }

        /* Modal de notificaciones */
        .notification-modal {
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(10px);
        }

        .notification-modal .modal-content {
            border-radius: 20px;
            border: none;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            background: linear-gradient(135deg, #fff 0%, #FCE8D5 100%);
        }

        .notification-item {
            border: 1px solid rgba(227, 139, 91, 0.2);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 15px;
            background: rgba(255, 255, 255, 0.8);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .notification-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            width: 5px;
            height: 100%;
            background: linear-gradient(135deg, var(--primary-orange), var(--secondary-orange));
        }

        .notification-item:hover {
            background: rgba(246, 184, 143, 0.1);
            transform: translateX(5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .notification-item.selected {
            background: linear-gradient(135deg, rgba(246, 184, 143, 0.2), rgba(227, 139, 91, 0.1));
            border-color: var(--secondary-orange);
        }

        .notification-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .notification-title {
            font-weight: bold;
            color: var(--dark-brown);
            font-size: 1.1rem;
        }

        .notification-time {
            font-size: 0.85rem;
            color: #666;
            font-style: italic;
        }

        .notification-details {
            color: #555;
            font-size: 0.95rem;
            line-height: 1.4;
        }

        .notification-actions {
            margin-top: 15px;
            display: flex;
            gap: 10px;
        }

        .btn-approve {
            background: linear-gradient(135deg, #28a745, #20c997);
            border: none;
            color: white;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .btn-approve:hover {
            background: linear-gradient(135deg, #20c997, #17a2b8);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
        }

        .btn-reject {
            background: linear-gradient(135deg, #dc3545, #c82333);
            border: none;
            color: white;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .btn-reject:hover {
            background: linear-gradient(135deg, #c82333, #bd2130);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(220, 53, 69, 0.3);
        }

        .btn-view {
            background: linear-gradient(135deg, #007bff, #0056b3);
            border: none;
            color: white;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .btn-view:hover {
            background: linear-gradient(135deg, #0056b3, #004085);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 123, 255, 0.3);
        }

        /* Loading states */
        .btn.loading {
            position: relative;
            pointer-events: none;
        }

        .btn.loading::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 16px;
            height: 16px;
            margin: -8px 0 0 -8px;
            border: 2px solid transparent;
            border-top-color: currentColor;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        .btn.loading span {
            opacity: 0;
        }

        /* Password validation states */
        .password-validation-success {
            border-color: #28a745;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
        }

        .password-validation-error {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }

        /* User authorization info */
        #datosUsuarioAutoriza {
            transition: all 0.4s ease;
            transform-origin: top;
        }

        #datosUsuarioAutoriza .alert-success {
            background: linear-gradient(135deg, rgba(40, 167, 69, 0.1), rgba(32, 201, 151, 0.1));
            border: 1px solid rgba(40, 167, 69, 0.3);
            border-radius: 10px;
        }

        /* Success message animation */
        .success-message {
            animation: successPulse 0.6s ease-in-out;
        }

        @keyframes successPulse {
            0% {
                transform: translate(-50%, -50%) scale(0.8);
                opacity: 0;
            }

            50% {
                transform: translate(-50%, -50%) scale(1.05);
                opacity: 1;
            }

            100% {
                transform: translate(-50%, -50%) scale(1);
                opacity: 1;
            }
        }

        /* No notifications state */
        .no-notifications {
            text-align: center;
            padding: 40px 20px;
            color: #666;
        }

        .no-notifications i {
            font-size: 3rem;
            margin-bottom: 15px;
            opacity: 0.5;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .navbar-right-controls {
                gap: 10px;
            }

            .notification-bell .bell-icon {
                font-size: 1.5rem;
            }

            .notification-badge {
                width: 20px;
                height: 20px;
                font-size: 0.65rem;
            }

            .notification-item {
                padding: 15px;
            }

            .notification-actions {
                flex-direction: column;
            }

            .notification-actions .btn {
                width: 100%;
                margin-bottom: 5px;
            }

            .action-buttons {
                flex-direction: column;
                gap: 3px;
            }

            .action-buttons .btn {
                width: 100%;
                height: 35px;
            }
        }

        /* Campo de correo deshabilitado */
        .campo-correo-disabled {
            background-color: #f8f9fa;
            cursor: not-allowed;
            opacity: 0.6;
        }

        /* Animación para habilitar campo */
        .campo-correo-enabled {
            background-color: white;
            transition: all 0.3s ease;
            animation: fieldHighlight 0.5s ease-in-out;
        }

        @keyframes fieldHighlight {
            0% {
                background-color: #ffffcc;
            }

            100% {
                background-color: white;
            }
        }

        /* ✅ INDICADOR DE FILTRO ACTIVO */
        .filter-indicator {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            box-shadow: 0 3px 10px rgba(40, 167, 69, 0.3);
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <img src="{{ asset('img/FontTrack.png') }}" alt="logo" height="70px" width="100px" class="logo"
                onclick="window.location.href='{{ route('users') }}'">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('users') }}">
                            <i class="bi bi-house me-2"></i>Inicio
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('lugares.index') }}">
                            <i class="bi bi-geo-alt me-2"></i>Lugares
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('reportes.index') }}">
                            <i class="bi bi-file-text me-2"></i>Reportes
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('vehiculos.index') }}">
                            <i class="bi bi-truck me-2"></i>Vehículos
                        </a>
                    </li>
                </ul>

                <div class="navbar-right-controls">
                    @if(Auth::user()->id_lugar)
                    <!-- Campana de notificaciones -->
                    <div class="notification-bell" id="notificationBell" title="Notificaciones pendientes">
                        <i class="fas fa-bell bell-icon"></i>
                        <span class="notification-badge hidden" id="notificationBadge">0</span>
                    </div>
                    @endif

                    <!-- Perfil de usuario -->
                    <div class="user-profile" id="userProfileDropdown">
                        <img src="{{ Auth::user()->foto_usuario_url ?? asset('img/usuario_default.png') }}"
                            alt="Foto de perfil">
                        <span class="user-name">{{ Auth::user()->nombre }}</span>
                        <div class="user-dropdown" id="userDropdownMenu">
                            <a href="#"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="bi bi-box-arrow-right"></i> Cerrar sesión
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Modal de Notificaciones -->
    @if(Auth::user()->id_lugar)
    <div class="modal fade notification-modal" id="modalNotificaciones" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-bell"></i>
                        Notificaciones Pendientes
                        <span class="badge bg-warning" id="modalNotificationCount">0</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="max-height: 600px; overflow-y: auto;">
                    <div id="notificationsContainer">
                        <div class="text-center">
                            <i class="fas fa-spinner fa-spin fa-2x"></i>
                            <p class="mt-2">Cargando notificaciones...</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" id="btnRefreshNotifications">
                        <i class="fas fa-sync-alt"></i> Actualizar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para ver detalles de notificación -->
    <div class="modal fade" id="modalDetalleNotificacion" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detalles de la Notificación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="detalleNotificacionBody">
                    <!-- Se carga via AJAX -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para aprobar notificación -->
    <div class="modal fade" id="modalAprobarNotificacion" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Aprobar Notificación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>¡Atención!</strong> Al aprobar esta notificación se creará automáticamente un reporte de
                        falla y se descontará el stock de los materiales.
                    </div>
                    <div class="mb-3">
                        <label for="passwordAprobar" class="form-label">Confirma tu contraseña:</label>
                        <div class="input-group">
                            <input type="password" id="passwordAprobar" class="form-control"
                                placeholder="Ingresa tu contraseña para confirmar" required>
                            <button type="button" class="btn btn-outline-secondary" id="btnTogglePasswordAprobar">
                                <i class="bi bi-eye" id="iconTogglePasswordAprobar"></i>
                            </button>
                        </div>
                        <small id="passwordValidating" class="text-info" style="display: none;">
                            <i class="fas fa-spinner fa-spin"></i> Validando contraseña...
                        </small>
                        <small id="passwordValidSuccess" class="text-success" style="display: none;">
                            <i class="fas fa-check-circle"></i> Contraseña válida
                        </small>
                    </div>

                    <div id="datosUsuarioAutoriza" class="mb-3" style="display: none;">
                        <div class="alert alert-success">
                            <h6><i class="fas fa-user-check"></i> Usuario que autoriza:</h6>
                            <p class="mb-1"><strong>Nombre:</strong> <span id="nombreUsuarioAutoriza"></span></p>
                            <p class="mb-0"><strong>Correo:</strong> <span id="correoUsuarioAutoriza"></span></p>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="comentariosAprobar" class="form-label">
                            <i class="fas fa-envelope"></i> Correo para envío del reporte:
                        </label>
                        <input type="email" id="comentariosAprobar" class="form-control"
                            placeholder="correo@ejemplo.com" required>
                        <small class="form-text text-muted">Se enviará el PDF del reporte a este correo
                            electrónico</small>
                    </div>
                    <div id="errorAprobar" class="alert alert-danger d-none">
                        <i class="fas fa-times-circle"></i>
                        <span id="errorAprobarMessage">Error al procesar</span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-success" id="btnConfirmarAprobar" disabled>
                        <i class="fas fa-check"></i> Aprobar y Enviar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para rechazar notificación -->
    <div class="modal fade" id="modalRechazarNotificacion" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Rechazar Notificación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Nota:</strong> Al rechazar esta notificación, se mantendrá en el registro para auditoría
                        pero no se procesará.
                    </div>
                    <div class="mb-3">
                        <label for="comentariosRechazar" class="form-label">Motivo del rechazo (requerido):</label>
                        <textarea id="comentariosRechazar" class="form-control" rows="4"
                            placeholder="Explica por qué se rechaza esta notificación" required></textarea>
                    </div>
                    <div id="errorRechazar" class="alert alert-danger d-none">
                        <i class="fas fa-times-circle"></i>
                        <span id="errorRechazarMessage">Error al procesar</span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="btnConfirmarRechazar">
                        <i class="fas fa-times"></i> Rechazar
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

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
                            <label for="id_lugar" class="form-label">Lugar:</label>
                            <input type="text" class="form-control" value="{{ Auth::user()->lugar->nombre ?? 'Sin lugar asignado' }}" readonly>
                            <input type="hidden" name="id_lugar" value="{{ Auth::user()->id_lugar }}">
                        </div>
                        <div class="mb-3">
                            <label for="archivo_cardex" class="form-label">Seleccionar Archivo Excel:</label>
                            <input type="file" id="archivo_cardex" name="archivo_cardex" class="form-control"
                                accept=".xlsx,.xls" required>
                            <div class="form-text">El archivo debe contener: Clave Material, Descripción, Genérico,
                                Clasificación, Existencia, Costo Promedio.</div>
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

    @if(Auth::user()->id_lugar || Auth::user()->tipo_usuario == 1)
    <!-- ✅ CONTENIDO PRINCIPAL CON FILTRO CORRECTO -->
    <div class="container mt-4">
        <h2 class="mb-3">
            Lista de Materiales
            @if(Auth::user()->tipo_usuario == 1)
                - Todos los lugares
            @else
                - {{ Auth::user()->lugar->nombre ?? 'Sin lugar asignado' }}
            @endif
        </h2>
        
        <!-- ✅ INDICADOR VISUAL DEL FILTRO ACTIVO -->
        @if(Auth::user()->tipo_usuario != 1)
        <div class="text-center mb-4">
            <span class="filter-indicator">
                <i class="fas fa-filter"></i>
                🔒 Filtro activo: Solo materiales de {{ Auth::user()->lugar->nombre ?? 'Sin lugar' }}
                ({{ $materiales->total() }} {{ $materiales->total() == 1 ? 'material' : 'materiales' }})
            </span>
        </div>
        @endif
        
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="d-flex gap-2">
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalCardex">🔑 Subir
                    Kardex</button>
                <button class="btn btn-warning" id="btnReporteFallas">📋 Reporte de Fallas</button>
            </div>
            <a href="{{ route('materials.export') }}" class="btn btn-outline-primary">📤 Exportar Excel (Existencia)</a>
            <form class="d-flex" action="{{ route('materials') }}" method="GET">
                <input class="form-control me-2" type="search" name="query" placeholder="Buscar material"
                    aria-label="Buscar" value="{{ request('query') }}">
                <button class="btn btn-outline-success me-2" type="submit"><i class="bi bi-search"></i></button>
            </form>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalMaterial"
                id="btnNuevoMaterial">Registrar Material</button>
        </div>
        
        <!-- ✅ TABLA FILTRADA - SIN FILTROS ADICIONALES -->
        <div class="table-responsive">
            <table class="table mt-3">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Clave</th>
                        <th>Descripción</th>
                        <th>Genérico</th>
                        <th>Clasificación</th>
                        <th>Existencia</th>
                        <th>Costo ($)</th>
                        @if(Auth::user()->tipo_usuario == 1)
                        <th>Lugar</th>
                        @endif
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- ✅ TABLA LIMPIA: Los materiales YA VIENEN FILTRADOS del controlador --}}
                    @forelse($materiales as $material)
                        <tr data-id="{{ $material->id_material }}">
                            <td>{{ $material->id_material }}</td>
                            <td>{{ $material->clave_material }}</td>
                            <td>{{ $material->descripcion }}</td>
                            <td>{{ $material->generico }}</td>
                            <td>{{ $material->clasificacion }}</td>
                            <td>
                                <div class="d-flex align-items-center justify-content-center gap-1">
                                    <button class="btn btn-sm btn-success btnAumentar"
                                        data-id="{{ $material->id_material }}" title="Aumentar existencia"><i
                                            class="bi bi-plus"></i></button>
                                    <span class="mx-2">{{ $material->existencia }}</span>
                                    <button class="btn btn-sm btn-danger btnDisminuir"
                                        data-id="{{ $material->id_material }}" title="Reportar falla"><i
                                            class="bi bi-dash"></i></button>
                                </div>
                            </td>
                            <td>${{ number_format($material->costo_promedio, 2) }}</td>
                            @if(Auth::user()->tipo_usuario == 1)
                            <td>
                                <span class="badge bg-info">
                                    {{ $material->lugar->nombre ?? 'Sin lugar' }}
                                </span>
                            </td>
                            @endif
                            <td class="d-flex flex-column flex-md-row action-buttons">
                                <button class="btn btn-info btnVer" data-id="{{ $material->id_material }}"
                                    data-bs-toggle="modal" data-bs-target="#modalMaterial"><i
                                        class="bi bi-eye"></i></button>
                                <button class="btn btn-warning btnEditar" data-id="{{ $material->id_material }}"
                                    data-bs-toggle="modal" data-bs-target="#modalMaterial"><i
                                        class="bi bi-pencil"></i></button>
                                <button class="btn btn-danger btn-sm btnEliminar" data-id="{{ $material->id_material }}"><i
                                        class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ Auth::user()->tipo_usuario == 1 ? '9' : '8' }}" class="text-center">
                                <div class="py-4">
                                    <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">No hay materiales registrados</h5>
                                    @if(Auth::user()->tipo_usuario == 1)
                                        <p class="text-muted">No hay materiales registrados en el sistema.</p>
                                    @else
                                        <p class="text-muted">
                                            No hay materiales registrados para tu lugar asignado: 
                                            <strong>{{ Auth::user()->lugar->nombre ?? 'Sin lugar asignado' }}</strong>
                                        </p>
                                        <small class="text-muted">
                                            Puedes registrar nuevos materiales usando el botón "Registrar Material" 
                                            o importar desde Excel con "Subir Kardex"
                                        </small>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3 d-flex justify-content-center">
            {{ $materiales->appends(['query' => request('query')])->links('pagination::bootstrap-5') }}
        </div>
    </div>
    @else
    <!-- Mensaje para usuarios sin lugar asignado -->
    <div class="container mt-4">
        <div class="alert alert-warning text-center">
            <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
            <h4>Acceso Restringido</h4>
            <p>No tienes un lugar asignado para acceder a esta sección. Contacta al administrador para obtener acceso.</p>
        </div>
    </div>
    @endif

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
                        <label for="cantidadAumentar">Cantidad a aumentar:</label>
                        <input type="number" id="cantidadAumentar" name="cantidad" class="form-control" min="1"
                            required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-success">Aumentar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal de Reporte de Fallas -->
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
                            <label for="id_lugar_falla" class="form-label">Lugar:</label>
                            <input type="text" class="form-control" value="{{ Auth::user()->lugar->nombre ?? 'Sin lugar asignado' }}" readonly>
                            <input type="hidden" name="id_lugar" value="{{ Auth::user()->id_lugar }}">
                        </div>

                        <div class="mb-3">
                            <label for="vehiculo_eco" class="form-label">Selecciona Vehículo (ECO):</label>
                            <select id="vehiculo_eco" name="vehiculo_eco" class="form-select">
                                <option value="">-- Cargando vehículos... --</option>
                            </select>
                            <small class="text-muted">Los datos del vehículo se llenarán automáticamente</small>
                        </div>

                        <div class="row mb-2">
                            <div class="col-md-4">
                                <label>No. ECO</label>
                                <input type="text" name="eco" id="campo_eco" class="form-control" readonly>
                            </div>
                            <div class="col-md-4">
                                <label>Placas</label>
                                <input type="text" name="placas" id="campo_placas" class="form-control" readonly>
                            </div>
                            <div class="col-md-4">
                                <label>Marca</label>
                                <input type="text" name="marca" id="campo_marca" class="form-control" readonly>
                            </div>
                            <div class="col-md-4 mt-2">
                                <label>Año</label>
                                <input type="text" name="anio" id="campo_anio" class="form-control" readonly>
                            </div>
                            <div class="col-md-4 mt-2">
                                <label>KM</label>
                                <input type="text" name="km" id="campo_km" class="form-control" readonly>
                            </div>
                            <div class="col-md-4 mt-2">
                                <label>Fecha</label>
                                <input type="date" name="fecha" id="campo_fecha" class="form-control">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label>Nombre del Conductor</label>
                            <input type="text" name="nombre_conductor" id="campo_conductor" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label>Descripción Servicio / Fallo</label>
                            <textarea name="descripcion" rows="3" class="form-control"></textarea>
                        </div>
                        <div class="mb-3">
                            <label>Observaciones Técnicas del Trabajo Realizado</label>
                            <textarea name="observaciones" rows="3" class="form-control"></textarea>
                        </div>
                        <h6 class="mt-3">Materiales a utilizar</h6>
                        <div class="mb-3">
                            <label for="materialBuscar">Buscar Material</label>
                            <input type="text" id="materialBuscar" class="form-control" list="materialesList"
                                placeholder="Ingrese clave o descripción">
                            <!-- ✅ DATALIST FILTRADO: Solo materiales YA FILTRADOS por el controlador -->
                            <datalist id="materialesList">
                                @foreach($materiales as $material)
                                    <option data-id="{{ $material->id_material }}"
                                        value="{{ $material->clave_material }} - {{ $material->descripcion }}">
                                    </option>
                                @endforeach
                            </datalist>
                            <button type="button" id="btnAgregarMaterial" class="btn btn-secondary mt-2">Agregar
                                Material</button>
                            
                            {{-- ✅ INFO SOLO PARA USUARIOS NO ADMIN --}}
                            @if(Auth::user()->tipo_usuario != 1)
                            <small class="text-muted d-block mt-1">
                                <i class="fas fa-info-circle"></i> 
                                Materiales disponibles: {{ $materiales->total() }} 
                                (solo de tu lugar: {{ Auth::user()->lugar->nombre ?? 'Sin lugar' }})
                            </small>
                            @endif
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
                        <div class="row mb-2">
                            <div class="col-md-6">
                                <label>Nombre y firma de quien reporta</label>
                                <select name="usuario_reporta_id" id="usuarioReporta" class="form-select" required>
                                    <option value="">-- Selecciona el usuario que reporta --</option>
                                </select>
                                <input type="hidden" name="nombre_usuario_reporta" id="nombreUsuarioReporta">
                                <input type="hidden" name="correo_usuario_reporta" id="correoUsuarioReporta">
                            </div>
                            <div class="col-md-6">
                                <label>Nombre y firma de quien revisó</label>
                                <select name="usuario_revisa_id" id="usuarioRevisa" class="form-select" required>
                                    <option value="">-- Selecciona el usuario que revisa (ADMIN) --</option>
                                </select>
                                <input type="hidden" name="nombre_usuario_revisa" id="nombreUsuarioRevisa">
                                <input type="hidden" name="correo_usuario_revisa" id="correoUsuarioRevisa">

                                <label class="mt-2">Contraseña del usuario que revisa</label>
                                <div class="input-group">
                                    <input type="password" name="reviso_por" id="reviso_por" class="form-control"
                                        placeholder="Contraseña del usuario seleccionado" required>
                                    <button type="button" class="btn btn-outline-secondary" id="btnTogglePassword">
                                        <i class="bi bi-eye" id="iconTogglePassword"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-primary"
                                        id="btnValidarPassword">Validar</button>
                                </div>
                                <small id="passwordError" class="text-danger" style="display: none;">Contraseña
                                    incorrecta</small>
                                <small id="passwordSuccess" class="text-success" style="display: none;">Contraseña
                                    validada correctamente</small>
                            </div>
                        </div>
                        <div class="mb-2">
                            <label>Correo para enviar reporte</label>
                            <input type="email" name="correo_destino" id="correoDestino"
                                class="form-control" placeholder="correo@ejemplo.com" disabled>
                            <small class="text-muted">Este campo se habilitará al presionar "Guardar y enviar
                                PDF"</small>
                        </div>
                        <input type="hidden" name="materials" id="materialsData">
                        <input type="hidden" name="enviar_correo" id="enviarCorreo" value="false">
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="btnGuardarPDF" class="btn btn-primary" disabled>
                            <i class="fas fa-save"></i> Guardar PDF
                        </button>
                        <button type="button" id="btnGuardarEnviarPDF" class="btn btn-success" disabled>
                            <i class="fas fa-paper-plane"></i> Guardar y enviar PDF
                        </button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </form>
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

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            let currentNotificationId = null;
            let notificationsRefreshInterval = null;
            let passwordValidationTimeout = null;
            let passwordVerified = false;

            @if(Auth::user()->id_lugar)
            initNotifications();
            
            // Cargar vehículos del lugar del usuario al abrir el modal
            $('#modalFalla').on('show.bs.modal', function() {
                const idLugar = {{ Auth::user()->id_lugar }};
                cargarVehiculosDelLugar(idLugar);
                cargarUsuariosDelLugar(idLugar);
                cargarUsuariosAdmin();
            });
            @endif

            function initNotifications() {
                loadNotificationCounter();
                startNotificationRefresh();
            }

            function startNotificationRefresh() {
                notificationsRefreshInterval = setInterval(loadNotificationCounter, 30000);
            }

            function stopNotificationRefresh() {
                if (notificationsRefreshInterval) {
                    clearInterval(notificationsRefreshInterval);
                }
            }

            function loadNotificationCounter() {
                $.get('/notificaciones/contador')
                    .done(function (response) {
                        if (response.success) {
                            updateNotificationBadge(response.count);
                        }
                    })
                    .fail(function (xhr) {
                        console.error('Error al cargar contador de notificaciones:', xhr);
                    });
            }

            function updateNotificationBadge(count) {
                const badge = $('#notificationBadge');
                const bell = $('#notificationBell');

                if (count > 0) {
                    badge.text(count).removeClass('hidden');
                    bell.addClass('has-notifications');
                } else {
                    badge.addClass('hidden');
                    bell.removeClass('has-notifications');
                }
            }

            function loadNotifications() {
                $('#notificationsContainer').html(`
                    <div class="text-center">
                        <i class="fas fa-spinner fa-spin fa-2x"></i>
                        <p class="mt-2">Cargando notificaciones...</p>
                    </div>
                `);

                $.get('/notificaciones/pendientes')
                    .done(function (response) {
                        if (response.success) {
                            renderNotifications(response.notificaciones, response.total);
                        }
                    })
                    .fail(function (xhr) {
                        $('#notificationsContainer').html(`
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle"></i>
                                Error al cargar notificaciones: ${xhr.responseJSON?.message || 'Error desconocido'}
                            </div>
                        `);
                    });
            }

            function renderNotifications(notificaciones, total) {
                $('#modalNotificationCount').text(total);

                if (notificaciones.length === 0) {
                    $('#notificationsContainer').html(`
                        <div class="no-notifications">
                            <i class="fas fa-bell-slash"></i>
                            <h5>No hay notificaciones pendientes</h5>
                            <p class="text-muted">Todas las notificaciones han sido procesadas.</p>
                        </div>
                    `);
                    return;
                }

                let html = '';
                notificaciones.forEach(function (notif) {
                    html += `
                        <div class="notification-item" data-id="${notif.id}">
                            <div class="notification-header">
                                <span class="notification-title">${notif.resumen}</span>
                                <span class="notification-time">${notif.tiempo_transcurrido}</span>
                            </div>
                            <div class="notification-details">
                                <strong>Usuario:</strong> ${notif.usuario_reporta}<br>
                                <strong>Lugar:</strong> ${notif.lugar}<br>
                                <strong>Vehículo:</strong> ${notif.vehiculo || 'No especificado'}<br>
                                <strong>Materiales:</strong> ${notif.materiales_count} items<br>
                                <strong>Fecha:</strong> ${notif.fecha_creacion}
                            </div>
                            <div class="notification-actions">
                                <button class="btn btn-view btn-sm" onclick="verDetalleNotificacion(${notif.id})">
                                    <i class="fas fa-eye"></i> Ver Detalles
                                </button>
                                <button class="btn btn-approve btn-sm" onclick="aprobarNotificacion(${notif.id})">
                                    <i class="fas fa-check"></i> Aprobar
                                </button>
                                <button class="btn btn-reject btn-sm" onclick="rechazarNotificacion(${notif.id})">
                                    <i class="fas fa-times"></i> Rechazar
                                </button>
                            </div>
                        </div>
                    `;
                });

                $('#notificationsContainer').html(html);
            }

            function cargarVehiculosDelLugar(idLugar) {
                $('#vehiculo_eco').html('<option value="">-- Cargando vehículos... --</option>');
                
                if (idLugar) {
                    $.ajax({
                        url: `/materials/vehiculos-lugar/${idLugar}`,
                        method: 'GET',
                        success: function (response) {
                            let options = '<option value="">-- Selecciona un vehículo --</option>';
                            
                            if (response.vehiculos && Array.isArray(response.vehiculos) && response.vehiculos.length > 0) {
                                response.vehiculos.forEach(function (vehiculo) {
                                    options += `<option value="${vehiculo.eco}" 
                                                  data-placas="${vehiculo.placas || ''}"
                                                  data-marca="${vehiculo.marca || ''}"
                                                  data-anio="${vehiculo.anio || ''}"
                                                  data-km="${vehiculo.kilometraje || ''}">
                                                  ${vehiculo.eco} - ${vehiculo.marca || 'Sin marca'} (${vehiculo.placas || 'Sin placas'})
                                                </option>`;
                                });
                            } else {
                                options += '<option value="" disabled>No hay vehículos disponibles</option>';
                            }
                            $('#vehiculo_eco').html(options);
                        },
                        error: function (xhr) {
                            $('#vehiculo_eco').html('<option value="" disabled>Error al cargar vehículos</option>');
                        }
                    });
                }
            }

            $('#vehiculo_eco').on('change', function () {
                let selectedOption = $(this).find('option:selected');
                let eco = selectedOption.val();
                
                if (eco) {
                    $('#campo_eco').val(eco);
                    $('#campo_placas').val(selectedOption.data('placas') || '');
                    $('#campo_marca').val(selectedOption.data('marca') || '');
                    $('#campo_anio').val(selectedOption.data('anio') || '');
                    $('#campo_km').val(selectedOption.data('km') || '');
                } else {
                    limpiarCamposVehiculo();
                }
            });

            function limpiarCamposVehiculo() {
                $('#campo_eco').val('');
                $('#campo_placas').val('');
                $('#campo_marca').val('');
                $('#campo_anio').val('');
                $('#campo_km').val('');
                $('#campo_conductor').val('');
            }

            function cargarUsuariosDelLugar(idLugar) {
                if (!idLugar) {
                    $('#usuarioReporta').html('<option value="">-- Selecciona el usuario que reporta --</option>');
                    return;
                }

                $.ajax({
                    url: `/lugares/${idLugar}/usuarios`,
                    method: 'GET',
                    success: function (response) {
                        let options = '<option value="">-- Selecciona el usuario que reporta --</option>';
                        if (response.usuarios && Array.isArray(response.usuarios) && response.usuarios.length > 0) {
                            response.usuarios.forEach(function (usuario) {
                                options += `<option value="${usuario.id}" data-nombre="${usuario.name}" data-correo="${usuario.email}">
                                              ${usuario.name} (${usuario.email})
                                            </option>`;
                            });
                        } else {
                            options += '<option value="" disabled>No hay usuarios disponibles</option>';
                        }
                        $('#usuarioReporta').html(options);
                    },
                    error: function (xhr) {
                        $('#usuarioReporta').html('<option value="" disabled>Error al cargar usuarios</option>');
                    }
                });
            }

            function cargarUsuariosAdmin() {
                $.ajax({
                    url: '/usuarios/admin',
                    method: 'GET',
                    success: function (response) {
                        let options = '<option value="">-- Selecciona el usuario que revisa (ADMIN) --</option>';
                        if (response.usuarios && Array.isArray(response.usuarios) && response.usuarios.length > 0) {
                            response.usuarios.forEach(function (usuario) {
                                options += `<option value="${usuario.id}" data-nombre="${usuario.name}" data-correo="${usuario.email}">
                                              ${usuario.name} (${usuario.email})
                                            </option>`;
                            });
                        } else {
                            options += '<option value="" disabled>No hay usuarios ADMIN disponibles</option>';
                        }
                        $('#usuarioRevisa').html(options);
                    },
                    error: function (xhr) {
                        $('#usuarioRevisa').html('<option value="" disabled>Error al cargar usuarios admin</option>');
                    }
                });
            }

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

            $('#userProfileDropdown').click(function (e) {
                e.preventDefault();
                $('#userDropdownMenu').toggleClass('show');
            });

            $(document).click(function (e) {
                if (!$(e.target).closest('#userProfileDropdown').length) {
                    $('#userDropdownMenu').removeClass('show');
                }
            });

            $('#btnTogglePassword').click(function () {
                const passwordField = $('#reviso_por');
                const icon = $('#iconTogglePassword');

                if (passwordField.attr('type') === 'password') {
                    passwordField.attr('type', 'text');
                    icon.removeClass('bi-eye').addClass('bi-eye-slash');
                } else {
                    passwordField.attr('type', 'password');
                    icon.removeClass('bi-eye-slash').addClass('bi-eye');
                }
            });

            // ✅ FUNCIÓN PARA EL MODAL DE MATERIALES CON SELECTOR DE LUGAR
            function openMaterialModal(mode, data = {}) {
                const modal = $('#modalMaterial');
                const title = $('#modalMaterialTitle');
                const body = $('#modalMaterialBody');
                const footer = $('#modalMaterialFooter');

                if (mode === 'view') {
                    title.text('Detalles del Material');
                    body.html(`
                        <div class="alert alert-info">
                            <strong>Lugar:</strong> ${data.lugar_nombre || 'Sin lugar asignado'}
                        </div>
                        <p><strong>Clave:</strong> ${data.clave_material || 'N/A'}</p>
                        <p><strong>Descripción:</strong> ${data.descripcion || 'N/A'}</p>
                        <p><strong>Genérico:</strong> ${data.generico || 'N/A'}</p>
                        <p><strong>Clasificación:</strong> ${data.clasificacion || 'N/A'}</p>
                        <p><strong>Existencia:</strong> ${data.existencia || 0}</p>
                        <p><strong>Costo ($):</strong> ${data.costo_promedio || 0}</p>
                    `);
                    footer.html('<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>');
                } else if (mode === 'edit' || mode === 'create') {
                    title.text(mode === 'edit' ? 'Editar Material' : 'Registrar Material');
                    
                    // ✅ SELECTOR DE LUGAR
                    let lugaresOptions = '<option value="">-- Selecciona un lugar --</option>';
                    @foreach($lugares as $lugar)
                        lugaresOptions += `<option value="{{ $lugar->id_lugar }}" ${data.id_lugar == {{ $lugar->id_lugar }} ? 'selected' : ''}>{{ $lugar->nombre }}</option>`;
                    @endforeach
                    
                    body.html(`
                        <div class="mb-3">
                            <label for="id_lugar_material" class="form-label">
                                <i class="fas fa-map-marker-alt me-2"></i>Lugar: <span class="text-danger">*</span>
                            </label>
                            <select id="id_lugar_material" name="id_lugar" class="form-select" required>
                                ${lugaresOptions}
                            </select>
                            <small class="form-text text-muted">Selecciona el lugar donde se almacenará este material</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="clave_material" class="form-label">Clave Material: <span class="text-danger">*</span></label>
                            <input type="text" 
                                   id="clave_material" 
                                   name="clave_material" 
                                   class="form-control" 
                                   value="${data.clave_material || ''}" 
                                   required 
                                   placeholder="Ej: MAT001"
                                   ${mode === 'edit' ? 'readonly' : ''}>
                            <small class="form-text text-muted">Código único del material</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="descripcion_material" class="form-label">Descripción: <span class="text-danger">*</span></label>
                            <input type="text" 
                                   id="descripcion_material" 
                                   name="descripcion" 
                                   class="form-control" 
                                   value="${data.descripcion || ''}" 
                                   required 
                                   placeholder="Descripción del material">
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="generico_material" class="form-label">Genérico:</label>
                                    <input type="text" 
                                           id="generico_material" 
                                           name="generico" 
                                           class="form-control" 
                                           value="${data.generico || ''}" 
                                           placeholder="Nombre genérico">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="clasificacion_material" class="form-label">Clasificación:</label>
                                    <input type="text" 
                                           id="clasificacion_material" 
                                           name="clasificacion" 
                                           class="form-control" 
                                           value="${data.clasificacion || ''}" 
                                           placeholder="Clasificación">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="existencia_material" class="form-label">Existencia: <span class="text-danger">*</span></label>
                                    <input type="number" 
                                           id="existencia_material" 
                                           name="existencia" 
                                           class="form-control" 
                                           value="${data.existencia || '0'}" 
                                           min="0" 
                                           required>
                                    <small class="form-text text-muted">Cantidad disponible</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="costo_material" class="form-label">Costo ($): <span class="text-danger">*</span></label>
                                    <input type="number" 
                                           id="costo_material" 
                                           name="costo_promedio" 
                                           class="form-control" 
                                           value="${data.costo_promedio || ''}" 
                                           min="0" 
                                           step="0.01" 
                                           required 
                                           placeholder="0.00">
                                </div>
                            </div>
                        </div>
                    `);
                    
                    // ✅ Si es modo crear y el usuario no es admin, preseleccionar su lugar
                    if (mode === 'create' && {{ Auth::user()->tipo_usuario != 1 ? 'true' : 'false' }} && {{ Auth::user()->id_lugar ?? 'null' }}) {
                        setTimeout(() => {
                            $('#id_lugar_material').val({{ Auth::user()->id_lugar ?? 'null' }});
                        }, 100);
                    }
                    
                    footer.html(`
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary" id="btnGuardarMaterial">
                            <i class="fas fa-save"></i> ${mode === 'edit' ? 'Actualizar' : 'Guardar'} Material
                        </button>
                    `);
                    $('#materialId').val(data.id_material || '');
                }
                modal.modal('show');
            }

            $('#usuarioReporta').on('change', function () {
                let selectedOption = $(this).find('option:selected');
                $('#nombreUsuarioReporta').val(selectedOption.data('nombre') || '');
                $('#correoUsuarioReporta').val(selectedOption.data('correo') || '');
            });

            $('#usuarioRevisa').on('change', function () {
                let selectedOption = $(this).find('option:selected');
                $('#nombreUsuarioRevisa').val(selectedOption.data('nombre') || '');
                $('#correoUsuarioRevisa').val(selectedOption.data('correo') || '');

                passwordVerified = false;
                $('#reviso_por').val('');
                $('#passwordError').hide();
                $('#passwordSuccess').hide();
                $('#btnGuardarPDF, #btnGuardarEnviarPDF').prop('disabled', true);
            });

            $('#btnValidarPassword').click(function () {
                let usuarioId = $('#usuarioRevisa').val();
                let password = $('#reviso_por').val();

                if (!usuarioId) {
                    alert('Por favor selecciona el usuario que revisa');
                    return;
                }

                if (!password) {
                    alert('Por favor ingresa la contraseña');
                    return;
                }

                $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Validando...');

                $.post('/verificar-password-usuario', {
                    usuario_id: usuarioId,
                    password: password
                })
                    .done(function (response) {
                        if (response.success) {
                            passwordVerified = true;
                            $('#passwordError').hide();
                            $('#passwordSuccess').show().text('Contraseña validada correctamente');
                            $('#btnGuardarPDF, #btnGuardarEnviarPDF').prop('disabled', false);
                            $('#btnValidarPassword').removeClass('btn-outline-primary').addClass('btn-success').html('<i class="fas fa-check"></i> Validado');
                        } else {
                            passwordVerified = false;
                            $('#passwordSuccess').hide();
                            $('#passwordError').show().text('Contraseña incorrecta para el usuario seleccionado');
                            $('#btnGuardarPDF, #btnGuardarEnviarPDF').prop('disabled', true);
                            $('#btnValidarPassword').prop('disabled', false).html('Validar');
                        }
                    })
                    .fail(function (xhr) {
                        passwordVerified = false;
                        $('#passwordSuccess').hide();
                        $('#passwordError').show().text('Error al verificar la contraseña');
                        $('#btnGuardarPDF, #btnGuardarEnviarPDF').prop('disabled', true);
                        $('#btnValidarPassword').prop('disabled', false).html('Validar');
                    });
            });

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

            $("#selectedMaterialsTable").on("click", ".btnInc", function () {
                let input = $(this).closest('tr').find('input.cantidad');
                input.val(parseInt(input.val()) + 1);
                actualizarMaterialsData();
            });
            
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
                $("#modalFalla").modal("show");
                passwordVerified = false;
                $('#btnGuardarPDF, #btnGuardarEnviarPDF').prop('disabled', true);
                $('#reviso_por').val('');
                $('#passwordError').hide();
                $('#passwordSuccess').hide();
                $('#nombreUsuarioRevisa').val('');
                $('#correoUsuarioRevisa').val('');
                $('#btnValidarPassword').removeClass('btn-success').addClass('btn-outline-primary').html('Validar');

                $('#correoDestino').prop('disabled', true).val('');
                $('#enviarCorreo').val('false');
            });

            $("#btnGuardarPDF").click(function () {
                if (!passwordVerified) {
                    alert('Por favor, valida la contraseña del usuario que revisa primero.');
                    return;
                }
                if (!validarFormulario()) {
                    return;
                }

                actualizarMaterialsData();
                $('#enviarCorreo').val('false');
                $('#correoDestino').val('');

                enviarFormulario(false);
            });

            $("#btnGuardarEnviarPDF").click(function () {
                if (!passwordVerified) {
                    alert('Por favor, valida la contraseña del usuario que revisa primero.');
                    return;
                }
                if (!validarFormulario()) {
                    return;
                }

                if ($('#correoDestino').prop('disabled')) {
                    $('#correoDestino').prop('disabled', false).focus();
                    alert('Por favor, ingresa el correo electrónico para enviar el reporte.');
                    return;
                }

                const correoDestino = $('#correoDestino').val().trim();
                if (!correoDestino) {
                    alert('Por favor, ingresa un correo electrónico válido.');
                    $('#correoDestino').focus();
                    return;
                }

                actualizarMaterialsData();
                $('#enviarCorreo').val('true');

                enviarFormulario(true);
            });

            function validarFormulario() {
                if (!$('#usuarioReporta').val()) {
                    alert('Por favor selecciona el usuario que reporta');
                    return false;
                }
                if (!$('#usuarioRevisa').val()) {
                    alert('Por favor selecciona el usuario que revisa');
                    return false;
                }
                return true;
            }

            function enviarFormulario(mostrarPDF) {
                const formData = $("#formFalla").serialize();

                const $btnGuardar = $('#btnGuardarPDF');
                const $btnGuardarEnviar = $('#btnGuardarEnviarPDF');

                $btnGuardar.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');
                $btnGuardarEnviar.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando y enviando...');

                $.post('/materials/reporte-falla', formData)
                    .done(function (response) {
                        if (response.data && response.data.id_falla) {
                            alert(response.message);

                            if (mostrarPDF && response.pdf_url) {
                                $("#iframePDF").attr("src", response.pdf_url);
                                $("#modalVerPDF").modal("show");
                            }

                            $('#modalFalla').modal('hide');
                            location.reload();
                        } else {
                            alert('Reporte creado pero no se pudo obtener el ID.');
                        }
                    })
                    .fail(function (xhr) {
                        handleAjaxError(xhr);
                    })
                    .always(function () {
                        $btnGuardar.prop('disabled', false).html('<i class="fas fa-save"></i> Guardar PDF');
                        $btnGuardarEnviar.prop('disabled', false).html('<i class="fas fa-paper-plane"></i> Guardar y enviar PDF');
                    });
            }

            $('.btnEditar').click(function () {
                let id = $(this).data('id');
                $.get(`/edit_material/${id}`, function (response) {
                    openMaterialModal('edit', response.data);
                });
            });

            $('.btnVer').click(function () {
                let id = $(this).data('id');
                $.get(`/materials/${id}`, function (response) {
                    openMaterialModal('view', response.data);
                });
            });

            $('#btnNuevoMaterial').click(function () {
                openMaterialModal('create');
            });

            // ✅ ENVÍO DEL FORMULARIO CON VALIDACIONES COMPLETAS
            $('#formMaterial').off('submit').on('submit', function (e) {
                e.preventDefault();
                
                console.log('🔄 Iniciando envío del formulario de material...');
                
                // ✅ VALIDACIONES ANTES DEL ENVÍO (INCLUYENDO LUGAR)
                const idLugar = $('#id_lugar_material').val();
                const clave = $('#clave_material').val();
                const descripcion = $('#descripcion_material').val();
                const existencia = $('#existencia_material').val();
                const costo = $('#costo_material').val();
                
                if (!idLugar || idLugar.trim() === '') {
                    alert('❌ Error: Debes seleccionar un lugar');
                    $('#id_lugar_material').focus();
                    return false;
                }
                
                if (!clave || clave.trim() === '') {
                    alert('❌ Error: La clave del material es obligatoria');
                    $('#clave_material').focus();
                    return false;
                }
                
                if (!descripcion || descripcion.trim() === '') {
                    alert('❌ Error: La descripción es obligatoria');
                    $('#descripcion_material').focus();
                    return false;
                }
                
                if (!existencia || existencia < 0) {
                    alert('❌ Error: La existencia debe ser un número mayor o igual a 0');
                    $('#existencia_material').focus();
                    return false;
                }
                
                if (!costo || costo <= 0) {
                    alert('❌ Error: El costo debe ser mayor a 0');
                    $('#costo_material').focus();
                    return false;
                }
                
                const id = $('#materialId').val();
                const url = id ? `/materials/${id}` : '/materials';
                const method = id ? 'PUT' : 'POST';
                
                const $btn = $('#btnGuardarMaterial');
                const originalText = $btn.html();
                $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');
                
                $.ajax({
                    url: url,
                    type: method,
                    data: $(this).serialize(),
                    success: function (response) {
                        console.log('✅ Material guardado exitosamente:', response);
                        
                        // Mostrar mensaje de éxito mejorado
                        const successAlert = `
                            <div class="alert alert-success alert-dismissible fade show position-fixed" 
                                 style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
                                <i class="fas fa-check-circle me-2"></i>
                                <strong>¡Éxito!</strong> ${response.message}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        `;
                        $('body').append(successAlert);
                        
                        // Auto-remover después de 3 segundos
                        setTimeout(() => {
                            $('.alert-success').fadeOut();
                        }, 3000);
                        
                        $('#modalMaterial').modal('hide');
                        
                        // Recargar la página después de un breve delay
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    },
                    error: function(xhr) {
                        console.error('❌ Error al guardar material:', xhr);
                        
                        let errorMsg = 'Error desconocido';
                        
                        if (xhr.responseJSON) {
                            if (xhr.responseJSON.error) {
                                errorMsg = xhr.responseJSON.error;
                            } else if (xhr.responseJSON.message) {
                                errorMsg = xhr.responseJSON.message;
                            } else if (xhr.responseJSON.errors) {
                                const errors = Object.values(xhr.responseJSON.errors).flat();
                                errorMsg = errors.join('<br>');
                            }
                        } else {
                            errorMsg = xhr.responseText || 'Error de conexión';
                        }
                        
                        const errorAlert = `
                            <div class="alert alert-danger alert-dismissible fade show position-fixed" 
                                 style="top: 20px; right: 20px; z-index: 9999; min-width: 350px; max-width: 500px;">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Error:</strong><br>${errorMsg}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        `;
                        $('body').append(errorAlert);
                        
                        // Auto-remover después de 8 segundos
                        setTimeout(() => {
                            $('.alert-danger').fadeOut();
                        }, 8000);
                    },
                    complete: function() {
                        $btn.prop('disabled', false).html(originalText);
                    }
                });
            });

            $('.btnEliminar').click(function () {
                const id = $(this).data('id');
                $('#btnConfirmarEliminar').data('id', id);
                $('#modalEliminar').modal('show');
            });

            $('#btnConfirmarEliminar').click(function () {
                const id = $(this).data('id');
                $.ajax({
                    url: `/materials/${id}`,
                    type: 'DELETE',
                    success: function (response) {
                        alert(response.message);
                        $('#modalEliminar').modal('hide');
                        location.reload();
                    },
                    error: handleAjaxError
                });
            });

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
                    url: `/materials/${id}/aumentar`,
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

            $('#formCardex').submit(function (e) {
                e.preventDefault();
                const formData = new FormData(this);
                $('#progreso').show();
                $('.progress-bar').css('width', '0%');
                $.ajax({
                    url: '/materials/import',
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

            $("#selectedMaterialsTable").on("change", ".cantidad", function () {
                actualizarMaterialsData();
            });

            $('#campo_fecha').val(new Date().toISOString().split('T')[0]);

            // Funciones globales para notificaciones
            window.verDetalleNotificacion = function(id) {
                $.get(`/notificaciones/${id}`)
                    .done(function (response) {
                        if (response.success) {
                            const notif = response.notificacion;

                            let materialesHtml = '';
                            if (notif.materiales && notif.materiales.length > 0) {
                                materialesHtml = `
                                    <table class="table table-sm table-bordered">
                                        <thead>
                                            <tr><th>Material</th><th>Cantidad</th><th>Costo Unit.</th><th>Total</th></tr>
                                        </thead>
                                        <tbody>
                                `;
                                notif.materiales.forEach(function (mat) {
                                    materialesHtml += `
                                        <tr>
                                            <td>${mat.descripcion}</td>
                                            <td>${mat.cantidad}</td>
                                            <td>${mat.costo_unitario || 0}</td>
                                            <td>${mat.costo_total || 0}</td>
                                        </tr>
                                    `;
                                });
                                materialesHtml += '</tbody></table>';
                            } else {
                                materialesHtml = '<p class="text-muted">No hay materiales especificados.</p>';
                            }

                            $('#detalleNotificacionBody').html(`
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>Información del Vehículo</h6>
                                        <p><strong>ECO:</strong> ${notif.eco || 'N/A'}</p>
                                        <p><strong>Placas:</strong> ${notif.placas || 'N/A'}</p>
                                        <p><strong>Marca:</strong> ${notif.marca || 'N/A'}</p>
                                        <p><strong>Año:</strong> ${notif.anio || 'N/A'}</p>
                                        <p><strong>KM:</strong> ${notif.km || 'N/A'}</p>
                                        <p><strong>Conductor:</strong> ${notif.nombre_conductor || 'N/A'}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Información del Reporte</h6>
                                        <p><strong>Lugar:</strong> ${notif.lugar_nombre}</p>
                                        <p><strong>Fecha:</strong> ${notif.fecha || 'N/A'}</p>
                                        <p><strong>Reportado por:</strong> ${notif.usuario_reporta}</p>
                                        <p><strong>Correo:</strong> ${notif.correo_reporta}</p>
                                        <p><strong>Fecha de creación:</strong> ${notif.fecha_creacion}</p>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <h6>Descripción del Problema</h6>
                                    <p>${notif.descripcion || 'Sin descripción'}</p>
                                </div>
                                <div class="mt-3">
                                    <h6>Observaciones Técnicas</h6>
                                    <p>${notif.observaciones || 'Sin observaciones'}</p>
                                </div>
                                <div class="mt-3">
                                    <h6>Materiales Solicitados</h6>
                                    ${materialesHtml}
                                </div>
                                ${notif.correo_destino ? `
                                <div class="mt-3">
                                    <h6>Correo de Notificación</h6>
                                    <p>${notif.correo_destino}</p>
                                </div>
                                ` : ''}
                            `);

                            $('#modalDetalleNotificacion').modal('show');
                        }
                    })
                    .fail(function (xhr) {
                        alert('Error al cargar detalles: ' + (xhr.responseJSON?.message || 'Error desconocido'));
                    });
            };

            window.aprobarNotificacion = function(id) {
                currentNotificationId = id;
                $('#passwordAprobar').val('');
                $('#comentariosAprobar').val('');
                $('#errorAprobar').addClass('d-none');
                $('#datosUsuarioAutoriza').hide();
                $('#passwordValidating, #passwordValidSuccess').hide();
                $('#btnConfirmarAprobar').prop('disabled', true);
                $('#modalAprobarNotificacion').modal('show');
            };

            window.rechazarNotificacion = function(id) {
                currentNotificationId = id;
                $('#comentariosRechazar').val('');
                $('#errorRechazar').addClass('d-none');
                $('#modalRechazarNotificacion').modal('show');
            };

            $('#notificationBell').click(function () {
                $('#modalNotificaciones').modal('show');
                loadNotifications();
            });

            $('#btnRefreshNotifications').click(function () {
                loadNotifications();
                loadNotificationCounter();
            });

            $('#btnTogglePasswordAprobar').click(function () {
                const passwordField = $('#passwordAprobar');
                const icon = $('#iconTogglePasswordAprobar');

                if (passwordField.attr('type') === 'password') {
                    passwordField.attr('type', 'text');
                    icon.removeClass('bi-eye').addClass('bi-eye-slash');
                } else {
                    passwordField.attr('type', 'password');
                    icon.removeClass('bi-eye-slash').addClass('bi-eye');
                }
            });

            $('#passwordAprobar').on('input', function () {
                const password = $(this).val();

                if (passwordValidationTimeout) {
                    clearTimeout(passwordValidationTimeout);
                }

                $(this).removeClass('password-validation-success password-validation-error');
                $('#passwordValidating, #passwordValidSuccess').hide();
                $('#datosUsuarioAutoriza').hide();
                $('#errorAprobar').addClass('d-none');
                $('#btnConfirmarAprobar').prop('disabled', true);

                if (password.length === 0) {
                    return;
                }

                if (password.length >= 3) {
                    $('#passwordValidating').show();

                    passwordValidationTimeout = setTimeout(function () {
                        validarPasswordAprobacion(password);
                    }, 800);
                }
            });

            function validarPasswordAprobacion(password) {
                $.post('/verificar-password', { password: password })
                    .done(function (response) {
                        $('#passwordValidating').hide();

                        if (response.success) {
                            $('#passwordAprobar').removeClass('password-validation-error').addClass('password-validation-success');
                            $('#passwordValidSuccess').show();
                            $('#nombreUsuarioAutoriza').text('{{ Auth::user()->nombre ?? Auth::user()->name ?? "Usuario" }}');
                            $('#correoUsuarioAutoriza').text('{{ Auth::user()->correo ?? Auth::user()->email ?? "sin-correo@example.com" }}');
                            $('#datosUsuarioAutoriza').slideDown();
                            $('#errorAprobar').addClass('d-none');
                            updateApproveButtonState();
                        } else {
                            $('#passwordAprobar').removeClass('password-validation-success').addClass('password-validation-error');
                            $('#passwordValidSuccess').hide();
                            $('#datosUsuarioAutoriza').hide();
                            $('#errorAprobarMessage').text('Contraseña incorrecta');
                            $('#errorAprobar').removeClass('d-none');
                            updateApproveButtonState();
                        }
                    })
                    .fail(function (xhr) {
                        $('#passwordValidating').hide();
                        $('#passwordAprobar').removeClass('password-validation-success').addClass('password-validation-error');
                        $('#passwordValidSuccess').hide();
                        $('#datosUsuarioAutoriza').hide();
                        $('#errorAprobarMessage').text('Error al verificar contraseña');
                        $('#errorAprobar').removeClass('d-none');
                        updateApproveButtonState();
                    });
            }

            $('#comentariosAprobar').on('input', function () {
                updateApproveButtonState();
            });

            function updateApproveButtonState() {
                const passwordValid = $('#passwordAprobar').hasClass('password-validation-success');
                const emailValid = isValidEmail($('#comentariosAprobar').val());
                $('#btnConfirmarAprobar').prop('disabled', !(passwordValid && emailValid));
            }

            function isValidEmail(email) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return email && emailRegex.test(email);
            }

            $('#btnConfirmarAprobar').click(function () {
                const password = $('#passwordAprobar').val();
                const correo = $('#comentariosAprobar').val();

                if (!password) {
                    $('#errorAprobarMessage').text('La contraseña es requerida');
                    $('#errorAprobar').removeClass('d-none');
                    return;
                }

                if (!isValidEmail(correo)) {
                    $('#errorAprobarMessage').text('Ingresa un correo electrónico válido');
                    $('#errorAprobar').removeClass('d-none');
                    return;
                }

                const $btn = $(this);
                $btn.addClass('loading').prop('disabled', true);
                $btn.html('<span>Procesando...</span>');

                $.post(`/notificaciones/${currentNotificationId}/aprobar`, {
                    password: password,
                    comentarios: correo
                })
                    .done(function (response) {
                        if (response.success) {
                            $('#modalAprobarNotificacion').modal('hide');
                            $('#modalNotificaciones').modal('hide');

                            showSuccessMessage('✅ ' + response.message);

                            loadNotificationCounter();
                            setTimeout(() => {
                                location.reload();
                            }, 1500);
                        }
                    })
                    .fail(function (xhr) {
                        const error = xhr.responseJSON?.error || 'Error al aprobar notificación';
                        $('#errorAprobarMessage').text(error);
                        $('#errorAprobar').removeClass('d-none');
                    })
                    .always(function () {
                        $btn.removeClass('loading').prop('disabled', false);
                        $btn.html('<i class="fas fa-check"></i> Aprobar y Enviar');
                    });
            });

            function showSuccessMessage(message) {
                const $message = $(`
                    <div class="position-fixed top-50 start-50 translate-middle bg-success text-white p-4 rounded-4 shadow-lg" style="z-index: 9999; min-width: 300px; text-align: center;">
                        <i class="fas fa-check-circle me-2 fa-lg"></i>
                        <strong>${message}</strong>
                    </div>
                `);

                $('body').append($message);

                setTimeout(() => {
                    $message.fadeOut(400, function () {
                        $(this).remove();
                    });
                }, 3000);
            }

            $('#btnConfirmarRechazar').click(function () {
                const comentarios = $('#comentariosRechazar').val();

                if (!comentarios.trim()) {
                    $('#errorRechazarMessage').text('El motivo del rechazo es requerido');
                    $('#errorRechazar').removeClass('d-none');
                    return;
                }

                const $btn = $(this);
                $btn.addClass('loading').prop('disabled', true);
                $btn.html('<span>Procesando...</span>');

                $.post(`/notificaciones/${currentNotificationId}/rechazar`, {
                    comentarios: comentarios
                })
                    .done(function (response) {
                        if (response.success) {
                            $('#modalRechazarNotificacion').modal('hide');

                            showSuccessMessage('❌ ' + response.message);

                            loadNotifications();
                            loadNotificationCounter();
                        }
                    })
                    .fail(function (xhr) {
                        const error = xhr.responseJSON?.error || 'Error al rechazar notificación';
                        $('#errorRechazarMessage').text(error);
                        $('#errorRechazar').removeClass('d-none');
                    })
                    .always(function () {
                        $btn.removeClass('loading').prop('disabled', false);
                        $btn.html('<i class="fas fa-times"></i> Rechazar');
                    });
            });

            $('#modalAprobarNotificacion, #modalRechazarNotificacion').on('hidden.bs.modal', function () {
                currentNotificationId = null;
                $(this).find('.alert').addClass('d-none');
                $(this).find('input, textarea').val('');

                if ($(this).is('#modalAprobarNotificacion')) {
                    $('#datosUsuarioAutoriza').hide();
                    $('#passwordValidating, #passwordValidSuccess').hide();
                    $('#btnConfirmarAprobar').prop('disabled', true);
                    $('#passwordAprobar').attr('type', 'password')
                        .removeClass('password-validation-success password-validation-error');
                    $('#iconTogglePasswordAprobar').removeClass('bi-eye-slash').addClass('bi-eye');

                    if (passwordValidationTimeout) {
                        clearTimeout(passwordValidationTimeout);
                        passwordValidationTimeout = null;
                    }
                }
            });

            $(window).on('beforeunload', function () {
                stopNotificationRefresh();
            });

            // Exponer función openMaterialModal globalmente
            window.openMaterialModal = openMaterialModal;

        });
    </script>
</body>

</html>