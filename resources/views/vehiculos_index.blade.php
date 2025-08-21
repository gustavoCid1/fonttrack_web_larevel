<!DOCTYPE html>
<!--
    FONTTRACK - SISTEMA DE GESTIÓN DE VEHÍCULOS
    
    Sistema completo para la administración del parque vehicular con las siguientes características:
    
    FUNCIONALIDADES PRINCIPALES:
    - CRUD completo de vehículos (Crear, Leer, Actualizar, Eliminar)
    - Búsqueda en tiempo real con AJAX
    - Importación masiva desde archivos Excel
    - Filtrado por lugares/ubicaciones (MODIFICADO: Disponible para todos los usuarios)
    - Sistema de notificaciones en tiempo real
    - Diseño responsivo y moderno
    - Animaciones suaves y efectos visuales
    
    CARACTERÍSTICAS TÉCNICAS:
    - Framework: Laravel Blade
    - Frontend: Bootstrap 5, jQuery, Font Awesome
    - Estilo: CSS Variables, Glassmorphism, Animaciones CSS
    - AJAX: Búsqueda en tiempo real, formularios dinámicos
    - Responsive: Optimizado para móviles y tablets
    - Autenticación: Sistema de roles y permisos
    
    COMPONENTES:
    - Navbar con perfil de usuario y navegación
    - Sistema de búsqueda y filtros avanzados (MODIFICADO: Filtro de lugar accesible para todos)
    - Tabla responsiva con paginación
    - Modales para CRUD operations
    - Sistema de importación Excel con progress bar
    - Notificaciones push
    
    MODIFICACIONES REALIZADAS:
    - Filtro de lugar ahora disponible para todos los usuarios (no solo administradores)
    - Agregado selector de lugar en el formulario de vehículos mejorado
    - Función de filtrado mejorada con persistencia de estado
    - Información adicional sobre el lugar actual del usuario
    
    @package FontTrack
    @module Vehicles Management
    @version 2.1.0 (MODIFICADO)
    @author gustavo angel cid flores 
-->
<html lang="es">

<head>
    <!-- =====================================
         METADATOS Y CONFIGURACIÓN BÁSICA
    ===================================== -->
    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Vehículos - FontTrack</title>
    <link rel="icon" type="image/png" href="{{ asset('img/logo.png') }}">
    
    <!-- BIBLIOTECAS CSS EXTERNAS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- BIBLIOTECAS JAVASCRIPT EXTERNAS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- TOKEN CSRF PARA PETICIONES AJAX -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- PROTECCIÓN DE RUTAS - REDIRECCIÓN SI NO ESTÁ AUTENTICADO -->
    @guest
        <script>
            window.location.href = '/login';
        </script>
    @endguest

    <style>
        /* =====================================
           VARIABLES CSS GLOBALES
           
           Sistema de colores y propiedades reutilizables
           basado en la identidad visual de FontTrack
        ===================================== */
        
        :root {
            /* Colores principales del sistema */
            --primary-orange: #F6B88F;        /* Naranja principal */
            --secondary-orange: #E38B5B;      /* Naranja secundario */
            --dark-brown: #634D3B;            /* Marrón oscuro para texto */
            --light-cream: #FCE8D5;           /* Crema claro para fondos */
            
            /* Colores de estado y acentos */
            --accent-blue: #88C0D0;           /* Azul para información */
            --accent-yellow: #E5A34D;         /* Amarillo para advertencias */
            --accent-red: #D9534F;            /* Rojo para errores/peligro */
            
            /* Sistema de sombras */
            --shadow-light: rgba(0, 0, 0, 0.1);    /* Sombra suave */
            --shadow-medium: rgba(0, 0, 0, 0.15);   /* Sombra media */
            --shadow-heavy: rgba(0, 0, 0, 0.3);     /* Sombra intensa */
        }

        /* Reset universal de box-sizing */
        * {
            box-sizing: border-box;
        }

        /* =====================================
           ANIMACIONES GLOBALES DEL SISTEMA
           
           Colección de animaciones reutilizables
           que aportan fluidez y modernidad
        ===================================== */
        
        /* Animación de flotación suave */
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-8px); }
        }

        /* Animación de aparición desde abajo */
        @keyframes slideInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Animación de aparición con escala */
        @keyframes fadeInScale {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }

        /* Efecto shimmer para loading states */
        @keyframes shimmer {
            0% { left: -100%; }
            100% { left: 100%; }
        }

        /* Animación de rotación para spinners */
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Animación especial para modales (efecto burbuja) */
        @keyframes modalBubbleIn {
            0% { transform: scale(0.3) translateY(100px); opacity: 0; }
            50% { transform: scale(1.05) translateY(-10px); opacity: .8; }
            70% { transform: scale(0.95) translateY(5px); opacity: .9; }
            100% { transform: scale(1) translateY(0); opacity: 1; }
        }

        /* Animación de aparición simple */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Animación de sacudida para alertas */
        @keyframes shake {
            0%, 100% { transform: translateX(0) scale(1.2); }
            25% { transform: translateX(-2px) scale(1.2); }
            75% { transform: translateX(2px) scale(1.2); }
        }

        /* =====================================
           CONFIGURACIÓN DEL BODY Y FONDO
           
           Establecimiento del layout principal
           y efectos de fondo decorativos
        ===================================== */
        
        /* Configuración base del body */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, var(--light-cream) 0%, #f8f1e8 100%);
            color: var(--dark-brown);
            margin: 0;
            padding: 0;
            min-height: 100vh;
            position: relative;
        }

        /* Efectos decorativos de fondo con gradientes radiales */
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

        /* =====================================
           BARRA DE NAVEGACIÓN PRINCIPAL
           
           Header responsive con gradientes,
           efectos glassmorphism y animaciones
        ===================================== */
        
        /* Contenedor principal del navbar */
        .navbar {
            background: linear-gradient(135deg, var(--primary-orange) 0%, var(--secondary-orange) 100%);
            box-shadow: 0 8px 32px var(--shadow-medium);
            padding: 15px 25px;
            border-bottom: none;
            position: sticky;
            top: 0;
            z-index: 1020;                 /* Aparece sobre el contenido al hacer scroll */
            backdrop-filter: blur(10px);   /* Efecto glassmorphism */
        }

        /* Overlay decorativo del navbar */
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

        /* Logo del sistema con efectos hover */
        .navbar .logo {
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.2));
        }

        .navbar .logo:hover {
            transform: scale(1.08) rotate(2deg);
            filter: drop-shadow(0 8px 16px rgba(0, 0, 0, 0.3)) brightness(1.1);
        }

        /* Título principal del navbar */
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

        /* Efecto shimmer en enlaces del navbar */
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

        /* Estados hover de los enlaces */
        .navbar .nav-link:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateY(-2px) scale(1.05);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }

        .navbar .nav-link:hover::before {
            left: 100%;                    /* Activa el efecto shimmer */
        }

        /* Icono del toggle para móviles */
        .navbar-toggler-icon {
            filter: invert(1);
        }

        /* =====================================
           PERFIL DE USUARIO EN NAVBAR
           
           Dropdown interactivo con foto de perfil
           y opciones de usuario
        ===================================== */
        
        /* Contenedor del perfil de usuario */
        .user-profile {
            display: flex;
            align-items: center;
            position: relative;
            cursor: pointer;
            padding: 8px 15px;
            border-radius: 15px;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);   /* Efecto glassmorphism */
        }

        .user-profile:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }

        /* Foto de perfil del usuario */
        .user-profile img {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            margin-right: 12px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            transition: all 0.3s ease;
            object-fit: cover;             /* Mantiene proporciones de la imagen */
        }

        .user-profile:hover img {
            border-color: white;
            transform: scale(1.1);
        }

        /* Nombre del usuario */
        .user-name {
            font-weight: 600;
            color: white;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
        }

        /* Menú dropdown del usuario */
        .user-dropdown {
            position: absolute;
            top: 120%;
            right: 0;
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 60px var(--shadow-heavy);
            padding: 15px 0;
            min-width: 200px;
            display: none;                 /* Oculto por defecto */
            z-index: 1000;
            border: 1px solid rgba(227, 139, 91, 0.2);
        }

        /* Estado visible del dropdown */
        .user-dropdown.show {
            display: block;
            animation: fadeInScale 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }

        /* Enlaces dentro del dropdown */
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

        /* =====================================
           CONTENEDOR PRINCIPAL DE CONTENIDO
           
           Card principal con efectos glassmorphism
           y animaciones de entrada
        ===================================== */
        
        /* Contenedor principal mejorado */
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

        /* Efecto decorativo de fondo flotante */
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

        /* Título principal de la página */
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

        /* Línea decorativa bajo el título */
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

        /* =====================================
           SECCIÓN DE BÚSQUEDA Y FILTROS
           
           Área de controles con efectos glassmorphism
           y elementos interactivos
        ===================================== */
        
        /* Contenedor de búsqueda y filtros */
        .search-filters {
            background: linear-gradient(135deg, var(--primary-orange) 0%, var(--secondary-orange) 100%);
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 15px 40px var(--shadow-medium);
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
            z-index: 1;
        }

        /* Efecto decorativo de fondo */
        .search-filters::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            animation: float 6s ease-in-out infinite;
        }

        /* Campo de búsqueda principal */
        .search-input {
            border: none;
            border-radius: 15px;
            padding: 15px 20px;
            font-size: 16px;
            font-weight: 500;
            box-shadow: inset 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            position: relative;
            z-index: 1;
        }

        /* Estado focus del campo de búsqueda */
        .search-input:focus {
            outline: none;
            box-shadow: 0 0 30px rgba(227, 139, 91, 0.4), inset 0 4px 8px rgba(0, 0, 0, 0.1);
            transform: scale(1.02);
            background: white;
        }

        /* Placeholder del campo de búsqueda */
        .search-input::placeholder {
            color: rgba(99, 77, 59, 0.6);
            font-weight: 400;
        }

        /* =====================================
           MODIFICACIÓN: INFORMACIÓN DE UBICACIÓN ACTUAL
           
           Nuevo componente para mostrar información
           sobre el lugar actual del usuario
        ===================================== */
        
        /* Información del lugar actual del usuario */
        .location-info {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            padding: 15px 20px;
            margin-bottom: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            position: relative;
            z-index: 1;
        }

        .location-info .location-text {
            color: var(--dark-brown);
            font-weight: 600;
            font-size: 0.95rem;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .location-info i {
            color: var(--secondary-orange);
            font-size: 1.1rem;
        }

        /* =====================================
           SISTEMA DE BOTONES MEJORADO
           
           Botones con gradientes, animaciones
           y efectos hover modernos
        ===================================== */
        
        /* Configuración base para todos los botones */
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

        /* Efecto shimmer para todos los botones */
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

        /* Botón primario (naranja) */
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

        /* Botón de información (azul) */
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

        /* Botón de advertencia (amarillo) */
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

        /* Botón de peligro (rojo) */
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

        /* Botón secundario (gris) */
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

        /* Botón de éxito (verde) */
        .btn-success {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
        }

        .btn-success:hover {
            background: linear-gradient(135deg, #20c997, #28a745);
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 8px 25px rgba(40, 167, 69, 0.4);
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

        /* =====================================
           BOTONES DE ACCIÓN EN TABLA
           
           Botones circulares para acciones CRUD
           con animaciones específicas
        ===================================== */
        
        /* Contenedor de botones de acción */
        .action-buttons {
            display: flex;
            gap: 8px;
            justify-content: center;
            align-items: center;
        }

        /* Botones de acción individuales */
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

        /* Efectos hover específicos para cada tipo de botón */
        .btn-info:hover i {
            transform: scale(1.2);         /* Botón de ver */
        }

        .btn-warning:hover i {
            transform: rotate(15deg) scale(1.2);  /* Botón de editar */
        }

        .btn-danger:hover i {
            transform: scale(1.2);
            animation: shake 0.5s ease-in-out;    /* Botón de eliminar */
        }

        /* =====================================
           SISTEMA DE MODALES MEJORADO
           
           Modales con animaciones de entrada
           y efectos glassmorphism
        ===================================== */
        
        /* Animación de entrada para modales */
        .modal.show .modal-dialog {
            animation: modalBubbleIn .6s cubic-bezier(.68, -.55, .265, 1.55);
        }

        /* Contenido principal del modal */
        .modal-content {
            overflow: hidden;
            border-radius: 20px;
            box-shadow: 0 25px 80px var(--shadow-heavy);
            border: none;
            backdrop-filter: blur(10px);
        }

        /* Fondo del modal con efecto blur */
        .modal-backdrop {
            background: linear-gradient(45deg, rgba(0, 0, 0, .5), rgba(0, 0, 0, .7));
            backdrop-filter: blur(5px);
        }

        /* Header del modal */
        .modal-header {
            background: linear-gradient(135deg, var(--primary-orange), var(--secondary-orange));
            color: white;
            border: none;
            padding: 20px 30px;
        }

        /* Título del modal */
        .modal-title {
            font-weight: 600;
            font-size: 1.3rem;
        }

        /* Botón de cerrar modal */
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

        /* Footer del modal */
        .modal-footer {
            background: linear-gradient(135deg, var(--primary-orange), var(--secondary-orange));
            border: none;
            padding: 20px 30px;
        }

        /* =====================================
           TABLA RESPONSIVA MEJORADA
           
           Tabla con efectos hover, gradientes
           y animaciones suaves
        ===================================== */
        
        /* Contenedor responsivo de la tabla */
        .table-responsive {
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 40px var(--shadow-light);
            background: white;
            margin-bottom: 30px;
            position: relative;
            z-index: 1;
        }

        /* Configuración base de la tabla */
        .table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin: 0;
        }

        /* Header de la tabla */
        .table thead {
            background: linear-gradient(135deg, var(--primary-orange) 0%, var(--secondary-orange) 100%);
            color: white;
            position: relative;
        }

        /* Línea decorativa en el header */
        .table thead::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.5), transparent);
        }

        /* Celdas del header */
        .table th {
            padding: 20px 15px;
            font-weight: 600;
            font-size: 1.1rem;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
            border: none;
            position: relative;
        }

        /* Separadores verticales en header */
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

        /* Celdas del cuerpo de la tabla */
        .table td {
            padding: 18px 15px;
            border: none;
            border-bottom: 1px solid rgba(246, 184, 143, 0.1);
            font-weight: 500;
            vertical-align: middle;
        }

        /* Filas del cuerpo con efectos hover */
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

        /* =====================================
           BADGES DE ESTADO
           
           Indicadores visuales para estados
           de vehículos con colores específicos
        ===================================== */
        
        /* Configuración base para badges */
        .badge {
            padding: 8px 12px;
            border-radius: 10px;
            font-size: 0.8em;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Badge para estado activo */
        .badge-activo {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            box-shadow: 0 2px 10px rgba(40, 167, 69, 0.3);
        }

        /* Badge para estado inactivo */
        .badge-inactivo {
            background: linear-gradient(135deg, #6c757d, #5a6268);
            color: white;
            box-shadow: 0 2px 10px rgba(108, 117, 125, 0.3);
        }

        /* Badge para estado de mantenimiento */
        .badge-mantenimiento {
            background: linear-gradient(135deg, #ffc107, #e0a800);
            color: white;
            box-shadow: 0 2px 10px rgba(255, 193, 7, 0.3);
        }

        /* =====================================
           CONTROLES DE FORMULARIO
           
           Inputs y selects con efectos focus
           y estilos coherentes
        ===================================== */
        
        /* Campos de formulario */
        .form-control,
        .form-select {
            border-radius: 12px;
            padding: 15px 20px;
            border: 2px solid rgba(246, 184, 143, 0.3);
            transition: all 0.3s ease;
            font-weight: 500;
        }

        /* Estados focus de los campos */
        .form-control:focus,
        .form-select:focus {
            border-color: var(--secondary-orange);
            box-shadow: 0 0 20px rgba(227, 139, 91, 0.3);
            outline: none;
        }

        /* Etiquetas de formulario */
        .form-label {
            font-weight: 600;
            color: var(--dark-brown);
            margin-bottom: 8px;
        }

        /* =====================================
           SISTEMA DE NOTIFICACIONES
           
           Notificaciones toast con animaciones
           de entrada y salida
        ===================================== */
        
        /* Contenedor de notificaciones */
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 25px;
            border-radius: 10px;
            color: white;
            font-weight: bold;
            z-index: 9999;
            opacity: 0;
            transform: translateX(100%);
            transition: all 0.3s ease;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        }

        /* Estado visible de notificaciones */
        .notification.show {
            opacity: 1;
            transform: translateX(0);
        }

        /* Notificación de éxito */
        .notification.success {
            background: linear-gradient(135deg, #28a745, #20c997);
        }

        /* Notificación de error */
        .notification.error {
            background: linear-gradient(135deg, #dc3545, #c82333);
        }

        /* =====================================
           ELEMENTOS DE LOADING Y PROGRESO
           
           Spinners, barras de progreso y
           estados de carga
        ===================================== */
        
        /* Barra de progreso */
        .progress {
            height: 10px;
            border-radius: 5px;
            overflow: hidden;
            background: rgba(246, 184, 143, 0.2);
        }

        .progress-bar {
            background: linear-gradient(135deg, var(--secondary-orange), var(--primary-orange));
            transition: width 0.3s ease;
        }

        /* Estado de carga para tabla */
        .table-loading {
            position: relative;
        }

        .table-loading::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
        }

        /* Spinner de carga */
        .loading-spinner {
            width: 40px;
            height: 40px;
            border: 4px solid var(--primary-orange);
            border-top: 4px solid var(--secondary-orange);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        /* =====================================
           ESTADO VACÍO
           
           Mensaje cuando no hay datos
           para mostrar en la tabla
        ===================================== */
        
        /* Contenedor de estado vacío */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: rgba(99, 77, 59, 0.6);
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 20px;
            opacity: 0.5;
        }

        .empty-state h4 {
            font-size: 1.5rem;
            margin-bottom: 10px;
            color: var(--dark-brown);
        }

        .empty-state p {
            font-size: 1.1rem;
            margin-bottom: 0;
        }

        /* =====================================
           DISEÑO RESPONSIVO
           
           Adaptaciones para tablets y móviles
           con breakpoints específicos
        ===================================== */
        
        /* Adaptaciones para tablets (768px y menos) */
        @media (max-width: 768px) {
            .container {
                margin: 15px;
                padding: 25px;
                border-radius: 20px;
            }

            h2 {
                font-size: 2rem;
            }

            .search-filters {
                padding: 20px;
            }

            .search-input {
                padding: 12px 15px;
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

            /* MODIFICACIÓN: Ajustes responsivos para nueva funcionalidad */
            .location-info {
                padding: 12px 15px;
                margin-bottom: 10px;
            }

            .location-info .location-text {
                font-size: 0.85rem;
            }
        }
    </style>
</head>

<body>
    <!-- =====================================
         SISTEMA DE NOTIFICACIONES PUSH
         
         Notificación flotante para feedback
         al usuario sobre acciones realizadas
    ===================================== -->
    
    <div class="notification" id="notification">
        <span id="notificationMessage"></span>
    </div>

    <!-- =====================================
         BARRA DE NAVEGACIÓN PRINCIPAL
         
         Header sticky con logo, navegación
         y perfil de usuario
    ===================================== -->
    
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <!-- Logo del sistema con funcionalidad de clic -->
            <img src="{{ asset('img/FontTrack.png') }}" alt="logo" height="70px" width="100px" class="logo"
                onclick="window.location.href='{{ route('materiales.index') }}'">
            
            <!-- Botón toggle para navegación móvil -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <!-- Menú de navegación principal -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <!-- Enlace a página de inicio -->
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('users') }}">
                            <i class="bi bi-house me-2"></i>Inicio
                        </a>
                    </li>
                    
                    <!-- Enlace a gestión de materiales -->
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('materials') }}">
                            <i class="bi bi-box-seam me-2"></i>Materiales
                        </a>
                    </li>
                    
                    <!-- Enlace a reportes -->
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('reportes.index') }}">
                            <i class="bi bi-file-text me-2"></i>Reportes
                        </a>
                    </li>
                    
                    <!-- Enlace a lugares (solo para administradores) -->
                    @if(Auth::user()->tipo_usuario == 1)
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('lugares.index') }}">
                                <i class="bi bi-geo-alt me-2"></i>Lugares
                            </a>
                        </li>
                    @endif
                </ul>

                <!-- Perfil de usuario con dropdown -->
                <div class="user-profile ms-auto">
                    <img src="{{ Auth::user()->foto_usuario_url ?? asset('img/usuario_default.png') }}"
                        alt="Foto de perfil">
                    <span class="user-name">{{ Auth::user()->nombre }}</span>
                    
                    <!-- Menú desplegable del usuario -->
                    <div class="user-dropdown">
                        <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="bi bi-box-arrow-right"></i> Cerrar sesión
                        </a>
                        
                        <!-- Formulario oculto para logout -->
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- =====================================
         CONTENIDO PRINCIPAL DE LA PÁGINA
         
         Contenedor principal con título,
         controles y tabla de vehículos
    ===================================== -->
    
    <div class="container mt-4">
        <!-- Título principal de la página -->
        <h2 class="mb-4"><i class="bi bi-truck me-3"></i>Gestión de Vehículos</h2>

        <!-- =====================================
             SECCIÓN DE BÚSQUEDA Y FILTROS
             
             Controles para buscar, filtrar
             y realizar acciones masivas
        ===================================== -->
        
        <div class="search-filters">
            <!-- MODIFICACIÓN: Información del lugar actual del usuario -->
            <div class="location-info">
                <p class="location-text">
                    <i class="bi bi-geo-alt-fill"></i>
                    <span>Tu lugar de trabajo: <strong>{{ Auth::user()->lugar->nombre ?? 'No asignado' }}</strong></span>
                    <span class="ms-3 text-muted">|</span>
                    <span class="ms-3">Filtra para ver vehículos de otros lugares</span>
                </p>
            </div>

            <div class="row">
                <!-- Campo de búsqueda principal -->
                <div class="col-md-4">
                    <div class="d-flex">
                        <input type="text" id="searchInput" class="form-control search-input me-2"
                            placeholder="Buscar por ECO, placas, marca..." autocomplete="off">
                        <button class="btn btn-outline-light" onclick="performSearch()">
                            <i class="bi bi-search"></i> Buscar
                        </button>
                    </div>
                </div>
                
                <!-- MODIFICACIÓN: Filtro por lugar ahora disponible para todos los usuarios -->
                <div class="col-md-3">
                    <select id="lugarFilter" class="form-select search-input" onchange="filterByLugar()">
                        <option value="">Todos los lugares</option>
                        @foreach($lugares as $lugar)
                            <option value="{{ $lugar->id_lugar }}" 
                                {{ request('lugar') == $lugar->id_lugar ? 'selected' : '' }}
                                {{ Auth::user()->id_lugar == $lugar->id_lugar ? 'data-current="true"' : '' }}>
                                {{ $lugar->nombre }}
                                {{ Auth::user()->id_lugar == $lugar->id_lugar ? ' (Tu lugar)' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Botón para agregar nuevo vehículo -->
                <div class="col-md-2">
                    <button class="btn btn-outline-light w-100" data-bs-toggle="modal" data-bs-target="#modalVehiculo">
                        <i class="fas fa-plus"></i> Nuevo Vehículo
                    </button>
                </div>
                
                <!-- Botón para importar desde Excel -->
                <div class="col-md-3">
                    <button class="btn btn-outline-light w-100" data-bs-toggle="modal"
                        data-bs-target="#modalImportExcel">
                        <i class="fas fa-file-excel"></i> Importar Excel
                    </button>
                </div>
            </div>
        </div>

        <!-- =====================================
             MENSAJES DE ESTADO
             
             Alertas para mostrar resultados
             de operaciones al usuario
        ===================================== -->
        
        <!-- Mensaje de éxito -->
        @if(session('success'))
            <div class="alert alert-success">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            </div>
        @endif

        <!-- Mensaje de error -->
        @if(session('error'))
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-circle-fill me-2"></i>{{ session('error') }}
            </div>
        @endif

        <!-- MODIFICACIÓN: Información adicional sobre el filtro activo -->
        @if(request('lugar'))
            <div class="alert alert-info">
                <i class="bi bi-info-circle-fill me-2"></i>
                Mostrando vehículos del lugar: 
                <strong>
                    @foreach($lugares as $lugar)
                        @if($lugar->id_lugar == request('lugar'))
                            {{ $lugar->nombre }}
                        @endif
                    @endforeach
                </strong>
                <a href="{{ route('vehiculos.index') }}" class="btn btn-sm btn-outline-primary ms-3">
                    <i class="bi bi-x-circle"></i> Ver todos
                </a>
            </div>
        @endif

        <!-- =====================================
             TABLA PRINCIPAL DE VEHÍCULOS
             
             Tabla responsiva con datos de vehículos
             y botones de acción
        ===================================== -->
        
        <div class="table-responsive" id="vehiclesTableContainer">
            <table class="table">
                <!-- Header de la tabla con iconos descriptivos -->
                <thead>
                    <tr>
                        <th><i class="bi bi-tag me-2"></i>ECO</th>
                        <th><i class="bi bi-credit-card me-2"></i>Placas</th>
                        <th><i class="bi bi-truck me-2"></i>Marca/Modelo</th>
                        <th><i class="bi bi-calendar me-2"></i>Año</th>
                        <th><i class="bi bi-speedometer me-2"></i>Kilometraje</th>
                        <th><i class="bi bi-person me-2"></i>Conductor</th>
                        <th><i class="bi bi-check-circle me-2"></i>Estado</th>
                        <th><i class="bi bi-geo-alt me-2"></i>Lugar</th>
                        <th><i class="bi bi-gear me-2"></i>Acciones</th>
                    </tr>
                </thead>
                
                <!-- Cuerpo de la tabla con datos dinámicos -->
                <tbody id="vehiculosTableBody">
                    @if(isset($vehiculos) && $vehiculos->count() > 0)
                        @foreach($vehiculos as $vehiculo)
                            <tr data-id="{{ $vehiculo->id }}"
                                @if(Auth::user()->id_lugar == $vehiculo->id_lugar) 
                                    style="background: rgba(246, 184, 143, 0.05); border-left: 4px solid var(--primary-orange);" 
                                    title="Vehículo de tu lugar de trabajo"
                                @endif>
                                <!-- Número ECO del vehículo -->
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-tag-fill text-primary me-2"></i>
                                        <strong>{{ $vehiculo->eco }}</strong>
                                        @if(Auth::user()->id_lugar == $vehiculo->id_lugar)
                                            <i class="bi bi-house-fill text-warning ms-2" title="Tu lugar"></i>
                                        @endif
                                    </div>
                                </td>
                                
                                <!-- Placas del vehículo -->
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-credit-card-fill text-info me-2"></i>
                                        {{ $vehiculo->placas ?? 'N/A' }}
                                    </div>
                                </td>
                                
                                <!-- Marca y modelo -->
                                <td>
                                    <div>
                                        <strong>{{ $vehiculo->marca ?? 'N/A' }}</strong>
                                        @if($vehiculo->modelo)
                                            <br><small class="text-muted">{{ $vehiculo->modelo }}</small>
                                        @endif
                                    </div>
                                </td>
                                
                                <!-- Año de fabricación -->
                                <td>{{ $vehiculo->anio ?? 'N/A' }}</td>
                                
                                <!-- Kilometraje formateado -->
                                <td>{{ number_format($vehiculo->kilometraje) }} km</td>
                                
                                <!-- Conductor habitual -->
                                <td>{{ $vehiculo->conductor_habitual ?? 'No asignado' }}</td>
                                
                                <!-- Estado con badge colorizado -->
                                <td>
                                    <span class="badge badge-{{ $vehiculo->estatus }}">
                                        {{ ucfirst($vehiculo->estatus) }}
                                    </span>
                                </td>
                                
                                <!-- Lugar asignado con indicador visual -->
                                <td>
                                    <div class="d-flex align-items-center">
                                        {{ $vehiculo->lugar->nombre ?? 'N/A' }}
                                        @if(Auth::user()->id_lugar == $vehiculo->id_lugar)
                                            <i class="bi bi-star-fill text-warning ms-2" title="Tu lugar de trabajo"></i>
                                        @endif
                                    </div>
                                </td>
                                
                                <!-- Botones de acciones CRUD -->
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn btn-info btn-sm" onclick="showVehiculo({{ $vehiculo->id }})"
                                            title="Ver">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-warning btn-sm" onclick="editVehiculo({{ $vehiculo->id }})"
                                            title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-danger btn-sm" onclick="deleteVehiculo({{ $vehiculo->id }})"
                                            title="Eliminar">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <!-- Estado vacío cuando no hay vehículos -->
                        <tr>
                            <td colspan="9" class="empty-state">
                                <i class="bi bi-truck"></i>
                                <h4>No hay vehículos registrados</h4>
                                <p>
                                    @if(request('lugar'))
                                        No hay vehículos en el lugar seleccionado
                                    @else
                                        Aún no tienes vehículos en tu lugar de trabajo
                                    @endif
                                </p>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- =====================================
             PAGINACIÓN DE RESULTADOS
             
             Enlaces de paginación usando
             el componente de Laravel
        ===================================== -->
        
        @if(isset($vehiculos) && method_exists($vehiculos, 'links'))
            <div class="d-flex justify-content-center">
                {{ $vehiculos->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>

    <!-- =====================================
         MODAL PARA CREAR/EDITAR VEHÍCULO
         
         Formulario modal para operaciones
         CRUD de vehículos
    ===================================== -->
    
    <div class="modal fade" id="modalVehiculo" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="formVehiculo">
                    @csrf
                    <input type="hidden" id="vehiculoId" name="vehiculo_id">
                    
                    <!-- Header del modal -->
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalVehiculoTitle">
                            <i class="fas fa-car"></i> Nuevo Vehículo
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    
                    <!-- Cuerpo del modal con campos del formulario -->
                    <div class="modal-body">
                        <div class="row">
                            <!-- MODIFICACIÓN: Campo lugar mejorado con información adicional -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="id_lugar" class="form-label">Lugar *</label>
                                    <select id="id_lugar" name="id_lugar" class="form-select" required>
                                        <option value="">Selecciona un lugar</option>
                                        @foreach($lugares as $lugar)
                                            <option value="{{ $lugar->id_lugar }}"
                                                {{ Auth::user()->id_lugar == $lugar->id_lugar ? 'selected' : '' }}>
                                                {{ $lugar->nombre }}
                                                {{ Auth::user()->id_lugar == $lugar->id_lugar ? ' (Tu lugar actual)' : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="form-text">
                                        <i class="bi bi-info-circle me-1"></i>
                                        Por defecto se selecciona tu lugar de trabajo actual
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Campo ECO -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="eco" class="form-label">No. ECO *</label>
                                    <input type="text" id="eco" name="eco" class="form-control" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Campo placas -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="placas" class="form-label">Placas</label>
                                    <input type="text" id="placas" name="placas" class="form-control">
                                </div>
                            </div>
                            
                            <!-- Campo marca -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="marca" class="form-label">Marca</label>
                                    <input type="text" id="marca" name="marca" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Campo modelo -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="modelo" class="form-label">Modelo</label>
                                    <input type="text" id="modelo" name="modelo" class="form-control">
                                </div>
                            </div>
                            
                            <!-- Campo año -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="anio" class="form-label">Año</label>
                                    <input type="text" id="anio" name="anio" class="form-control" maxlength="4">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Campo kilometraje -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="kilometraje" class="form-label">Kilometraje</label>
                                    <input type="number" id="kilometraje" name="kilometraje" class="form-control"
                                        min="0">
                                </div>
                            </div>
                            
                            <!-- Campo color -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="color" class="form-label">Color</label>
                                    <input type="text" id="color" name="color" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Campo conductor habitual -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="conductor_habitual" class="form-label">Conductor Habitual</label>
                                    <input type="text" id="conductor_habitual" name="conductor_habitual"
                                        class="form-control">
                                </div>
                            </div>
                            
                            <!-- Campo estado -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="estatus" class="form-label">Estado *</label>
                                    <select id="estatus" name="estatus" class="form-select" required>
                                        <option value="activo">Activo</option>
                                        <option value="inactivo">Inactivo</option>
                                        <option value="mantenimiento">En Mantenimiento</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Footer del modal con botones de acción -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                        <button type="submit" id="btnSubmitVehiculo" class="btn btn-success">
                            <i class="fas fa-save"></i> Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- =====================================
         MODAL PARA VER DETALLES DE VEHÍCULO
         
         Modal de solo lectura para mostrar
         información completa del vehículo
    ===================================== -->
    
    <div class="modal fade" id="modalVerVehiculo" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-eye"></i> Detalles del Vehículo
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="vehiculoDetails">
                    <!-- Los detalles se cargarán dinámicamente vía AJAX -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- =====================================
         MODAL PARA IMPORTAR EXCEL
         
         Formulario para importación masiva
         de vehículos desde archivos Excel
    ===================================== -->
    
    <div class="modal fade" id="modalImportExcel" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="formImportExcel" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-file-excel"></i> Importar Vehículos desde Excel
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Campo para seleccionar archivo Excel -->
                        <div class="mb-3">
                            <label for="excel_file" class="form-label">Seleccionar archivo Excel</label>
                            <input type="file" id="excel_file" name="excel_file" class="form-control"
                                accept=".xlsx,.xls" required>
                            <div class="form-text">
                                <i class="bi bi-info-circle me-1"></i>
                                Formato soportado: .xlsx, .xls
                            </div>
                        </div>

                <!-- CORREGIDO: Campo para seleccionar lugar mejorado -->
                        <div class="mb-3">
                            <label for="lugar_import" class="form-label">Lugar para los vehículos *</label>
                            <select id="lugar_import" name="lugar_import" class="form-select" required>
                                <option value="">⚠️ Selecciona un lugar (OBLIGATORIO)</option>
                                @foreach($lugares as $lugar)
                                    <option value="{{ $lugar->id_lugar }}"
                                        {{ Auth::user()->id_lugar == $lugar->id_lugar ? 'selected' : '' }}
                                        data-lugar-nombre="{{ $lugar->nombre }}">
                                        {{ $lugar->nombre }}
                                        {{ Auth::user()->id_lugar == $lugar->id_lugar ? ' (Tu lugar actual - RECOMENDADO)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">
                                <i class="bi bi-exclamation-triangle text-warning me-1"></i>
                                <strong>IMPORTANTE:</strong> Todos los vehículos del archivo se asignarán al lugar seleccionado
                            </div>
                            
                            <!-- NUEVO: Confirmación visual del lugar seleccionado -->
                            <div id="lugarConfirmacion" class="mt-2" style="display: none;">
                                <div class="alert alert-info py-2">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Los vehículos se importarán a: <strong id="lugarNombreConfirmacion"></strong>
                                </div>
                            </div>
                        </div>

                        <!-- CORREGIDO: Información sobre el formato esperado -->
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle-fill me-2"></i>
                            <strong>Formato esperado del Excel:</strong>
                            <ul class="mb-2 mt-2">
                                <li><strong>ECO</strong> (obligatorio)</li>
                                <li>Placas</li>
                                <li>Marca</li>
                                <li>Modelo</li>
                                <li>Año</li>
                                <li>Kilometraje</li>
                                <li>Color</li>
                                <li>Conductor Habitual</li>
                                <li>Estado (activo/inactivo/mantenimiento)</li>
                            </ul>
                            <div class="alert alert-warning py-2 mb-0">
                                <i class="bi bi-exclamation-triangle me-1"></i>
                                <strong>NOTA:</strong> El lugar se asigna desde el selector de arriba, no desde el Excel
                            </div>
                        </div>

                        <!-- Barra de progreso para importación -->
                        <div id="import-progress" class="mt-3" style="display: none;">
                            <label class="form-label">Progreso de importación:</label>
                            <div class="progress">
                                <div id="progress-bar" class="progress-bar" role="progressbar" style="width: 0%"></div>
                            </div>
                            <small id="progress-text" class="form-text">Preparando importación...</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                        <button type="submit" id="btnImportExcel" class="btn btn-success">
                            <i class="fas fa-upload"></i> Importar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- =====================================
         FUNCIONALIDAD JAVASCRIPT/JQUERY
         
         Lógica cliente para AJAX, eventos,
         y interacciones del usuario
    ===================================== -->
    
    <script>
        /**
         * INICIALIZACIÓN DEL SISTEMA DE VEHÍCULOS
         * 
         * Configura todos los event listeners, AJAX handlers
         * y funcionalidades interactivas de la página
         * 
         * MODIFICACIONES INCLUIDAS:
         * - Filtro de lugar accesible para todos los usuarios
         * - Persistencia de filtro en URL
         * - Indicadores visuales mejorados
         */
        $(document).ready(function () {
            console.log('🚗 Sistema de vehículos inicializado - Búsqueda AJAX habilitada');
            console.log('✨ MODIFICACIÓN: Filtro de lugar disponible para todos los usuarios');

            // =====================================
            // GESTIÓN DEL MENÚ DE USUARIO
            // =====================================
            
            // Manejar el menú desplegable del perfil de usuario
            $('.user-profile').click(function (e) {
                e.stopPropagation();
                $('.user-dropdown').toggleClass('show');
            });

            // Cerrar el menú desplegable al hacer clic fuera
            $(document).click(function () {
                $('.user-dropdown').removeClass('show');
            });

            // =====================================
            // CONFIGURACIÓN DE AJAX
            // =====================================
            
            // Configurar token CSRF para todas las peticiones AJAX
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // =====================================
            // FUNCIONES UTILITARIAS
            // =====================================
            
            /**
             * Muestra una notificación toast al usuario
             * @param {string} message - Mensaje a mostrar
             * @param {string} type - Tipo de notificación (success/error)
             */
            function showNotification(message, type = 'success') {
                const notification = $('#notification');
                $('#notificationMessage').text(message);
                notification.removeClass('success error').addClass(type + ' show');
                setTimeout(() => notification.removeClass('show'), 4000);
            }

            /**
             * Maneja errores de peticiones AJAX
             * @param {object} xhr - Objeto XMLHttpRequest con el error
             */
            function handleAjaxError(xhr) {
                const res = xhr.responseJSON;
                if (res?.errors) {
                    showNotification('Errores: ' + Object.values(res.errors).flat().join(', '), 'error');
                } else if (res?.message) {
                    showNotification('Error: ' + res.message, 'error');
                } else {
                    showNotification('Error: ' + xhr.responseText, 'error');
                }
            }

            // =====================================
            // SISTEMA DE BÚSQUEDA AJAX AVANZADO
            // =====================================
            
            let searchTimeout;
            
            /**
             * Event listener para búsqueda en tiempo real
             * Implementa debouncing para optimizar rendimiento
             */
            $('#searchInput').on('input', function() {
                clearTimeout(searchTimeout);
                const query = $(this).val().trim();
                
                // Si el campo está vacío, recargar la página para mostrar todos los vehículos
                if (query.length === 0) {
                    searchTimeout = setTimeout(() => {
                        location.reload();
                    }, 300);
                    return;
                }
                
                // Buscar después de 500ms de inactividad (debouncing)
                if (query.length >= 2) {
                    searchTimeout = setTimeout(() => {
                        performAjaxSearch(query);
                    }, 500);
                }
            });

            /**
             * MODIFICACIÓN: Función principal de búsqueda AJAX mejorada
             * Ahora incluye el filtro de lugar en las búsquedas
             * @param {string} query - Término de búsqueda
             */
            function performAjaxSearch(query) {
                console.log('🔍 Iniciando búsqueda AJAX:', query);
                
                // Mostrar estado de carga en la tabla
                $('#vehiclesTableContainer').addClass('table-loading');
                $('#vehiculosTableBody').html(`
                    <tr>
                        <td colspan="9" class="text-center">
                            <div class="loading-spinner"></div>
                            <p class="mt-2">Buscando vehículos...</p>
                        </td>
                    </tr>
                `);

                // MODIFICACIÓN: Incluir filtro de lugar en la búsqueda AJAX
                const lugarFiltro = $('#lugarFilter').val();
                const searchData = { q: query };
                if (lugarFiltro) {
                    searchData.lugar = lugarFiltro;
                }

                // Realizar petición AJAX de búsqueda
                $.ajax({
                    url: '/vehiculos/search',
                    method: 'GET',
                    data: searchData,
                    success: function(vehiculos) {
                        console.log('✅ Búsqueda exitosa:', vehiculos);
                        updateVehiclesTable(vehiculos);
                    },
                    error: function(xhr) {
                        console.error('❌ Error en búsqueda:', xhr);
                        $('#vehiculosTableBody').html(`
                            <tr>
                                <td colspan="9" class="text-center text-danger">
                                    <i class="bi bi-exclamation-triangle" style="font-size: 2rem;"></i>
                                    <br>Error al buscar vehículos
                                </td>
                            </tr>
                        `);
                        handleAjaxError(xhr);
                    },
                    complete: function() {
                        $('#vehiclesTableContainer').removeClass('table-loading');
                    }
                });
            }

            /**
             * MODIFICACIÓN: Actualiza la tabla con los resultados de búsqueda
             * Incluye indicadores visuales para vehículos del lugar del usuario
             * @param {array} vehiculos - Array de vehículos encontrados
             */
            function updateVehiclesTable(vehiculos) {
                let html = '';
                const userLugar = {{ Auth::user()->id_lugar ?? 'null' }};
                
                if (vehiculos && vehiculos.length > 0) {
                    vehiculos.forEach(vehiculo => {
                        // MODIFICACIÓN: Añadir estilos especiales para vehículos del lugar del usuario
                        const isUserLocation = userLugar && vehiculo.id_lugar == userLugar;
                        const rowStyle = isUserLocation ? 
                            'style="background: rgba(246, 184, 143, 0.05); border-left: 4px solid var(--primary-orange);" title="Vehículo de tu lugar de trabajo"' : '';

                        html += `
                            <tr data-id="${vehiculo.id}" ${rowStyle}>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-tag-fill text-primary me-2"></i>
                                        <strong>${vehiculo.eco}</strong>
                                        ${isUserLocation ? '<i class="bi bi-house-fill text-warning ms-2" title="Tu lugar"></i>' : ''}
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-credit-card-fill text-info me-2"></i>
                                        ${vehiculo.placas || 'N/A'}
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <strong>${vehiculo.marca || 'N/A'}</strong>
                                        ${vehiculo.modelo ? '<br><small class="text-muted">' + vehiculo.modelo + '</small>' : ''}
                                    </div>
                                </td>
                                <td>${vehiculo.anio || 'N/A'}</td>
                                <td>${vehiculo.kilometraje ? Number(vehiculo.kilometraje).toLocaleString() + ' km' : '0 km'}</td>
                                <td>${vehiculo.conductor_habitual || 'No asignado'}</td>
                                <td>
                                    <span class="badge badge-${vehiculo.estatus}">
                                        ${vehiculo.estatus.charAt(0).toUpperCase() + vehiculo.estatus.slice(1)}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        ${vehiculo.lugar ? vehiculo.lugar.nombre : 'N/A'}
                                        ${isUserLocation ? '<i class="bi bi-star-fill text-warning ms-2" title="Tu lugar de trabajo"></i>' : ''}
                                    </div>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn btn-info btn-sm" onclick="showVehiculo(${vehiculo.id})" title="Ver">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-warning btn-sm" onclick="editVehiculo(${vehiculo.id})" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-danger btn-sm" onclick="deleteVehiculo(${vehiculo.id})" title="Eliminar">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        `;
                    });
                } else {
                    // Mostrar estado vacío cuando no hay resultados
                    html = `
                        <tr>
                            <td colspan="9" class="empty-state">
                                <i class="bi bi-search"></i>
                                <h4>No se encontraron resultados</h4>
                                <p>No hay vehículos que coincidan con tu búsqueda</p>
                            </td>
                        </tr>
                    `;
                }
                
                $('#vehiculosTableBody').html(html);
            }

            // =====================================
            // GESTIÓN DE FORMULARIOS
            // =====================================
            
            /**
             * Handler para el formulario de vehículos (crear/editar)
             */
            $('#formVehiculo').submit(function (e) {
                e.preventDefault();

                const vehiculoId = $('#vehiculoId').val();
                const isEditing = vehiculoId && vehiculoId !== '';
                const url = isEditing ? `/vehiculos/${vehiculoId}` : '/vehiculos';
                const method = isEditing ? 'PUT' : 'POST';

                const formData = $(this).serialize();

                // Deshabilitar botón durante el envío
                $('#btnSubmitVehiculo').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');

                $.ajax({
                    url: url,
                    method: method,
                    data: formData,
                    success: function (response) {
                        if (response.success) {
                            $('#modalVehiculo').modal('hide');
                            showNotification(response.message);
                            setTimeout(() => location.reload(), 1500);
                        } else {
                            showNotification('Error: ' + (response.message || 'Error desconocido'), 'error');
                        }
                    },
                    error: function (xhr) {
                        handleAjaxError(xhr);
                    },
                    complete: function () {
                        $('#btnSubmitVehiculo').prop('disabled', false).html('<i class="fas fa-save"></i> Guardar');
                    }
                });
            });

            /**
             * CORREGIDO: Handler para el formulario de importación Excel
             * Ahora envía correctamente el campo lugar_import como espera el backend
             */
            $('#formImportExcel').submit(function (e) {
                e.preventDefault();

                // CORRECCIÓN: Validar que se haya seleccionado un lugar
                const lugarSeleccionado = $('#lugar_import').val();
                const archivoSeleccionado = $('#excel_file')[0].files[0];

                if (!lugarSeleccionado) {
                    showNotification('Por favor selecciona un lugar para los vehículos', 'error');
                    $('#lugar_import').focus();
                    return;
                }

                if (!archivoSeleccionado) {
                    showNotification('Por favor selecciona un archivo Excel', 'error');
                    $('#excel_file').focus();
                    return;
                }

                // CORRECCIÓN: Usar FormData del formulario para respetar los nombres de campos
                const formData = new FormData(this);

                // CORRECCIÓN: Verificar que los datos se están enviando correctamente
                console.log('📁 Archivo seleccionado:', archivoSeleccionado.name);
                console.log('🏢 Lugar seleccionado (lugar_import):', lugarSeleccionado);
                console.log('📋 FormData contiene:');
                for (let [key, value] of formData.entries()) {
                    console.log(`  ${key}:`, typeof value === 'object' ? value.name : value);
                }

                // Mostrar confirmación del lugar seleccionado
                const nombreLugar = $('#lugar_import option:selected').text();
                $('#progress-text').text(`Validando datos para: ${nombreLugar}`);

                $('#btnImportExcel').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Importando...');
                $('#import-progress').show();

                // Simular progreso de importación
                let progress = 0;
                const progressInterval = setInterval(() => {
                    progress += 10;
                    $('#progress-bar').css('width', progress + '%');
                    if (progress < 30) {
                        $('#progress-text').text('Validando archivo Excel...');
                    } else if (progress < 50) {
                        $('#progress-text').text('Leyendo datos del archivo...');
                    } else if (progress < 70) {
                        $('#progress-text').text(`Asignando vehículos al lugar: ${nombreLugar}`);
                    } else if (progress < 90) {
                        $('#progress-text').text('Guardando en la base de datos...');
                    } else {
                        $('#progress-text').text('Finalizando importación...');
                    }
                }, 200);

                $.ajax({
                    url: '/vehiculos/import-excel',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        clearInterval(progressInterval);
                        $('#progress-bar').css('width', '100%');
                        
                        console.log('✅ Respuesta del servidor:', response);

                        if (response.success) {
                            $('#progress-text').text(`¡Importación completada en ${nombreLugar}!`);
                            setTimeout(() => {
                                $('#modalImportExcel').modal('hide');
                                showNotification(`Vehículos importados exitosamente al lugar: ${nombreLugar}`, 'success');
                                
                                // CORRECCIÓN: Redirigir con filtro del lugar importado
                                const currentUrl = new URL(window.location);
                                currentUrl.searchParams.set('lugar', lugarSeleccionado);
                                window.location.href = currentUrl.toString();
                            }, 1500);
                        } else {
                            $('#progress-text').text('Error en la importación');
                            showNotification('Error: ' + (response.message || 'Error en la importación'), 'error');
                        }
                    },
                    error: function (xhr) {
                        clearInterval(progressInterval);
                        $('#progress-text').text('Error al procesar el archivo');
                        
                        console.error('❌ Error en importación:', xhr);
                        console.error('📄 Respuesta del servidor:', xhr.responseText);
                        console.error('📋 Status HTTP:', xhr.status);
                        
                        // CORRECCIÓN: Mostrar error más específico
                        if (xhr.status === 422) {
                            const response = xhr.responseJSON;
                            if (response?.errors) {
                                const errorMessages = Object.values(response.errors).flat();
                                showNotification('Errores de validación: ' + errorMessages.join(', '), 'error');
                            } else if (response?.message) {
                                showNotification('Error: ' + response.message, 'error');
                            } else {
                                showNotification('Error de validación en los datos del archivo', 'error');
                            }
                        } else if (xhr.status === 413) {
                            showNotification('El archivo es demasiado grande. Máximo 10MB permitidos.', 'error');
                        } else if (xhr.status === 500) {
                            showNotification('Error interno del servidor. Verifica el formato del archivo Excel.', 'error');
                        } else {
                            showNotification('Error al procesar la importación: ' + (xhr.responseText || 'Error desconocido'), 'error');
                        }
                    },
                    complete: function () {
                        $('#btnImportExcel').prop('disabled', false).html('<i class="fas fa-upload"></i> Importar');
                        setTimeout(() => {
                            if ($('#progress-bar').css('width') !== '100%') {
                                $('#import-progress').hide();
                                $('#progress-bar').css('width', '0%');
                                $('#progress-text').text('Preparando importación...');
                            }
                        }, 3000);
                    }
                });
            });

            // =====================================
            // CORRECCIÓN: VALIDACIÓN DEL MODAL DE IMPORTACIÓN
            // =====================================
            
            /**
             * NUEVO: Mostrar confirmación visual del lugar seleccionado
             */
            $('#lugar_import').change(function() {
                const lugarId = $(this).val();
                const lugarNombre = $(this).find('option:selected').text();
                
                if (lugarId) {
                    $('#lugarNombreConfirmacion').text(lugarNombre);
                    $('#lugarConfirmacion').show();
                    console.log('🏢 Lugar seleccionado para importación:', lugarNombre);
                } else {
                    $('#lugarConfirmacion').hide();
                }
            });

            /**
             * NUEVO: Validación en tiempo real del archivo Excel
             */
            $('#excel_file').change(function() {
                const file = this.files[0];
                if (file) {
                    const fileName = file.name;
                    const fileSize = (file.size / 1024 / 1024).toFixed(2); // MB
                    const validExtensions = ['.xlsx', '.xls'];
                    const fileExtension = fileName.substring(fileName.lastIndexOf('.')).toLowerCase();
                    
                    console.log('📁 Archivo seleccionado:', {
                        nombre: fileName,
                        tamaño: fileSize + ' MB',
                        extensión: fileExtension
                    });
                    
                    if (!validExtensions.includes(fileExtension)) {
                        showNotification('Formato de archivo no válido. Solo se permiten archivos .xlsx y .xls', 'error');
                        $(this).val('');
                        return;
                    }
                    
                    if (file.size > 10 * 1024 * 1024) { // 10MB máximo
                        showNotification('El archivo es demasiado grande. Máximo 10MB permitidos.', 'error');
                        $(this).val('');
                        return;
                    }
                    
                    showNotification(`Archivo "${fileName}" (${fileSize} MB) listo para importar`, 'success');
                }
            });
            
            // Limpiar formulario al cerrar modal de vehículo
            $('#modalVehiculo').on('hidden.bs.modal', function () {
                $('#formVehiculo')[0].reset();
                $('#vehiculoId').val('');
                $('#modalVehiculoTitle').html('<i class="fas fa-car"></i> Nuevo Vehículo');
                $('#btnSubmitVehiculo').html('<i class="fas fa-save"></i> Guardar');
                
                // MODIFICACIÓN: Resetear al lugar del usuario por defecto
                $('#id_lugar').val({{ Auth::user()->id_lugar ?? '""' }});
            });

            // Limpiar formulario al cerrar modal de importación
            $('#modalImportExcel').on('hidden.bs.modal', function () {
                $('#formImportExcel')[0].reset();
                $('#import-progress').hide();
                $('#progress-bar').css('width', '0%');
                
                // MODIFICACIÓN: Resetear al lugar del usuario por defecto
                $('#lugar_import').val({{ Auth::user()->id_lugar ?? '""' }});
            });

            // =====================================
            // EVENTOS DE TECLADO
            // =====================================
            
            // Búsqueda al presionar Enter
            $('#searchInput').keypress(function (e) {
                if (e.which == 13) {
                    e.preventDefault();
                    const query = $(this).val().trim();
                    if (query.length >= 2) {
                        performAjaxSearch(query);
                    }
                }
            });

            // Exponer función para uso global
            window.showNotification = showNotification;
            
            // MODIFICACIÓN: Exponer función de búsqueda AJAX para uso global
            window.performAjaxSearch = performAjaxSearch;
        });

        // =====================================
        // FUNCIONES GLOBALES PARA BOTONES
        // =====================================
        
        /**
         * Función de búsqueda activada por botón
         */
        function performSearch() {
            const query = $('#searchInput').val().trim();
            if (query.length >= 2) {
                // Activar la búsqueda AJAX usando el mismo evento input
                $('#searchInput').trigger('input');
            } else if (query.length === 0) {
                location.reload();
            } else {
                showNotification('Ingresa al menos 2 caracteres para buscar', 'error');
            }
        }

        /**
         * Muestra los detalles de un vehículo en modal
         * @param {number} id - ID del vehículo
         */
        function showVehiculo(id) {
            $.get(`/vehiculos/${id}`, function (response) {
                if (response.data) {
                    const vehiculo = response.data;
                    const userLugar = {{ Auth::user()->id_lugar ?? 'null' }};
                    const isUserLocation = userLugar && vehiculo.id_lugar == userLugar;
                    
                    const details = `
                        <div class="row">
                            <div class="col-12">
                                <div class="card mb-3" ${isUserLocation ? 'style="border-left: 4px solid var(--primary-orange);"' : ''}>
                                    <div class="card-body">
                                        ${isUserLocation ? `
                                        <div class="alert alert-info mb-3">
                                            <i class="bi bi-star-fill text-warning me-2"></i>
                                            <strong>Este vehículo pertenece a tu lugar de trabajo</strong>
                                        </div>
                                        ` : ''}
                                        <div class="row mb-2">
                                            <div class="col-6">
                                                <i class="bi bi-tag text-primary me-2"></i>
                                                <strong>ECO:</strong>
                                            </div>
                                            <div class="col-6">${vehiculo.eco}</div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-6">
                                                <i class="bi bi-credit-card text-info me-2"></i>
                                                <strong>Placas:</strong>
                                            </div>
                                            <div class="col-6">${vehiculo.placas || 'N/A'}</div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-6">
                                                <i class="bi bi-truck text-success me-2"></i>
                                                <strong>Marca:</strong>
                                            </div>
                                            <div class="col-6">${vehiculo.marca || 'N/A'}</div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-6">
                                                <i class="bi bi-gear text-warning me-2"></i>
                                                <strong>Modelo:</strong>
                                            </div>
                                            <div class="col-6">${vehiculo.modelo || 'N/A'}</div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-6">
                                                <i class="bi bi-calendar text-danger me-2"></i>
                                                <strong>Año:</strong>
                                            </div>
                                            <div class="col-6">${vehiculo.anio || 'N/A'}</div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-6">
                                                <i class="bi bi-speedometer text-primary me-2"></i>
                                                <strong>Kilometraje:</strong>
                                            </div>
                                            <div class="col-6">${vehiculo.kilometraje ? Number(vehiculo.kilometraje).toLocaleString() + ' km' : 'N/A'}</div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-6">
                                                <i class="bi bi-palette text-info me-2"></i>
                                                <strong>Color:</strong>
                                            </div>
                                            <div class="col-6">${vehiculo.color || 'N/A'}</div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-6">
                                                <i class="bi bi-person text-success me-2"></i>
                                                <strong>Conductor:</strong>
                                            </div>
                                            <div class="col-6">${vehiculo.conductor_habitual || 'No asignado'}</div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-6">
                                                <i class="bi bi-check-circle text-warning me-2"></i>
                                                <strong>Estado:</strong>
                                            </div>
                                            <div class="col-6"><span class="badge badge-${vehiculo.estatus}">${vehiculo.estatus.charAt(0).toUpperCase() + vehiculo.estatus.slice(1)}</span></div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-6">
                                                <i class="bi bi-geo-alt text-danger me-2"></i>
                                                <strong>Lugar:</strong>
                                            </div>
                                            <div class="col-6">
                                                ${vehiculo.lugar ? vehiculo.lugar.nombre : 'N/A'}
                                                ${isUserLocation ? '<i class="bi bi-star-fill text-warning ms-2" title="Tu lugar de trabajo"></i>' : ''}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    $('#vehiculoDetails').html(details);
                    $('#modalVerVehiculo').modal('show');
                }
            }).fail(function (xhr) {
                showNotification('Error al cargar los detalles del vehículo', 'error');
            });
        }

        /**
         * Carga los datos de un vehículo para edición
         * @param {number} id - ID del vehículo
         */
        function editVehiculo(id) {
            $.get(`/vehiculos/${id}/edit`, function (response) {
                if (response.data) {
                    const vehiculo = response.data;

                    // Poblar el formulario con los datos del vehículo
                    $('#vehiculoId').val(vehiculo.id);
                    $('#id_lugar').val(vehiculo.id_lugar);
                    $('#eco').val(vehiculo.eco);
                    $('#placas').val(vehiculo.placas);
                    $('#marca').val(vehiculo.marca);
                    $('#modelo').val(vehiculo.modelo);
                    $('#anio').val(vehiculo.anio);
                    $('#kilometraje').val(vehiculo.kilometraje);
                    $('#color').val(vehiculo.color);
                    $('#conductor_habitual').val(vehiculo.conductor_habitual);
                    $('#estatus').val(vehiculo.estatus);

                    // Cambiar título del modal
                    $('#modalVehiculoTitle').html('<i class="fas fa-edit"></i> Editar Vehículo');
                    $('#modalVehiculo').modal('show');
                }
            }).fail(function (xhr) {
                showNotification('Error al cargar los datos del vehículo', 'error');
            });
        }

        /**
         * Elimina un vehículo con confirmación
         * @param {number} id - ID del vehículo
         */
        function deleteVehiculo(id) {
            if (confirm('¿Estás seguro de que deseas eliminar este vehículo?')) {
                $.ajax({
                    url: `/vehiculos/${id}`,
                    method: 'DELETE',
                    success: function (response) {
                        if (response.success) {
                            showNotification(response.message);
                            setTimeout(() => location.reload(), 1500);
                        } else {
                            showNotification('Error: ' + (response.message || 'Error desconocido'), 'error');
                        }
                    },
                    error: function (xhr) {
                        showNotification('Error al eliminar el vehículo', 'error');
                    }
                });
            }
        }

        /**
         * MODIFICACIÓN: Filtra vehículos por lugar con persistencia en URL
         * Ahora todos los usuarios pueden filtrar por lugar
         */
        function filterByLugar() {
            const lugar = $('#lugarFilter').val();
            const currentUrl = new URL(window.location);
            
            // Mantener otros parámetros de consulta si existen
            if (lugar) {
                currentUrl.searchParams.set('lugar', lugar);
                console.log('🏢 Filtrando por lugar:', lugar);
            } else {
                currentUrl.searchParams.delete('lugar');
                console.log('🌍 Mostrando todos los lugares');
            }
            
            // MODIFICACIÓN: Mantener parámetros de búsqueda si existen
            const currentSearch = $('#searchInput').val();
            if (currentSearch.trim()) {
                currentUrl.searchParams.set('search', currentSearch);
            }
            
            // Redirigir con los nuevos parámetros
            window.location.href = currentUrl.toString();
        }

        /**
         * MODIFICACIÓN: Función para resetear filtros
         * Limpia todos los filtros y vuelve a la vista predeterminada
         */
        function resetFilters() {
            $('#searchInput').val('');
            $('#lugarFilter').val('');
            const baseUrl = window.location.pathname;
            window.location.href = baseUrl;
        }

        /**
         * MODIFICACIÓN: Función para aplicar filtro rápido del lugar del usuario
         * Filtra rápidamente por el lugar de trabajo del usuario actual
         */
        function filterByUserLocation() {
            const userLugar = {{ Auth::user()->id_lugar ?? 'null' }};
            if (userLugar) {
                $('#lugarFilter').val(userLugar);
                filterByLugar();
            } else {
                showNotification('No tienes un lugar de trabajo asignado', 'error');
            }
        }

        // =====================================
        // MODIFICACIÓN: FUNCIONALIDADES ADICIONALES
        // =====================================

        /**
         * MODIFICACIÓN: Destacar vehículos del lugar del usuario
         * Añade efectos visuales especiales para vehículos del lugar del usuario
         */
        function highlightUserVehicles() {
            const userLugar = {{ Auth::user()->id_lugar ?? 'null' }};
            if (userLugar) {
                $(`tr[data-lugar="${userLugar}"]`).addClass('user-location-highlight');
            }
        }

        /**
         * MODIFICACIÓN: Contador de vehículos por lugar
         * Muestra estadísticas básicas de vehículos
         */
        function showVehicleStats() {
            const totalVehicles = $('#vehiculosTableBody tr').length;
            const userLugar = {{ Auth::user()->id_lugar ?? 'null' }};
            let userLocationCount = 0;
            
            if (userLugar) {
                userLocationCount = $(`tr[data-lugar="${userLugar}"]`).length;
            }
            
            console.log(`📊 Total de vehículos mostrados: ${totalVehicles}`);
            if (userLocationCount > 0) {
                console.log(`🏠 Vehículos de tu lugar: ${userLocationCount}`);
            }
        }

        // =====================================
        // MODIFICACIÓN: INICIALIZACIÓN ADICIONAL
        // =====================================

        /**
         * MODIFICACIÓN: Inicializar funcionalidades adicionales al cargar la página
         */
        $(document).ready(function() {
            // Destacar vehículos del lugar del usuario
            highlightUserVehicles();
            
            // Mostrar estadísticas básicas
            showVehicleStats();
            
            // MODIFICACIÓN: Preseleccionar lugar del usuario en filtros si no hay otro filtro activo
            const currentFilter = new URLSearchParams(window.location.search).get('lugar');
            if (!currentFilter) {
                const userLugar = {{ Auth::user()->id_lugar ?? 'null' }};
                if (userLugar) {
                    console.log('🏠 Lugar del usuario detectado:', userLugar);
                    // No autoseleccionar, pero marcar visualmente en el dropdown
                    $(`#lugarFilter option[value="${userLugar}"]`).addClass('text-primary fw-bold');
                }
            }
            
            // MODIFICACIÓN: Añadir tooltips informativos
            $('[title]').each(function() {
                $(this).attr('data-bs-toggle', 'tooltip');
            });
            
            // Inicializar tooltips de Bootstrap si están disponibles
            if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });
            }
        });

        // =====================================
        // MODIFICACIÓN: EVENTOS ADICIONALES
        // =====================================

        /**
         * MODIFICACIÓN: Event listener para teclas de acceso rápido
         */
        $(document).keydown(function(e) {
            // Ctrl + F: Enfocar campo de búsqueda
            if (e.ctrlKey && e.keyCode === 70) {
                e.preventDefault();
                $('#searchInput').focus();
                return false;
            }
            
            // Ctrl + L: Enfocar filtro de lugar
            if (e.ctrlKey && e.keyCode === 76) {
                e.preventDefault();
                $('#lugarFilter').focus();
                return false;
            }
            
            // Ctrl + R: Resetear filtros
            if (e.ctrlKey && e.keyCode === 82) {
                e.preventDefault();
                resetFilters();
                return false;
            }
            
            // Ctrl + N: Nuevo vehículo
            if (e.ctrlKey && e.keyCode === 78) {
                e.preventDefault();
                $('#modalVehiculo').modal('show');
                return false;
            }
        });

        /**
         * MODIFICACIÓN: Exponer funciones adicionales para uso global
         */
        window.resetFilters = resetFilters;
        window.filterByUserLocation = filterByUserLocation;
        window.highlightUserVehicles = highlightUserVehicles;
    </script>

    <!-- =====================================
         MODIFICACIÓN: ESTILOS CSS ADICIONALES
         
         Estilos específicos para las nuevas
         funcionalidades implementadas
    ===================================== -->
    
    <style>
        /* MODIFICACIÓN: Estilos para destacar vehículos del lugar del usuario */
        .user-location-highlight {
            background: linear-gradient(135deg, rgba(246, 184, 143, 0.08), rgba(227, 139, 91, 0.04)) !important;
            border-left: 4px solid var(--primary-orange) !important;
            position: relative;
        }

        .user-location-highlight::before {
            content: '🏠';
            position: absolute;
            left: -2px;
            top: 50%;
            transform: translateY(-50%);
            background: var(--primary-orange);
            color: white;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            z-index: 10;
        }

        /* MODIFICACIÓN: Mejorar selectors de lugar */
        #lugarFilter option[data-current="true"] {
            background: linear-gradient(135deg, var(--primary-orange), var(--secondary-orange));
            color: white;
            font-weight: bold;
        }

        /* MODIFICACIÓN: Indicador de filtro activo */
        .filter-active {
            box-shadow: 0 0 20px rgba(227, 139, 91, 0.5) !important;
            border: 2px solid var(--secondary-orange) !important;
        }

        /* MODIFICACIÓN: Estilo para información de lugar actual */
        .current-location-badge {
            background: linear-gradient(135deg, var(--primary-orange), var(--secondary-orange));
            color: white;
            padding: 4px 8px;
            border-radius: 15px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-left: 8px;
        }

        /* MODIFICACIÓN: Animación especial para elementos destacados */
        .highlight-pulse {
            animation: pulse-highlight 2s ease-in-out infinite;
        }

        @keyframes pulse-highlight {
            0% { box-shadow: 0 0 0 0 rgba(246, 184, 143, 0.7); }
            70% { box-shadow: 0 0 0 10px rgba(246, 184, 143, 0); }
            100% { box-shadow: 0 0 0 0 rgba(246, 184, 143, 0); }
        }

        /* MODIFICACIÓN: Tooltip personalizado */
        .custom-tooltip {
            background: linear-gradient(135deg, var(--dark-brown), #4a3a2b);
            color: white;
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 0.8rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        /* MODIFICACIÓN: Indicadores de acceso rápido */
        .keyboard-shortcut {
            font-size: 0.7rem;
            background: rgba(99, 77, 59, 0.1);
            padding: 2px 6px;
            border-radius: 4px;
            margin-left: 8px;
            color: var(--dark-brown);
        }

        /* CORRECCIÓN: Estilos para validación de importación */
        #lugarConfirmacion {
            animation: fadeIn 0.3s ease-in-out;
        }

        #lugarConfirmacion .alert {
            margin-bottom: 0;
            border-left: 4px solid var(--accent-blue);
        }

        /* CORRECCIÓN: Estilos para campos requeridos */
        .form-select:required:invalid {
            border-color: var(--accent-red);
            box-shadow: 0 0 10px rgba(217, 83, 79, 0.3);
        }

        .form-select:required:valid {
            border-color: #28a745;
            box-shadow: 0 0 10px rgba(40, 167, 69, 0.3);
        }

        /* CORRECCIÓN: Indicador de archivo seleccionado */
        .file-selected {
            border-color: #28a745 !important;
            background: rgba(40, 167, 69, 0.05);
        }

        /* CORRECCIÓN: Estilo para progress bar mejorado */
        .progress {
            overflow: hidden;
            background: rgba(246, 184, 143, 0.2);
            border-radius: 8px;
            height: 12px;
        }

        .progress-bar {
            background: linear-gradient(45deg, var(--secondary-orange), var(--primary-orange));
            transition: width 0.4s ease;
            position: relative;
            overflow: hidden;
        }

        .progress-bar::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            right: 0;
            background-image: linear-gradient(
                -45deg,
                rgba(255, 255, 255, .2) 25%,
                transparent 25%,
                transparent 50%,
                rgba(255, 255, 255, .2) 50%,
                rgba(255, 255, 255, .2) 75%,
                transparent 75%,
                transparent
            );
            background-size: 30px 30px;
            animation: progress-bar-stripes 1s linear infinite;
        }

        @keyframes progress-bar-stripes {
            0% { background-position: 30px 0; }
            100% { background-position: 0 0; }
        }

        /* MODIFICACIÓN: Responsive mejorado para filtros */
        @media (max-width: 768px) {
            .location-info {
                text-align: center;
            }
            
            .location-info .location-text {
                flex-direction: column;
                gap: 4px;
            }
            
            .current-location-badge {
                margin-left: 0;
                margin-top: 4px;
            }

            /* CORRECCIÓN: Ajustes responsivos para modal de importación */
            #lugarConfirmacion .alert {
                font-size: 0.85rem;
                padding: 8px 12px;
            }
        }
    </style>

    <!-- =====================================
         MODIFICACIÓN: INFORMACIÓN DE AYUDA
         
         Modal con información sobre las nuevas
         funcionalidades y atajos de teclado
    ===================================== -->
    
    <div class="modal fade" id="modalAyuda" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-question-circle"></i> Ayuda - Funcionalidades del Sistema
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <h6><i class="bi bi-search me-2"></i>Búsqueda y Filtros</h6>
                    <ul class="mb-3">
                        <li>Busca por ECO, placas, marca o cualquier campo</li>
                        <li>Los resultados aparecen automáticamente mientras escribes</li>
                        <li>Filtra por lugar para ver vehículos de ubicaciones específicas</li>
                        <li>Los vehículos de tu lugar aparecen destacados con <i class="bi bi-house-fill text-warning"></i></li>
                    </ul>

                    <h6><i class="bi bi-file-excel me-2"></i>Importación de Excel</h6>
                    <ul class="mb-3">
                        <li><strong>IMPORTANTE:</strong> Selecciona primero el lugar donde se guardarán los vehículos</li>
                        <li>Formatos soportados: .xlsx y .xls (máximo 10MB)</li>
                        <li>Columnas requeridas: ECO (obligatorio), Placas, Marca, Modelo, etc.</li>
                        <li>Todos los vehículos del archivo se asignarán al lugar seleccionado</li>
                        <li>Después de importar se aplicará automáticamente el filtro del lugar</li>
                    </ul>

                    <h6><i class="bi bi-keyboard me-2"></i>Atajos de Teclado</h6>
                    <ul class="mb-3">
                        <li><kbd>Ctrl + F</kbd> - Enfocar búsqueda</li>
                        <li><kbd>Ctrl + L</kbd> - Enfocar filtro de lugar</li>
                        <li><kbd>Ctrl + R</kbd> - Resetear filtros</li>
                        <li><kbd>Ctrl + N</kbd> - Nuevo vehículo</li>
                    </ul>

                    <h6><i class="bi bi-star-fill me-2"></i>Características Especiales</h6>
                    <ul class="mb-0">
                        <li>Búsqueda en tiempo real sin recargar la página</li>
                        <li>Importación masiva desde archivos Excel con validación</li>
                        <li>Indicadores visuales para tu lugar de trabajo</li>
                        <li>Filtros persistentes en la URL</li>
                        <li>Diseño responsivo para todos los dispositivos</li>
                        <li>Validación automática de archivos y datos</li>
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                        <i class="fas fa-check"></i> Entendido
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- =====================================
         MODIFICACIÓN: BOTÓN DE AYUDA FLOTANTE
         
         Botón de acceso rápido a la ayuda
    ===================================== -->
    
    <div style="position: fixed; bottom: 20px; right: 20px; z-index: 1000;">
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalAyuda" 
                title="Ayuda y atajos de teclado">
            <i class="fas fa-question-circle"></i>
        </button>
    </div>

</body>

</html>