<!DOCTYPE html>
<!--
    FONTTRACK - PÁGINA DE BIENVENIDA
    
    Página principal del sistema FontTrack para gestión de inventario.
    Incluye animaciones fluidas, efectos visuales modernos y transiciones suaves.
    
    Características principales:
    - Diseño responsivo con burbujas animadas de fondo
    - Efecto de transición circular al navegar
    - Botones interactivos con hover effects
    - Glassmorphism design pattern
    - Optimizado para dispositivos móviles y desktop
    
    @version 2.0.0
    @author Daniela Peralta Perez
-->
<html lang="es">

<head>
    <!-- METADATOS BÁSICOS -->
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>FontTrack - Bienvenido</title>
    <link rel="icon" href="img/logo.png" type="image/png" />
    
    <!-- BIBLIOTECA DE ICONOS IONICOS -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css" />

    <style>
        /* =====================================
           RESET CSS Y CONFIGURACIÓN BASE
        ===================================== */
        
        /* Reset universal para eliminar márgenes y padding por defecto */
        *,
        *::before,
        *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Configuración base del documento */
        html,
        body {
            height: 100%;
            font-family: Arial, sans-serif;
            color: #634D3B;                /* Color principal del texto */
            background: #F9E5D5;           /* Color de fondo base */
            display: flex;
            flex-direction: column;
            overflow-x: hidden;            /* Evita scroll horizontal */
        }

        /* =====================================
           SISTEMA DE TRANSICIONES CIRCULARES
        ===================================== */
        
        /* Círculo que se expande durante la transición de página */
        .transition-circle {
            position: fixed;
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #634D3B 0%, #E38B5B 50%, #F4A978 100%);
            border-radius: 50%;
            z-index: 9998;                 /* Aparece sobre todo el contenido */
            transform: scale(0);           /* Comienza invisible */
            transition: transform 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }

        /* Estado de expansión del círculo de transición */
        .transition-circle.expanding {
            transform: scale(30);          /* Crece 30 veces su tamaño original */
        }

        /* =====================================
           SISTEMA DE BURBUJAS ANIMADAS
        ===================================== */
        
        /* Contenedor principal de las burbujas de fondo */
        .blob-container {
            position: fixed;
            inset: 0;                      /* Ocupa toda la pantalla */
            overflow: hidden;
            z-index: -1;                   /* Se mantiene detrás del contenido */
        }

        /* Configuración base para cada burbuja */
        .blob {
            position: absolute;
            width: 60vmin;                 /* Tamaño relativo al viewport */
            aspect-ratio: 1;               /* Mantiene forma cuadrada */
            border-radius: 40% 60% 30% 70% / 50% 30% 70% 50%;  /* Forma orgánica */
            opacity: .4;                   /* Transparencia para efecto sutil */
            animation: blobMove infinite ease-in-out;
        }

        /* Personalización individual de cada burbuja */
        
        /* Primera burbuja - Superior izquierda */
        .blob:nth-child(1) {
            background: #E38B5B;
            top: -10%;
            left: -10%;
            animation-duration: 12s;
        }

        /* Segunda burbuja - Inferior izquierda */
        .blob:nth-child(2) {
            background: #F6B88F;
            top: 60%;
            left: 5%;
            animation-duration: 10s;
        }

        /* Tercera burbuja - Superior derecha */
        .blob:nth-child(3) {
            background: #C49A6C;
            top: 20%;
            left: 70%;
            animation-duration: 8s;
        }

        /* Cuarta burbuja - Centro derecha (más pequeña) */
        .blob:nth-child(4) {
            background: #E38B5B;
            top: 40%;
            left: 85%;
            animation-duration: 15s;
            width: 40vmin;
        }

        /* Quinta burbuja - Centro superior (más pequeña) */
        .blob:nth-child(5) {
            background: #F6B88F;
            top: 10%;
            left: 40%;
            animation-duration: 14s;
            width: 30vmin;
        }

        /* =====================================
           ANIMACIONES KEYFRAMES
        ===================================== */
        
        /* Animación principal de movimiento de burbujas */
        @keyframes blobMove {
            0% {
                transform: scale(1) translate(0, 0) rotate(0deg);
                border-radius: 40% 60% 30% 70% / 50% 30% 70% 50%;
            }

            33% {
                transform: scale(1.2) translate(20px, -30px) rotate(120deg);
                border-radius: 60% 40% 70% 30% / 30% 70% 50% 50%;
            }

            66% {
                transform: scale(0.8) translate(-20px, 30px) rotate(240deg);
                border-radius: 30% 70% 50% 50% / 40% 60% 30% 70%;
            }

            100% {
                transform: scale(1) translate(0, 0) rotate(360deg);
                border-radius: 40% 60% 30% 70% / 50% 30% 70% 50%;
            }
        }

        /* Animación de aparición con desplazamiento hacia arriba */
        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Animación de rotación continua */
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Animación de deslizamiento desde la derecha */
        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(60px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* =====================================
           ESTRUCTURA PRINCIPAL DE LA PÁGINA
        ===================================== */
        
        /* Encabezado principal */
        header {
            background: #F4A978;          /* Color de fondo del header */
            padding: 1rem;
            text-align: center;
            border-bottom: 3px solid #E38B5B;  /* Borde decorativo */
            animation: slideInRight 0.8s ease-out;
            position: relative;
            z-index: 1;                   /* Aparece sobre las burbujas */
        }

        /* Título principal en el header */
        header h1 {
            font-size: 2.3rem;
            font-weight: bold;
        }

        /* Contenedor principal de contenido */
        main {
            flex: 1;                      /* Ocupa el espacio disponible */
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 1rem;
            position: relative;
            z-index: 1;                   /* Aparece sobre las burbujas */
        }

        /* =====================================
           TARJETA PRINCIPAL DE BIENVENIDA
        ===================================== */
        
        /* Tarjeta con efecto glassmorphism */
        .welcome-card {
            animation: slideInRight 1s ease-out both;
            background: rgba(255, 255, 255, 0.25);    /* Fondo semi-transparente */
            backdrop-filter: blur(16px);              /* Efecto de desenfoque del fondo */
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(83, 84, 85, 0.46);  /* Sombra suave */
            padding: 2.5rem 2rem;
            max-width: 600px;
            width: 100%;
            text-align: center;
        }

        /* Título secundario en la tarjeta */
        .welcome-card h2 {
            font-size: 2rem;
            margin-bottom: 1rem;
        }

        /* Párrafo descriptivo */
        .welcome-card p {
            font-size: 1.1rem;
            margin-bottom: 1.5rem;
            line-height: 1.4;             /* Mejora la legibilidad */
            opacity: .9;                  /* Ligeramente transparente */
        }

        /* =====================================
           SISTEMA DE BOTONES INTERACTIVOS
        ===================================== */
        
        /* Contenedor de la lista de botones */
        .btn-list ul {
            display: flex;
            justify-content: center;
            list-style: none;
            padding: 0;
            margin: 0;
        }

        /* Elemento individual de la lista */
        .btn-list li {
            margin: 0 15px;
        }

        /* Botón circular principal */
        .btn-list a {
            position: relative;
            width: 100px;
            height: 100px;
            background: #fff;
            border-radius: 50%;           /* Forma circular */
            border: 3px solid #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;             /* Oculta el desbordamiento del efecto */
            cursor: pointer;
            text-decoration: none;
            transition: box-shadow .5s ease;
            animation: slideInRight 1.2s ease-out;
        }

        /* Efecto de sombra al hacer hover */
        .btn-list a:hover {
            box-shadow: 0 0 20px #E38B5B;
        }

        /* Icono dentro del botón */
        .btn-list a i {
            font-size: 2em;
            color: #E38B5B;
            z-index: 2;                   /* Aparece sobre el efecto de fondo */
            transition: color .3s;
        }

        /* Etiqueta de texto del botón */
        .btn-list a .label {
            position: absolute;
            z-index: 2;                   /* Aparece sobre el efecto de fondo */
            color: #fff;
            font-size: 1.1rem;
            opacity: 0;                   /* Inicialmente invisible */
            transition: opacity .3s ease;
        }

        /* Efecto de fondo que se desliza hacia arriba */
        .btn-list a::after {
            content: "";
            position: absolute;
            top: -100%;                   /* Comienza fuera del botón */
            left: 0;
            width: 100%;
            height: 100%;
            background: #634D3B;
            border-radius: 50%;
            transition: top .5s ease-in;
            z-index: 1;                   /* Aparece detrás del contenido */
        }

        /* Estados de hover para efectos interactivos */
        .btn-list a:hover::after {
            top: 0;                       /* El fondo se desliza hacia arriba */
        }

        .btn-list a:hover i {
            color: transparent;           /* Oculta el icono durante hover */
        }

        .btn-list a:hover .label {
            opacity: 1;                   /* Muestra la etiqueta durante hover */
        }

        /* =====================================
           PIE DE PÁGINA
        ===================================== */
        
        /* Footer de la página */
        footer {
            background: #F4A978;          /* Mismo color que el header */
            padding: 1rem;
            text-align: center;
            border-top: 3px solid #E38B5B;    /* Borde decorativo superior */
            animation: slideInRight 0.8s ease-out;
            position: relative;
            z-index: 1;                   /* Aparece sobre las burbujas */
        }

        /* Texto del copyright */
        footer p {
            margin: 0;
            font-size: .95rem;
        }

        /* =====================================
           DISEÑO RESPONSIVO - TABLETS
        ===================================== */
        
        @media (max-width: 768px) {
            /* Ajuste de burbujas para tablets */
            .blob {
                width: 50vmin;
            }
            
            .blob:nth-child(4) {
                width: 35vmin;
            }
            
            .blob:nth-child(5) {
                width: 25vmin;
            }
        }

        /* =====================================
           DISEÑO RESPONSIVO - MÓVILES
        ===================================== */
        
        @media (max-width: 500px) {
            /* Botones más pequeños para móviles */
            .btn-list a {
                width: 80px;
                height: 80px;
            }

            /* Iconos más pequeños */
            .btn-list i {
                font-size: 1.6em;
            }

            /* Etiquetas más pequeñas */
            .btn-list a .label {
                font-size: 1rem;
            }

            /* Tarjeta principal adaptada para móviles */
            .welcome-card {
                padding: 2rem 1rem;
            }

            /* Títulos más pequeños */
            .welcome-card h2 {
                font-size: 1.5rem;
            }

            /* Párrafos más pequeños */
            .welcome-card p {
                font-size: 1rem;
            }

            /* Burbujas adaptadas para móviles */
            .blob {
                width: 40vmin;
            }
            
            .blob:nth-child(4) {
                width: 30vmin;
            }
            
            .blob:nth-child(5) {
                width: 20vmin;
            }
        }
    </style>
</head>

<body>
    <!-- =====================================
         ELEMENTOS DE FONDO Y EFECTOS
    ===================================== -->
    
    <!-- Contenedor de burbujas animadas que actúan como fondo decorativo -->
    <div class="blob-container">
        <div class="blob"></div>  <!-- Burbuja 1: Superior izquierda -->
        <div class="blob"></div>  <!-- Burbuja 2: Inferior izquierda -->
        <div class="blob"></div>  <!-- Burbuja 3: Superior derecha -->
        <div class="blob"></div>  <!-- Burbuja 4: Centro derecha -->
        <div class="blob"></div>  <!-- Burbuja 5: Centro superior -->
    </div>

    <!-- Círculo de transición para navegación suave entre páginas -->
    <div class="transition-circle" id="transitionCircle"></div>

    <!-- =====================================
         ESTRUCTURA PRINCIPAL DE LA PÁGINA
    ===================================== -->
    
    <!-- Encabezado con título y descripción del sistema -->
    <header>
        <h1>FontTrack</h1>
        <p>Gestión de inventario inspirada en la frescura de Bonafont</p>
    </header>

    <!-- Contenido principal de la página de bienvenida -->
    <main>
        <div class="welcome-card">
            <!-- Título de bienvenida -->
            <h2>Optimiza tu inventario con facilidad</h2>
            
            <!-- Imagen corporativa del sistema -->
            <img src="img/by.png" alt="by" style="display: block; margin: 1rem auto; max-width: 350px;">
            
            <!-- Descripción del propósito del sistema -->
            <p>FontTrack te ayuda a administrar tu stock de manera inteligente, eficiente y sencilla.</p>

            <!-- Lista de botones de navegación -->
            <div class="btn-list">
                <ul>
                    <li>
                        <!-- Botón principal para acceder al sistema -->
                        <a href="login" id="loginBtn">
                            <i class="ion-wrench"></i>           <!-- Icono de herramienta -->
                            <span class="label">Ingresar</span>  <!-- Texto que aparece en hover -->
                        </a>
                    </li>
                </ul>
            </div>

        </div>
    </main>

    <!-- Pie de página con información de copyright -->
    <footer>
        <p>&copy; 2025 FontTrack | Todos los derechos reservados</p>
    </footer>

    <!-- =====================================
         FUNCIONALIDAD JAVASCRIPT
    ===================================== -->
    
    <script>
        /**
         * SISTEMA DE TRANSICIONES CIRCULARES
         * 
         * Implementa un efecto visual donde un círculo se expande desde el botón
         * clicado para crear una transición suave entre páginas.
         * 
         * Funcionamiento:
         * 1. Detecta el clic en el botón de login
         * 2. Previene la navegación inmediata
         * 3. Posiciona un círculo en el centro del botón
         * 4. Expande el círculo para cubrir toda la pantalla
         * 5. Navega a la nueva página una vez completada la animación
         */
        document.addEventListener('DOMContentLoaded', function() {
            // Referencias a los elementos del DOM
            const loginBtn = document.getElementById('loginBtn');
            const circle = document.getElementById('transitionCircle');

            // Event listener para el botón de login
            loginBtn.addEventListener('click', function(e) {
                // Prevenir la navegación inmediata
                e.preventDefault();
                
                // Calcular la posición del centro del botón
                const btnRect = this.getBoundingClientRect();
                const btnCenterX = btnRect.left + btnRect.width / 2;
                const btnCenterY = btnRect.top + btnRect.height / 2;
                
                // Posicionar el círculo de transición en el centro del botón
                // Se resta 50 porque el círculo tiene 100px de ancho/alto
                circle.style.left = (btnCenterX - 50) + 'px';
                circle.style.top = (btnCenterY - 50) + 'px';
                
                // Activar la animación de expansión
                circle.classList.add('expanding');
                
                // Redirigir después de completar la transición (400ms)
                setTimeout(function() {
                    window.location.href = loginBtn.href;
                }, 400);
            });
        });
    </script>

</body>

</html>