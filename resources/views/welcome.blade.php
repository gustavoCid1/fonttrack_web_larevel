<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>FontTrack - Bienvenido</title>
    <link rel="icon" href="img/logo.png" type="image/png" />
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css" />

    <style>
        *,
        *::before,
        *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html,
        body {
            height: 100%;
            font-family: Arial, sans-serif;
            color: #634D3B;
            background: #F9E5D5;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        /* Efecto de expansión desde el botón - Reducido a la mitad */
        .transition-circle {
            position: fixed;
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #634D3B 0%, #E38B5B 50%, #F4A978 100%);
            border-radius: 50%;
            z-index: 9998;
            transform: scale(0);
            transition: transform 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }

        .transition-circle.expanding {
            transform: scale(30);
        }

        /* ───────── Animated Blobs ───────── */
        .blob-container {
            position: fixed;
            inset: 0;
            overflow: hidden;
            z-index: -1;
        }

        .blob {
            position: absolute;
            width: 60vmin;
            aspect-ratio: 1;
            border-radius: 40% 60% 30% 70% / 50% 30% 70% 50%;
            opacity: .4;
            animation: blobMove infinite ease-in-out;
        }

        .blob:nth-child(1) {
            background: #E38B5B;
            top: -10%;
            left: -10%;
            animation-duration: 12s;
        }

        .blob:nth-child(2) {
            background: #F6B88F;
            top: 60%;
            left: 5%;
            animation-duration: 10s;
        }

        .blob:nth-child(3) {
            background: #C49A6C;
            top: 20%;
            left: 70%;
            animation-duration: 8s;
        }

        .blob:nth-child(4) {
            background: #E38B5B;
            top: 40%;
            left: 85%;
            animation-duration: 15s;
            width: 40vmin;
        }

        .blob:nth-child(5) {
            background: #F6B88F;
            top: 10%;
            left: 40%;
            animation-duration: 14s;
            width: 30vmin;
        }

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

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Animaciones originales */
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

        header {
            background: #F4A978;
            padding: 1rem;
            text-align: center;
            border-bottom: 3px solid #E38B5B;
            animation: slideInRight 0.8s ease-out;
            position: relative;
            z-index: 1;
        }

        header h1 {
            font-size: 2.3rem;
            font-weight: bold;
        }

        main {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 1rem;
            position: relative;
            z-index: 1;
        }

        .welcome-card {
            animation: slideInRight 1s ease-out both;
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(83, 84, 85, 0.46);
            padding: 2.5rem 2rem;
            max-width: 600px;
            width: 100%;
            text-align: center;
        }

        .welcome-card h2 {
            font-size: 2rem;
            margin-bottom: 1rem;
        }

        .welcome-card p {
            font-size: 1.1rem;
            margin-bottom: 1.5rem;
            line-height: 1.4;
            opacity: .9;
        }

        .btn-list ul {
            display: flex;
            justify-content: center;
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .btn-list li {
            margin: 0 15px;
        }

        .btn-list a {
            position: relative;
            width: 100px;
            height: 100px;
            background: #fff;
            border-radius: 50%;
            border: 3px solid #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
            cursor: pointer;
            text-decoration: none;
            transition: box-shadow .5s ease;
            animation: slideInRight 1.2s ease-out;
        }

        .btn-list a:hover {
            box-shadow: 0 0 20px #E38B5B;
        }

        .btn-list a i {
            font-size: 2em;
            color: #E38B5B;
            z-index: 2;
            transition: color .3s;
        }

        .btn-list a .label {
            position: absolute;
            z-index: 2;
            color: #fff;
            font-size: 1.1rem;
            opacity: 0;
            transition: opacity .3s ease;
        }

        .btn-list a::after {
            content: "";
            position: absolute;
            top: -100%;
            left: 0;
            width: 100%;
            height: 100%;
            background: #634D3B;
            border-radius: 50%;
            transition: top .5s ease-in;
            z-index: 1;
        }

        .btn-list a:hover::after {
            top: 0;
        }

        .btn-list a:hover i {
            color: transparent;
        }

        .btn-list a:hover .label {
            opacity: 1;
        }

        footer {
            background: #F4A978;
            padding: 1rem;
            text-align: center;
            border-top: 3px solid #E38B5B;
            animation: slideInRight 0.8s ease-out;
            position: relative;
            z-index: 1;
        }

        footer p {
            margin: 0;
            font-size: .95rem;
        }

        @media (max-width: 768px) {
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

        @media (max-width: 500px) {
            .btn-list a {
                width: 80px;
                height: 80px;
            }

            .btn-list i {
                font-size: 1.6em;
            }

            .btn-list a .label {
                font-size: 1rem;
            }

            .welcome-card {
                padding: 2rem 1rem;
            }

            .welcome-card h2 {
                font-size: 1.5rem;
            }

            .welcome-card p {
                font-size: 1rem;
            }

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
    <!-- Burbujas animadas de fondo -->
    <div class="blob-container">
        <div class="blob"></div>
        <div class="blob"></div>
        <div class="blob"></div>
        <div class="blob"></div>
        <div class="blob"></div>
    </div>

    <!-- Círculo de transición -->
    <div class="transition-circle" id="transitionCircle"></div>

    <header>
        <h1>FontTrack</h1>
        <p>Gestión de inventario inspirada en la frescura de Bonafont</p>
    </header>

    <main>
        <div class="welcome-card">
            <h2>Optimiza tu inventario con facilidad</h2>
            <img src="img/by.png" alt="by" style="display: block; margin: 1rem auto; max-width: 350px;">
            <p>FontTrack te ayuda a administrar tu stock de manera inteligente, eficiente y sencilla.</p>

            <div class="btn-list">
                <ul>
                    <li>
                        <a href="login" id="loginBtn">
                            <i class="ion-wrench"></i>
                            <span class="label">Ingresar</span>
                        </a>
                    </li>
                </ul>
            </div>

        </div>
    </main>

    <footer>
        <p>&copy; 2025 FontTrack | Todos los derechos reservados</p>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const loginBtn = document.getElementById('loginBtn');
            const circle = document.getElementById('transitionCircle');

            loginBtn.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Obtener la posición del botón
                const btnRect = this.getBoundingClientRect();
                const btnCenterX = btnRect.left + btnRect.width / 2;
                const btnCenterY = btnRect.top + btnRect.height / 2;
                
                // Posicionar el círculo en el centro del botón
                circle.style.left = (btnCenterX - 50) + 'px';
                circle.style.top = (btnCenterY - 50) + 'px';
                
                // Iniciar la expansión del círculo
                circle.classList.add('expanding');
                
                // Redirigir después de completar la transición (reducido a la mitad: 400ms)
                setTimeout(function() {
                    window.location.href = loginBtn.href;
                }, 400);
            });
        });
    </script>

</body>

</html>