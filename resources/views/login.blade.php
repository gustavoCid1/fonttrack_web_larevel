<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Inicia Sesión – FontTrack</title>
    <link rel="icon" href="{{ asset('img/logo.png') }}" type="image/png" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css?family=Lato:400,700" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

    <style>
        /* ───────── Reset & Base ───────── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html { font-size: 16px; }
        body {
            font-family: "Lato", Arial, sans-serif;
            color: #634D3B;
            height: 100vh;
            background: #F9E5D5;
            overflow: auto;
        }

        /* ───────── Animación de entrada ───────── */
        .transition-circle {
            position: fixed;
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #634D3B 0%, #E38B5B 50%, #F4A978 100%);
            border-radius: 50%;
            z-index: 9998;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) scale(35);
            animation: shrinkIn 1s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards;
        }
        @keyframes shrinkIn { to { transform: translate(-50%, -50%) scale(0); } }

        /* ───────── Animated Blobs ───────── */
        .blob-container { position: fixed; inset: 0; overflow: hidden; z-index: -1; }
        .blob {
            position: absolute;
            width: 60vmin;
            aspect-ratio: 1;
            border-radius: 40% 60% 30% 70% / 50% 30% 70% 50%;
            opacity: .4;
            animation: blobMove infinite ease-in-out;
        }
        .blob:nth-child(1) { background: #E38B5B; top: -10%; left: -10%; animation-duration: 12s; }
        .blob:nth-child(2) { background: #F6B88F; top: 60%; left: 5%; animation-duration: 10s; }
        .blob:nth-child(3) { background: #C49A6C; top: 20%; left: 70%; animation-duration: 8s; }
        .blob:nth-child(4) { background: #E38B5B; top: 40%; left: 85%; animation-duration: 15s; width: 40vmin; }
        .blob:nth-child(5) { background: #F6B88F; top: 10%; left: 40%; animation-duration: 14s; width: 30vmin; }

        @keyframes blobMove {
            0% { transform: scale(1) translate(0, 0) rotate(0deg); border-radius: 40% 60% 30% 70% / 50% 30% 70% 50%; }
            33% { transform: scale(1.2) translate(20px, -30px) rotate(120deg); border-radius: 60% 40% 70% 30% / 30% 70% 50% 50%; }
            66% { transform: scale(0.8) translate(-20px, 30px) rotate(240deg); border-radius: 30% 70% 50% 50% / 40% 60% 30% 70%; }
            100% { transform: scale(1) translate(0, 0) rotate(360deg); border-radius: 40% 60% 30% 70% / 50% 30% 70% 50%; }
        }

        /* ───────── Glassmorphism Card ───────── */
        .login-card {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 90%;
            max-width: 420px;
            padding: 2.5rem 2rem;
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
            z-index: 1;
            opacity: 0;
            animation: fadeInUp 1s ease-out 0.5s forwards;
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translate(-50%, -40%) scale(0.9); }
            to { opacity: 1; transform: translate(-50%, -50%) scale(1); }
        }

        .login-card .title {
            text-align: center;
            font-size: 1.75rem;
            color: #E38B5B;
            font-weight: bold;
            margin-bottom: 1.5rem;
        }

        /* ───────── Burbujas de Error Mejoradas con X ───────── */
        .error-bubble {
            position: relative;
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            color: white;
            padding: 12px 40px 12px 16px;
            border-radius: 18px;
            font-size: 0.85rem;
            margin: 8px 0 16px;
            box-shadow: 0 4px 12px rgba(231, 76, 60, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.1);
            display: none;
            animation: bubbleSlideIn 0.3s ease-out;
            max-width: 100%;
        }

        .error-bubble::before {
            content: '';
            position: absolute;
            top: -8px;
            left: 20px;
            width: 0;
            height: 0;
            border-left: 8px solid transparent;
            border-right: 8px solid transparent;
            border-bottom: 8px solid #e74c3c;
        }

        .error-bubble.show {
            display: block;
        }

        .error-bubble .close-btn {
            position: absolute;
            top: 50%;
            right: 12px;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: white;
            font-size: 16px;
            cursor: pointer;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: background-color 0.2s;
        }

        .error-bubble .close-btn:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .error-bubble.hidden {
            display: none !important;
        }

        @keyframes bubbleSlideIn {
            from {
                opacity: 0;
                transform: translateY(-10px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        /* ───────── Input Fields con botón de mostrar contraseña ───────── */
        .input-container {
            position: relative;
            margin-bottom: 1rem;
        }

        .input-field {
            width: 100%;
            padding: .75rem 1rem;
            border: 1px solid #E0C4AA;
            border-radius: 8px;
            background: #fffaf6;
            transition: all .3s;
        }

        .input-field.has-icon {
            padding-right: 45px;
        }

        .input-field:focus { 
            border-color: #E38B5B; 
            outline: none; 
            box-shadow: 0 0 0 3px rgba(227, 139, 91, 0.1);
        }
        .input-field.is-invalid { border-color: #e74c3c; background: #fdf2f2; }

        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #634D3B;
            cursor: pointer;
            font-size: 1.1rem;
            padding: 4px;
            border-radius: 4px;
            transition: all 0.2s;
        }

        .password-toggle:hover {
            background-color: rgba(227, 139, 91, 0.1);
            color: #E38B5B;
        }

        /* ───────── Password Requirements (Estilo Burbuja) ───────── */
        .password-requirements {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: white;
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 0.75rem;
            margin: 8px 0 16px;
            box-shadow: 0 4px 12px rgba(44, 62, 80, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .requirement {
            display: block;
            color: #e74c3c;
            transition: color 0.3s;
            margin: 2px 0;
        }
        .requirement.valid { color: #27ae60; }

        /* ───────── User Type Indicator ───────── */
        .user-type-indicator {
            position: absolute;
            top: 10px;
            right: 15px;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: bold;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .user-type-indicator.admin {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
        }

        .user-type-indicator.user {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
        }

        .user-type-indicator.show {
            opacity: 1;
        }

        /* ───────── Animated Buttons Centrados ───────── */
        .btn-animated {
            text-transform: uppercase;
            font-weight: 700;
            border: 0;
            position: relative;
            letter-spacing: 0.15em;
            margin: 0.5rem auto;
            padding: 1rem 2.5rem;
            background: transparent;
            outline: none;
            font-size: 1rem;
            color: #634D3B;
            transition: all 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55) 0.15s;
            cursor: pointer;
            display: block;
            width: 100%;
            max-width: 280px;
            border-radius: 8px;
            text-align: center;
            text-decoration: none;
        }

        .btn-animated::after, .btn-animated::before {
            content: "";
            position: absolute;
            height: 40%;
            width: 10%;
            transition: all 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            z-index: -10;
            border-radius: 50%;
        }

        .btn-animated.btn-primary::before {
            background-color: #D1784C;
            top: -0.75rem;
            left: 0.5rem;
            animation: topAnimation 2s cubic-bezier(0.68, -0.55, 0.265, 1.55) 0.25s infinite alternate;
        }
        .btn-animated.btn-primary::after {
            background-color: #E38B5B;
            top: 3rem;
            left: calc(100% - 2rem);
            animation: bottomAnimation 2s cubic-bezier(0.68, -0.55, 0.265, 1.55) 0.5s infinite alternate;
        }

        .btn-animated.btn-secondary::before {
            background-color: #A9866A;
            top: -0.75rem;
            left: 0.5rem;
            animation: topAnimation 2s cubic-bezier(0.68, -0.55, 0.265, 1.55) 0.25s infinite alternate;
        }
        .btn-animated.btn-secondary::after {
            background-color: #c49a6c;
            top: 3rem;
            left: calc(100% - 2rem);
            animation: bottomAnimation 2s cubic-bezier(0.68, -0.55, 0.265, 1.55) 0.5s infinite alternate;
        }

        .btn-animated:hover { color: white; }
        .btn-animated:hover::before, .btn-animated:hover::after {
            top: 0;
            height: 100%;
            width: 100%;
            border-radius: 8px;
            animation: none;
        }
        .btn-animated:hover::after { left: 0rem; }
        .btn-animated:hover::before { top: 0rem; left: 0rem; }

        @keyframes topAnimation {
            from { transform: translate(0rem, 0); }
            to { transform: translate(0rem, 3.5rem); }
        }
        @keyframes bottomAnimation {
            from { transform: translate(-80%, 0); }
            to { transform: translate(0rem, 0); }
        }

        .btn-link {
            display: block;
            margin: 0.75rem auto;
            text-align: center;
            color: #634D3B;
            font-size: .9rem;
            text-decoration: none;
            max-width: 280px;
            padding: 8px 16px;
            border-radius: 6px;
            transition: all 0.3s ease;
        }
        .btn-link:hover { 
            color: #E38B5B; 
            background-color: rgba(227, 139, 91, 0.1);
            text-decoration: none;
        }

        /* ───────── Botón WhatsApp especial ───────── */
        .btn-whatsapp {
            background: linear-gradient(135deg,rgb(199, 120, 94) 0%,rgb(209, 83, 67) 100%);
            color: white !important;
            border-radius: 25px;
            padding: 10px 20px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(255, 145, 97, 0.3);
        }

        .btn-whatsapp:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(37, 211, 102, 0.4);
            color: white !important;
        }

        .btn-whatsapp i {
            font-size: 1.1rem;
        }

        /* ───────── Contenedor de botones centrado ───────── */
        .button-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
            margin-top: 1rem;
        }

        /* ───────── Modal Overrides ───────── */
        .modal .form-control {
            background: #fffaf6;
            border: 1px solid #E0C4AA;
            border-radius: 6px;
        }
        .modal .form-control:focus { 
            border-color: #E38B5B; 
            box-shadow: 0 0 0 3px rgba(227, 139, 91, 0.1);
        }
        .modal .form-control.is-invalid { border-color: #e74c3c; background: #fdf2f2; }
        .modal-header { background: #F6B88F; border-bottom: 2px solid #E38B5B; }
        .modal-title { color: #634D3B; font-weight: bold; }
        .modal .btn-primary { background: #E38B5B; border: none; border-radius: 6px; }
        .modal .btn-primary:hover { background: #D1784C; }
        .modal .btn-secondary { background: #c49a6c; border: none; border-radius: 6px; }
        .modal .btn-secondary:hover { background: #A9866A; }

        /* ───────── Imagen centrada ───────── */
        .logo-footer {
            display: block;
            margin: 1.5rem auto 0;
            max-width: 150px;
            height: auto;
        }

        /* ───────── Responsive ───────── */
        @media (max-width: 768px) {
            .login-card { width: 95%; padding: 2rem 1.5rem; }
            .login-card .title { font-size: 1.5rem; }
            .input-field { padding: .65rem .85rem; }
            .btn-animated { padding: .65rem 2rem; font-size: 0.9rem; max-width: 240px; }
            .blob { width: 50vmin; }
            .blob:nth-child(4) { width: 35vmin; }
            .blob:nth-child(5) { width: 25vmin; }
        }

        @media (max-width: 576px) {
            html { font-size: 14px; }
            .login-card { padding: 1.5rem 1rem; }
            .login-card .title { font-size: 1.25rem; }
            .input-field { padding: .5rem .75rem; font-size: .9rem; }
            .btn-animated { padding: .5rem 1.5rem; font-size: .9rem; max-width: 200px; }
            .blob { width: 40vmin; }
            .blob:nth-child(4) { width: 30vmin; }
            .blob:nth-child(5) { width: 20vmin; }
        }
    </style>
</head>

<body>
    <div class="transition-circle" id="transitionCircle"></div>

    <div class="blob-container">
        <div class="blob"></div>
        <div class="blob"></div>
        <div class="blob"></div>
        <div class="blob"></div>
        <div class="blob"></div>
    </div>

    <div class="login-card">
        <div class="user-type-indicator" id="userTypeIndicator"></div>
        <h4 class="title">Inicia Sesión</h4>
        <form method="POST" action="{{ route('login') }}" id="loginForm">
            @csrf

            <div class="input-container">
                <input id="logemail" name="correo" type="email" class="input-field" placeholder="Correo" required
                    autocomplete="off" value="{{ old('correo') }}">
            </div>
            @error('correo') 
                <div class="error-bubble show">
                    {{ $message }}
                    <button type="button" class="close-btn" onclick="closeBubble(this)">×</button>
                </div>
            @else
                <div class="error-bubble" id="emailError">
                    Solo se admiten correos con el dominio @bonafont.com o @danone.com
                    <button type="button" class="close-btn" onclick="closeBubble(this)">×</button>
                </div>
            @enderror

            <div class="input-container">
                <input id="logpass" name="password" type="password" class="input-field has-icon" placeholder="Contraseña" required
                    autocomplete="off">
                <button type="button" class="password-toggle" onclick="togglePassword('logpass', this)">
                    <i class="bi bi-eye"></i>
                </button>
            </div>
            @error('password') 
                <div class="error-bubble show">
                    {{ $message }}
                    <button type="button" class="close-btn" onclick="closeBubble(this)">×</button>
                </div>
            @else
                <div class="error-bubble" id="passwordError">
                    La contraseña no cumple con los requisitos
                    <button type="button" class="close-btn" onclick="closeBubble(this)">×</button>
                </div>
            @enderror

            <div class="button-container">
                <button type="submit" class="btn-animated btn-primary">Entrar</button>
                <a href="{{ url('/') }}" class="btn-animated btn-secondary">Regresar</a>
                <a href="#" class="btn-link" data-bs-toggle="modal" data-bs-target="#modalRegistro" id="btnNuevoUsuario">Registrar</a>
                <a id="btnWhatsApp" class="btn-link btn-whatsapp" target="_blank">
                    <i class="bi bi-whatsapp"></i>
                    ¿Olvidaste tu contraseña?
                </a>
            </div>
            
            <img src="{{ asset('img/by.png') }}" alt="by" class="logo-footer">
        </form>
    </div>

    <!-- Modal de Registro -->
    <div class="modal fade" id="modalRegistro" tabindex="-1" aria-labelledby="modalRegistroLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="formRegistro" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="usuarioId" name="id_usuario">

                    <div class="modal-header">
                        <h5 class="modal-title" id="modalRegistroLabel">Registrar Usuario</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre:</label>
                            <input type="text" id="nombre" name="nombre" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="correoRegistro" class="form-label">Correo:</label>
                            <input type="email" id="correoRegistro" name="correo" class="form-control" required>
                            <div class="error-bubble" id="emailModalError">
                                Debe ser un correo @bonafont.com o @danone.com
                                <button type="button" class="close-btn" onclick="closeBubble(this)">×</button>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="passwordRegistro" class="form-label">Contraseña:</label>
                            <div class="input-container">
                                <input type="password" id="passwordRegistro" name="password" class="form-control has-icon" required>
                                <button type="button" class="password-toggle" onclick="togglePassword('passwordRegistro', this)">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            <div class="password-requirements">
                                <span class="requirement" id="req-uppercase">• Al menos una letra mayúscula</span>
                                <span class="requirement" id="req-number">• Al menos un número</span>
                                <span class="requirement" id="req-length">• Mínimo 6 caracteres</span>
                                <span class="requirement" id="req-sequence">• No puede ser una secuencia numérica</span>
                            </div>
                        </div>
                        <input type="hidden" id="tipo_usuario" name="tipo_usuario" value="2">
                        <div class="mb-3">
                            <label for="foto_usuario" class="form-label">Foto de Perfil:</label>
                            <input type="file" id="foto_usuario" name="foto_usuario" class="form-control" accept="image/png, image/jpeg">
                            <small class="text-muted">Si no selecciona una imagen, se usará la foto por defecto</small>
                        </div>
                        <div class="mb-3">
                            <label for="id_lugar" class="form-label">Lugar:</label>
                            <select id="id_lugar" name="id_lugar" class="form-control">
                                @foreach($lugares as $lugar)
                                    <option value="{{ $lugar->id_lugar }}">{{ $lugar->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(() => document.getElementById('transitionCircle').style.display = 'none', 1500);
        });

        // Función para cerrar burbujas de error
        function closeBubble(button) {
            const bubble = button.parentElement;
            bubble.classList.remove('show');
            bubble.classList.add('hidden');
            // También remover la clase is-invalid del input asociado si existe
            const inputContainer = bubble.previousElementSibling;
            if (inputContainer && inputContainer.classList.contains('input-container')) {
                const input = inputContainer.querySelector('.input-field');
                if (input) {
                    input.classList.remove('is-invalid');
                }
            }
        }

        // Función para mostrar/ocultar contraseña
        function togglePassword(inputId, button) {
            const input = document.getElementById(inputId);
            const icon = button.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'bi bi-eye-slash';
            } else {
                input.type = 'password';
                icon.className = 'bi bi-eye';
            }
        }

        function isValidEmail(email) {
            if (!email || email.trim() === '') return false;
            const emailLower = email.toLowerCase().trim();
            return emailLower.endsWith('@bonafont.com') || emailLower.endsWith('@danone.com');
        }

        function validatePassword(password) {
            const hasUppercase = /[A-Z]/.test(password);
            const hasNumber = /[0-9]/.test(password);
            const hasMinLength = password.length >= 6;
            const isNotSequence = !isSequentialPassword(password);
            return { hasUppercase, hasNumber, hasMinLength, isNotSequence, isValid: hasUppercase && hasNumber && hasMinLength && isNotSequence };
        }

        function isSequentialPassword(password) {
            if (!/^\d+$/.test(password)) return false;
            let isAscending = true, isDescending = true;
            for (let i = 1; i < password.length; i++) {
                const current = parseInt(password[i]), previous = parseInt(password[i - 1]);
                if (current !== previous + 1) isAscending = false;
                if (current !== previous - 1) isDescending = false;
            }
            return isAscending || isDescending;
        }

        function showError(elementId) {
            const element = document.getElementById(elementId);
            if (element) {
                element.classList.remove('hidden');
                element.classList.add('show');
                element.style.display = 'block';
            }
        }

        function hideError(elementId) {
            const element = document.getElementById(elementId);
            if (element) {
                element.classList.remove('show');
                element.classList.add('hidden');
                element.style.display = 'none';
            }
        }

        // Función para mostrar indicador de tipo de usuario
        function showUserTypeIndicator(email) {
            const indicator = document.getElementById('userTypeIndicator');
            if (!indicator) return;
            
            if (isValidEmail(email)) {
                // Simular verificación de tipo de usuario basado en el email
                // En una implementación real, esto vendría del backend
                const isAdmin = email.toLowerCase().includes('admin') || 
                               email.toLowerCase().includes('supervisor') ||
                               email.toLowerCase().includes('gerente');
                
                if (isAdmin) {
                    indicator.textContent = 'ADMIN';
                    indicator.className = 'user-type-indicator admin show';
                } else {
                    indicator.textContent = 'USER';
                    indicator.className = 'user-type-indicator user show';
                }
                
                setTimeout(() => {
                    indicator.classList.remove('show');
                }, 3000);
            } else {
                indicator.classList.remove('show');
            }
        }

        // Validación de login
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const email = document.getElementById('logemail').value;
            const password = document.getElementById('logpass').value;
            let hasErrors = false;

            if (email && !isValidEmail(email)) {
                e.preventDefault();
                showError('emailError');
                document.getElementById('logemail').classList.add('is-invalid');
                hasErrors = true;
            } else {
                hideError('emailError');
                document.getElementById('logemail').classList.remove('is-invalid');
            }

            if (password) {
                const passwordValidation = validatePassword(password);
                if (!passwordValidation.isValid) {
                    e.preventDefault();
                    showError('passwordError');
                    document.getElementById('logpass').classList.add('is-invalid');
                    hasErrors = true;
                } else {
                    hideError('passwordError');
                    document.getElementById('logpass').classList.remove('is-invalid');
                }
            }
            return !hasErrors;
        });

        // Validación en tiempo real
        document.getElementById('logemail').addEventListener('input', function() {
            const email = this.value;
            if (email && !isValidEmail(email)) {
                showError('emailError');
                this.classList.add('is-invalid');
            } else {
                hideError('emailError');
                this.classList.remove('is-invalid');
                if (email) {
                    showUserTypeIndicator(email);
                }
            }
        });

        document.getElementById('logpass').addEventListener('input', function() {
            if (this.value) {
                const validation = validatePassword(this.value);
                if (!validation.isValid) {
                    showError('passwordError');
                    this.classList.add('is-invalid');
                } else {
                    hideError('passwordError');
                    this.classList.remove('is-invalid');
                }
            }
        });

        // Modal validations
        $('#correoRegistro').on('input', function() {
            const email = $(this).val();
            if (email && !isValidEmail(email)) {
                $(this).addClass('is-invalid');
                $('#emailModalError').addClass('show').show();
            } else {
                $(this).removeClass('is-invalid');
                $('#emailModalError').removeClass('show').hide();
            }
        });

        $('#passwordRegistro').on('input', function() {
            const password = $(this).val();
            const validation = validatePassword(password);
            $('#req-uppercase').toggleClass('valid', validation.hasUppercase);
            $('#req-number').toggleClass('valid', validation.hasNumber);
            $('#req-length').toggleClass('valid', validation.hasMinLength);
            $('#req-sequence').toggleClass('valid', validation.isNotSequence);
            $(this).toggleClass('is-invalid', password && !validation.isValid);
        });

        $('#formRegistro').submit(function (e) {
            e.preventDefault();
            const correoRegistro = $('#correoRegistro').val();
            const passwordRegistro = $('#passwordRegistro').val();

            if (!isValidEmail(correoRegistro)) {
                alert('El correo debe ser del dominio @bonafont.com o @danone.com');
                return false;
            }

            const passwordValidation = validatePassword(passwordRegistro);
            if (!passwordValidation.isValid) {
                alert('La contraseña debe cumplir con todos los requisitos mostrados');
                return false;
            }
            
            const id = $('#usuarioId').val();
            const url = id ? `/modal/update_user/${id}` : `/modal/register_user`;
            const method = id ? 'PUT' : 'POST';
            const data = new FormData(this);
            
            if (!$('#foto_usuario')[0].files.length) {
                data.append('foto_default', 'Sin_Foto.png');
            }

            $.ajax({
                url, method, data,
                processData: false,
                contentType: false,
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success(res) { alert(res.message); location.reload(); },
                error(err) {
                    const msgs = err.responseJSON?.errors || {};
                    alert(Object.values(msgs).flat().join('\n'));
                }
            });
        });

        $('#btnNuevoUsuario').on('click', function () {
            $('#formRegistro')[0].reset();
            $('#usuarioId').val('');
            $('#tipo_usuario').val('2');
            $('.requirement').removeClass('valid');
            $('.form-control').removeClass('is-invalid');
            $('.error-bubble').removeClass('show').hide();
        });

        // WhatsApp functionality
        document.getElementById('btnWhatsApp').addEventListener('click', function (e) {
            const emailInput = document.getElementById('logemail');
            const correo = emailInput.value.trim();
            if (!correo) {
                e.preventDefault();
                alert('Por favor ingresa tu correo antes de solicitar ayuda por WhatsApp.');
                return;
            }
            const mensaje = encodeURIComponent(`Hola, olvidé mi contraseña. Mi correo es ${correo}`);
            const numero = '5564936743';
            const url = `https://wa.me/${numero}?text=${mensaje}`;
            this.setAttribute('href', url);
        });
    </script>
</body>
</html>