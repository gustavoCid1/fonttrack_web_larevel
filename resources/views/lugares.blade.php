<!DOCTYPE html>
<html lang="es">

<head>
    <!-- 
    Sistema de Gestión de Lugares - FontTrack
    @author Jesús Felipe Avilez
    @author Gustavo Angel Cid Flores
    version 2.0.0
    Descripción: Interfaz para la administración de lugares del sistema,
    incluyendo registro, edición, visualización y eliminación de ubicaciones
    -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Lugares</title>
    
    <!-- Enlaces a librerías externas -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <style>
        /* Variables CSS para colores del tema */
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

        /* Animaciones CSS */
        @keyframes float {
            0%, 100% {
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

        /* Animaciones avanzadas para modales */
        @keyframes modalBubbleIn {
            0% {
                transform: scale(0.3) translateY(100px);
                opacity: 0;
            }
            50% {
                transform: scale(1.05) translateY(-10px);
                opacity: .8;
            }
            70% {
                transform: scale(0.95) translateY(5px);
                opacity: .9;
            }
            100% {
                transform: scale(1) translateY(0);
                opacity: 1;
            }
        }

        @keyframes modalWaterDropIn {
            0% {
                transform: scale(0) rotate(0deg);
                opacity: 0;
                filter: blur(10px);
            }
            30% {
                transform: scale(0.7) rotate(180deg);
                opacity: 0.7;
                filter: blur(5px);
            }
            60% {
                transform: scale(1.1) rotate(270deg);
                opacity: 0.9;
                filter: blur(2px);
            }
            100% {
                transform: scale(1) rotate(360deg);
                opacity: 1;
                filter: blur(0px);
            }
        }

        @keyframes ripple {
            0% {
                transform: scale(0, 0);
                opacity: 0.5;
            }
            100% {
                transform: scale(20, 20);
                opacity: 0;
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Animación de sacudida para botón de eliminar */
        @keyframes shake {
            0%, 100% {
                transform: translateX(0) scale(1.2);
            }
            25% {
                transform: translateX(-2px) scale(1.2);
            }
            75% {
                transform: translateX(2px) scale(1.2);
            }
        }

        /* Estilos del cuerpo y fondo */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, var(--light-cream) 0%, #f8f1e8 100%);
            color: var(--dark-brown);
            margin: 0;
            padding: 0;
            min-height: 100vh;
            position: relative;
        }

        /* Fondo decorativo con gradientes radiales */
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

        /* Barra de navegación */
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

        /* Efecto de vidrio en la navbar */
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

        /* Logo de la aplicación */
        .navbar .logo {
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.2));
        }

        .navbar .logo:hover {
            transform: scale(1.08) rotate(2deg);
            filter: drop-shadow(0 8px 16px rgba(0, 0, 0, 0.3)) brightness(1.1);
        }

        /* Brand de la navbar */
        .navbar .navbar-brand {
            color: white;
            font-weight: 700;
            font-size: 1.8em;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }

        .navbar .navbar-brand:hover {
            color: rgba(255, 255, 255, 0.9);
            transform: translateY(-2px);
        }

        /* Enlaces de navegación */
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

        /* Efecto shimmer en hover de enlaces */
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

        /* Icono del toggler para móviles */
        .navbar-toggler-icon {
            filter: invert(1);
        }

        /* Perfil de usuario */
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

        /* Dropdown del perfil de usuario */
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

        /* Contenedor principal */
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

        /* Efecto flotante en el contenedor */
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

        /* Títulos principales */
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

        /* Línea decorativa bajo los títulos */
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

        /* Contenedor del buscador */
        .search-container {
            background: linear-gradient(135deg, var(--primary-orange) 0%, var(--secondary-orange) 100%);
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 15px 40px var(--shadow-medium);
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
            z-index: 1;
        }

        /* Efecto flotante en el buscador */
        .search-container::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            animation: float 6s ease-in-out infinite;
        }

        /* Campo de búsqueda */
        .search-input {
            border: none;
            border-radius: 50px;
            padding: 18px 25px;
            font-size: 16px;
            font-weight: 500;
            box-shadow: inset 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            position: relative;
            z-index: 1;
        }

        .search-input:focus {
            outline: none;
            box-shadow: 0 0 30px rgba(227, 139, 91, 0.4), inset 0 4px 8px rgba(0, 0, 0, 0.1);
            transform: scale(1.02);
            background: white;
        }

        .search-input::placeholder {
            color: rgba(99, 77, 59, 0.6);
            font-weight: 400;
        }

        /* Estilos de botones */
        .btn {
            font-size: 1rem;
            font-weight: 600;
            border-radius: 12px;
            padding: 14px 20px;
            transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            border: none;
            position: relative;
            overflow: hidden;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        /* Efecto shimmer en botones */
        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .btn:hover::before {
            left: 100%;
        }

        /* Botón primario */
        .btn-primary {
            background: linear-gradient(135deg, var(--secondary-orange), var(--primary-orange));
            color: white;
            box-shadow: 0 4px 15px rgba(227, 139, 91, 0.3);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--primary-orange), var(--secondary-orange));
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 8px 25px rgba(227, 139, 91, 0.4);
            color: white;
        }

        /* Botón de información */
        .btn-info {
            background: linear-gradient(135deg, var(--accent-blue), #7ab8c8);
            color: white;
            box-shadow: 0 4px 15px rgba(136, 192, 208, 0.3);
        }

        .btn-info:hover {
            background: linear-gradient(135deg, #7ab8c8, var(--accent-blue));
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 8px 25px rgba(136, 192, 208, 0.4);
            color: white;
        }

        /* Botón de advertencia */
        .btn-warning {
            background: linear-gradient(135deg, var(--accent-yellow), #d4901a);
            color: white;
            box-shadow: 0 4px 15px rgba(229, 163, 77, 0.3);
        }

        .btn-warning:hover {
            background: linear-gradient(135deg, #d4901a, var(--accent-yellow));
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 8px 25px rgba(229, 163, 77, 0.4);
            color: white;
        }

        /* Botón de peligro */
        .btn-danger {
            background: linear-gradient(135deg, var(--accent-red), #c9302c);
            color: white;
            box-shadow: 0 4px 15px rgba(217, 83, 79, 0.3);
        }

        .btn-danger:hover {
            background: linear-gradient(135deg, #c9302c, var(--accent-red));
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 8px 25px rgba(217, 83, 79, 0.4);
            color: white;
        }

        /* Botón secundario */
        .btn-secondary {
            background: linear-gradient(135deg, #6c757d, #5a6268);
            color: white;
            box-shadow: 0 4px 15px rgba(108, 117, 125, 0.3);
        }

        .btn-secondary:hover {
            background: linear-gradient(135deg, #5a6268, #6c757d);
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 8px 25px rgba(108, 117, 125, 0.4);
            color: white;
        }

        /* Botón outline light */
        .btn-outline-light {
            border: 2px solid rgba(255, 255, 255, 0.3);
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border-radius: 50px;
            padding: 18px 25px;
            font-weight: 600;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .btn-outline-light:hover {
            background: white;
            color: var(--secondary-orange);
            border-color: white;
            transform: scale(1.05);
            box-shadow: 0 8px 20px rgba(255, 255, 255, 0.3);
        }

        /* Botones pequeños */
        .btn-sm {
            padding: 8px 16px;
            font-size: 0.9rem;
            border-radius: 10px;
        }

        /* Contenedor de botones de acción */
        .action-buttons {
            display: flex;
            gap: 8px;
            justify-content: center;
            align-items: center;
        }

        .action-buttons .btn {
            width: 40px;
            height: 40px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            position: relative;
            overflow: hidden;
        }

        .action-buttons .btn i {
            font-size: 1.1rem;
            transition: all 0.3s ease;
        }

        /* Efectos hover específicos para cada botón */
        .btn-info:hover i {
            transform: scale(1.2);
        }

        .btn-warning:hover i {
            transform: rotate(15deg) scale(1.2);
        }

        .btn-danger:hover i {
            transform: scale(1.2);
            animation: shake 0.5s ease-in-out;
        }

        /* Modales */
        .modal.show .modal-dialog {
            animation: modalBubbleIn .6s cubic-bezier(.68, -.55, .265, 1.55);
        }

        .modal-content {
            overflow: hidden;
            border-radius: 20px;
            box-shadow: 0 25px 80px var(--shadow-heavy);
            border: none;
            backdrop-filter: blur(10px);
        }

        .modal-backdrop {
            background: linear-gradient(45deg, rgba(0, 0, 0, .5), rgba(0, 0, 0, .7));
            backdrop-filter: blur(5px);
        }

        /* Encabezado del modal */
        .modal-header {
            background: linear-gradient(135deg, var(--primary-orange), var(--secondary-orange));
            color: white;
            border: none;
            padding: 20px 30px;
        }

        .modal-title {
            font-weight: 600;
            font-size: 1.3rem;
        }

        .btn-close {
            filter: brightness(0) invert(1);
            opacity: 0.8;
        }

        .btn-close:hover {
            opacity: 1;
            transform: scale(1.1);
        }

        /* Cuerpo del modal */
        .modal-body {
            padding: 30px;
            background: linear-gradient(135deg, #fff 0%, #FCE8D5 100%);
        }

        /* Pie del modal */
        .modal-footer {
            background: linear-gradient(135deg, var(--primary-orange), var(--secondary-orange));
            border: none;
            padding: 20px 30px;
        }

        /* Efecto ripple para botones de modal */
        .modal .btn {
            position: relative;
            overflow: hidden;
            z-index: 1;
        }

        .modal .btn:after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 5px;
            height: 5px;
            background: rgba(255, 255, 255, 0.5);
            opacity: 0;
            border-radius: 100%;
            transform: scale(1, 1) translate(-50%);
            transform-origin: 50% 50%;
        }

        .modal .btn:focus:after,
        .modal .btn:hover:after {
            animation: ripple 1s ease-out;
        }

        /* Tabla responsive */
        .table-responsive {
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 40px var(--shadow-light);
            background: white;
            margin-bottom: 30px;
            position: relative;
            z-index: 1;
        }

        .table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin: 0;
        }

        /* Encabezado de la tabla */
        .table thead {
            background: linear-gradient(135deg, var(--primary-orange) 0%, var(--secondary-orange) 100%);
            color: white;
            position: relative;
        }

        .table thead::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.5), transparent);
        }

        .table th {
            padding: 20px 15px;
            font-weight: 600;
            font-size: 1.1rem;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
            border: none;
            position: relative;
        }

        /* Separadores verticales en el encabezado */
        .table th::after {
            content: '';
            position: absolute;
            right: 0;
            top: 25%;
            bottom: 25%;
            width: 1px;
            background: rgba(255, 255, 255, 0.3);
        }

        .table th:last-child::after {
            display: none;
        }

        /* Celdas de la tabla */
        .table td {
            padding: 18px 15px;
            border: none;
            border-bottom: 1px solid rgba(246, 184, 143, 0.1);
            font-weight: 500;
            vertical-align: middle;
        }

        /* Filas de la tabla */
        .table tbody tr {
            transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            cursor: pointer;
            position: relative;
        }

        .table tbody tr:hover {
            background: linear-gradient(135deg, rgba(246, 184, 143, 0.1), rgba(227, 139, 91, 0.05));
            transform: scale(1.01) translateY(-2px);
            box-shadow: 0 8px 25px rgba(246, 184, 143, 0.2);
            border-radius: 10px;
        }

        /* Paginación */
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 30px 20px;
            list-style: none;
            margin: 0;
            gap: 5px;
        }

        .pagination li {
            margin: 0;
        }

        .pagination li a,
        .pagination li span {
            text-decoration: none;
            padding: 12px 18px;
            background: linear-gradient(135deg, var(--secondary-orange), var(--primary-orange));
            color: white;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            border: 2px solid transparent;
            min-width: 48px;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 15px rgba(227, 139, 91, 0.3);
        }

        .pagination li a:hover {
            background: linear-gradient(135deg, var(--primary-orange), var(--secondary-orange));
            transform: translateY(-3px) scale(1.1);
            box-shadow: 0 8px 25px rgba(227, 139, 91, 0.4);
        }

        /* Página activa en paginación */
        .pagination .active span {
            background: linear-gradient(135deg, var(--dark-brown), #4a3a2a);
            border: 2px solid var(--secondary-orange);
            transform: scale(1.1);
            box-shadow: 0 8px 25px rgba(99, 77, 59, 0.4);
        }

        /* Página deshabilitada en paginación */
        .pagination .disabled span {
            background: linear-gradient(135deg, #ccc, #aaa);
            color: #666;
            cursor: not-allowed;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        /* Vista previa de imagen */
        .image-preview {
            max-width: 150px;
            max-height: 150px;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
            border: 3px solid rgba(246, 184, 143, 0.3);
        }

        .image-preview:hover {
            transform: scale(1.05) rotate(2deg);
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.3);
            border-color: var(--secondary-orange);
        }

        /* Controles de formulario */
        .form-control,
        .form-select {
            border-radius: 12px;
            padding: 15px 20px;
            border: 2px solid rgba(246, 184, 143, 0.3);
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--secondary-orange);
            box-shadow: 0 0 20px rgba(227, 139, 91, 0.3);
            outline: none;
        }

        .form-label {
            font-weight: 600;
            color: var(--dark-brown);
            margin-bottom: 8px;
        }

        /* Estilos de alertas */
        .alert {
            border-radius: 15px;
            border: none;
            padding: 15px 20px;
            margin-bottom: 20px;
            animation: slideInUp 0.5s ease-out;
        }

        .alert-success {
            background: linear-gradient(135deg, rgba(40, 167, 69, 0.1), rgba(40, 167, 69, 0.05));
            color: #155724;
            border-left: 4px solid #28a745;
        }

        /* Estilos responsive */
        @media (max-width: 768px) {
            .container {
                margin: 15px;
                padding: 25px;
                border-radius: 20px;
            }

            h2 {
                font-size: 2rem;
            }

            .search-container {
                padding: 20px;
            }

            .search-input {
                padding: 15px 20px;
                font-size: 14px;
            }

            .table th,
            .table td {
                padding: 12px 8px;
                font-size: 0.9rem;
            }

            .btn-sm {
                padding: 6px 12px;
                font-size: 0.8rem;
            }

            .user-profile img {
                width: 35px;
                height: 35px;
            }

            .navbar .nav-link {
                padding: 10px 15px !important;
                font-size: 1rem;
            }

            .user-profile {
                margin-top: 10px;
            }

            .user-dropdown {
                position: static;
                margin-top: 5px;
            }

            /* Responsive para botones de acción */
            .action-buttons {
                flex-direction: column;
                gap: 4px;
            }

            .action-buttons .btn {
                width: 35px;
                height: 35px;
            }

            .action-buttons .btn i {
                font-size: 0.9rem;
            }
        }
    </style>
</head>

<body>
    <!-- Barra de navegación principal -->
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <!-- Logo de la aplicación -->
            <img src="{{ asset('img/FontTrack.png') }}" alt="logo" height="70px" width="100px" class="logo"
                onclick="window.location.href='{{ route('users') }}'">
            
            <!-- Botón de menú móvil -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <!-- Enlaces de navegación -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('users') }}">
                            <i class="bi bi-house me-2"></i>Inicio
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('materials') }}">
                            <i class="bi bi-box-seam me-2"></i>Materiales
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

                <!-- Perfil de usuario -->
                <div class="user-profile ms-auto">
                    <img src="{{ Auth::user()->foto_usuario_url ?? asset('img/usuario_default.png') }}"
                        alt="Foto de perfil">
                    <span class="user-name">{{ Auth::user()->nombre }}</span>
                    <div class="user-dropdown">
                        <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="bi bi-box-arrow-right"></i> Cerrar sesión
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Contenido principal -->
    <div class="container mt-5">
        <h2 class="mb-4"><i class="bi bi-geo-alt me-3"></i>Lista de Lugares</h2>

        <!-- Botón para crear nuevo lugar -->
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalRegistro">
            <i class="bi bi-plus-circle me-2"></i>Nuevo Lugar
        </button>

        <!-- Mensaje de éxito -->
        @if(session('success'))
            <div class="alert alert-success">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            </div>
        @endif

        <!-- Tabla de lugares -->
        <div class="table-responsive mt-3">
            <table class="table mt-3">
                <thead>
                    <tr>
                        <th><i class="bi bi-card-text me-2"></i>Nombre</th>
                        <th><i class="bi bi-map me-2"></i>Estado</th>
                        <th><i class="bi bi-gear me-2"></i>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($lugares as $lugar)
                        <tr data-id="{{ $lugar->id_lugar }}">
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-geo-alt-fill text-primary me-2"></i>
                                    <strong>{{ $lugar->nombre }}</strong>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-map-fill text-success me-2"></i>
                                    {{ $lugar->estado ?? 'Sin especificar' }}
                                </div>
                            </td>
                            <td>
                                <!-- Botones de acción para cada lugar -->
                                <div class="action-buttons">
                                    <button class="btn btn-info btn-sm btnVer" data-id="{{ $lugar->id_lugar }}"
                                        title="Ver detalles">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <button class="btn btn-warning btn-sm btnEditar" data-id="{{ $lugar->id_lugar }}"
                                        title="Editar lugar">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-danger btn-sm btnEliminar" data-id="{{ $lugar->id_lugar }}"
                                        title="Eliminar lugar">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal de Registro/Edición de Lugar -->
    <div class="modal fade" id="modalRegistro" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="formLugar">
                    @csrf
                    <input type="hidden" id="lugarId">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bi bi-geo-alt-fill me-2"></i>Registrar / Editar Lugar
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Campo de nombre del lugar -->
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre:</label>
                            <input type="text" id="nombre" name="nombre" class="form-control" required>
                        </div>

                        <!-- Campo de estado del lugar -->
                        <div class="mb-3">
                            <label for="estado" class="form-label">Estado:</label>
                            <input type="text" id="estado" name="estado" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-2"></i>Cerrar
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i>Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para Ver Detalles del Lugar -->
    <div class="modal fade" id="modalVer" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-eye me-2"></i>Detalles del Lugar
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="card mb-3">
                                <div class="card-body">
                                    <p class="card-text">
                                        <i class="bi bi-card-text text-primary me-2"></i>
                                        <strong>Nombre:</strong>
                                        <span id="verNombre" class="ms-2"></span>
                                    </p>
                                    <p class="card-text">
                                        <i class="bi bi-map text-success me-2"></i>
                                        <strong>Estado:</strong>
                                        <span id="verEstado" class="ms-2"></span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-2"></i>Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        /*
         * Sistema de Gestión de Lugares - FontTrack
         * Autor: Gustavo Angel Cid Flores
         * 
         * Script principal que maneja:
         * - CRUD completo de lugares (crear, leer, actualizar, eliminar)
         * - Modales interactivos para gestión de datos
         * - Validación de formularios
         * - Interfaz de usuario responsive
         * - Manejo de estados y mensajes
         */

        $(document).ready(function () {
            // Configuración inicial del token CSRF para peticiones AJAX
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            /**
             * Manejo del menú desplegable del perfil de usuario
             * Previene propagación del evento para evitar cierre automático
             */
            $('.user-profile').click(function (e) {
                e.stopPropagation();
                $('.user-dropdown').toggleClass('show');
            });

            /**
             * Cierra el menú desplegable al hacer clic fuera del área
             */
            $(document).click(function () {
                $('.user-dropdown').removeClass('show');
            });

            /**
             * Maneja la edición de un lugar existente
             * Carga los datos del lugar en el modal de edición
             */
            $('.btnEditar').click(function () {
                let id = $(this).data('id');
                
                // Petición AJAX para obtener datos del lugar
                $.get(`/lugares/${id}/edit`, function (response) {
                    $('#lugarId').val(response.data.id_lugar);
                    $('#nombre').val(response.data.nombre);
                    $('#estado').val(response.data.estado);
                    $('#modalRegistro').modal('show');
                }).fail(function() {
                    alert('Error al cargar los datos del lugar');
                });
            });

            /**
             * Maneja la visualización de detalles de un lugar
             * Muestra la información en un modal de solo lectura
             */
            $('.btnVer').click(function () {
                let id = $(this).data('id');
                
                // Petición AJAX para obtener detalles del lugar
                $.get(`/lugares/${id}`, function (response) {
                    $('#verNombre').text(response.data.nombre);
                    $('#verEstado').text(response.data.estado || 'Sin especificar');
                    $('#modalVer').modal('show');
                }).fail(function() {
                    alert('Error al cargar los detalles del lugar');
                });
            });

            /**
             * Maneja la eliminación de un lugar
             * Solicita confirmación antes de proceder
             */
            $('.btnEliminar').click(function () {
                let id = $(this).data('id');
                
                // Confirmación antes de eliminar
                if (confirm('¿Seguro que quieres eliminar este lugar?')) {
                    $.ajax({
                        url: `/lugares/${id}`,
                        type: 'DELETE',
                        success: function (response) {
                            alert(response.message);
                            location.reload(); // Recargar página para reflejar cambios
                        },
                        error: function (xhr) {
                            alert('Error al eliminar lugar: ' + xhr.responseText);
                        }
                    });
                }
            });

            /**
             * Maneja el envío del formulario de lugar (crear/editar)
             * Determina automáticamente si es creación o edición basado en el ID
             */
            $('#formLugar').submit(function (event) {
                event.preventDefault();
                
                let id = $('#lugarId').val();
                let url = id ? `/lugares/${id}` : `/lugares`;
                let method = id ? 'PUT' : 'POST';

                // Petición AJAX para guardar el lugar
                $.ajax({
                    url: url,
                    type: method,
                    data: $(this).serialize(),
                    success: function (response) {
                        alert(response.message);
                        location.reload(); // Recargar página para mostrar cambios
                    },
                    error: function (xhr) {
                        // Manejo de errores con mensaje específico
                        let errorMessage = 'Error al guardar';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (xhr.responseText) {
                            errorMessage += ': ' + xhr.responseText;
                        }
                        alert(errorMessage);
                    }
                });
            });

            /**
             * Limpia el formulario cuando se abre el modal para nuevo lugar
             */
            $('[data-bs-target="#modalRegistro"]').click(function() {
                if (!$(this).hasClass('btnEditar')) {
                    $('#formLugar')[0].reset();
                    $('#lugarId').val('');
                }
            });
        });
    </script>
</body>

</html>