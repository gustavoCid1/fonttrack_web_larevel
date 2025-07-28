<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Lugares</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>

 /* Animaciones para modales - Efecto globo/agua */
        @keyframes modalBubbleIn {
            0% {
                transform: scale(0.3) translateY(100px);
                opacity: 0;
            }
            50% {
                transform: scale(1.05) translateY(-10px);
                opacity: 0.8;
            }
            70% {
                transform: scale(0.95) translateY(5px);
                opacity: 0.9;
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

        @keyframes modalFadeOut {
            0% {
                transform: scale(1);
                opacity: 1;
            }
            100% {
                transform: scale(0.8);
                opacity: 0;
            }
        }

        /* Aplicar animaciones a los modales */
        .modal.show .modal-dialog {
            animation: modalBubbleIn 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }

        .modal-content {
            overflow: hidden;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .modal.fade .modal-dialog {
            transition: all 0.4s ease-out;
        }

        /* Efecto de ondas en el backdrop */
        .modal-backdrop {
            background: linear-gradient(45deg, rgba(0,0,0,0.5), rgba(0,0,0,0.7));
            backdrop-filter: blur(3px);
        }

        /* Barra de navegación con contraste suave */
        .navbar {
            background-color: #F6B88F;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
            padding: 12px 20px;
            border-bottom: 4px solid #E38B5B;
        }

        /* Logo clickeable */
        .navbar .logo {
            cursor: pointer;
            transition: transform 0.3s ease, filter 0.3s ease;
        }

        .navbar .logo:hover {
            transform: scale(1.05);
            filter: brightness(1.1);
        }

        .navbar .navbar-brand {
            color: #634D3B;
            font-weight: bold;
            font-size: 1.6em;
            transition: color 0.3s ease-in-out;
        }

        .navbar .navbar-brand:hover {
            color: #E38B5B;
        }

        .navbar .navbar-nav .nav-link {
            color: #634D3B;
            font-size: 1em;
            padding: 10px 15px;
            font-weight: bold;
            transition: background-color 0.3s ease-in-out, transform 0.2s ease-in-out;
        }

        .navbar .navbar-nav .nav-link:hover {
            background-color: rgba(227, 139, 91, 0.2);
            border-radius: 6px;
            transform: scale(1.05);
        }

        .navbar .btn-danger {
            background-color: #D9534F;
            font-weight: bold;
            padding: 8px 15px;
            border-radius: 6px;
            transition: background-color 0.3s ease-in-out, transform 0.2s ease-in-out;
        }

        .navbar .btn-danger:hover {
            background-color: #C9302C;
            transform: scale(1.1);
        }

        .navbar-toggler-icon {
            filter: invert(50%);
        }

        @media (max-width: 768px) {
            .navbar {
                padding: 8px 15px;
            }

            .navbar .navbar-brand {
                font-size: 1.3em;
            }

            .navbar .navbar-nav .nav-link {
                font-size: 0.9em;
                padding: 8px 10px;
            }
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #FCE8D5;
            color: #634D3B;
            text-align: center;
            margin: 0;
            padding: 0;
        }

        .container {
            background: #FFF;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #E38B5B;
            font-size: 2em;
            font-weight: bold;
        }

        /* Buscador de usuarios */
        .search-container {
            background: linear-gradient(135deg, #F6B88F, #E38B5B);
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .search-input {
            border: none;
            border-radius: 25px;
            padding: 12px 20px;
            font-size: 16px;
            box-shadow: inset 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .search-input:focus {
            transform: scale(1.02);
            box-shadow: 0 0 20px rgba(227, 139, 91, 0.5);
            outline: none;
        }

        .btn {
            background-color: #E38B5B;
            color: #fff;
            font-size: 1.2em;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            border: none;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .btn:hover {
            background-color: #D1784C;
            transform: scale(1.05);
        }

        .table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .table thead {
            background-color: #F6B88F;
            color: #fff;
        }

        .table th,
        .table td {
            padding: 15px;
        }

        .modal-header {
            background-color: #F6B88F;
            color: #fff;
        }

        .modal-footer {
            background-color: #FCE8D5;
        }

        .btn-info {
            background-color: #88C0D0;
            border: none;
        }

        .btn-warning {
            background-color: #E5A34D;
            border: none;
        }

        .btn-danger {
            background-color: #D9534F;
            border: none;
        }

        /* Paginación mejorada */
        .pagination {
            display: flex;
            justify-content: center;
            padding: 20px;
            list-style: none;
            margin: 0;
        }

        .pagination li {
            margin: 0 3px;
        }

        .pagination li a,
        .pagination li span {
            text-decoration: none;
            padding: 12px 16px;
            background-color: #E38B5B;
            color: white;
            border-radius: 8px;
            font-weight: bold;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .pagination li a:hover {
            background-color: #D1784C;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(227, 139, 91, 0.4);
        }

        .pagination .active span {
            background-color: #F6B88F;
            color: #fff;
            border: 2px solid #E38B5B;
            transform: scale(1.1);
        }

        .pagination .disabled span {
            background-color: #ccc;
            color: #666;
            cursor: not-allowed;
        }

        /* Vista previa de imagen en modal */
        .image-preview {
            max-width: 150px;
            max-height: 150px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease;
        }

        .image-preview:hover {
            transform: scale(1.05);
        }

        /* Animación para filas de tabla */
        .table tbody tr {
            transition: all 0.3s ease;
        }

        .table tbody tr:hover {
            background-color: rgba(246, 184, 143, 0.1);
            transform: scale(1.01);
        }
                    /* Estilo para el perfil de usuario */
        .user-profile {
            display: flex;
            align-items: center;
            cursor: pointer;
            position: relative;
        }

        .user-profile img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
            border: 2px solid #E38B5B;
        }

        .user-profile .user-name {
            font-weight: bold;
            color: #634D3B;
        }

        .user-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            padding: 10px 0;
            min-width: 180px;
            z-index: 1000;
            display: none;
        }

        .user-dropdown.show {
            display: block;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .user-dropdown a {
            display: block;
            padding: 8px 15px;
            color: #634D3B;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .user-dropdown a:hover {
            background-color: rgba(227, 139, 91, 0.1);
            color: #E38B5B;
        }

        .user-dropdown a i {
            margin-right: 8px;
        }

        @media (max-width: 768px) {
            .navbar {
                padding: 8px 15px;
            }

            .navbar .navbar-brand {
                font-size: 1.3em;
            }

            .navbar .navbar-nav .nav-link {
                font-size: 0.9em;
                padding: 8px 10px;
            }
            
            .user-profile {
                margin-top: 10px;
            }
            
            .user-dropdown {
                position: static;
                margin-top: 5px;
            }
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #FCE8D5;
            color: #634D3B;
            text-align: center;
            margin: 0;
            padding: 0;
        }

        .container {
            background: #FFF;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #E38B5B;
            font-size: 2em;
            font-weight: bold;
        }

        
    </style>
</head>

<body>
   <!-- Barra de navegación -->
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <img src="{{ asset('img/FontTrack.png') }}" alt="logo" height="70px" width="100px" class="logo" onclick="window.location.href='{{ route('users') }}'">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('users') }}">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('materials') }}">Materiales</a>
                    </li>
                <li class="nav-item"><a class="nav-link" href="{{ route('reportes.index') }}">Reportes</a></li>
                </ul>
                
                <!-- Perfil de usuario -->
                <div class="user-profile ms-auto">
                    <img src="{{ Auth::user()->foto_usuario_url ?? asset('img/usuario_default.png') }}" alt="Foto de perfil">
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
    <div class="container mt-5">
        <h2 class="mb-4">Lista de Lugares</h2>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalRegistro">Nuevo Lugar</button>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="table-responsive mt-3">
            <table class="table mt-3">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($lugares as $lugar)
                        <tr data-id="{{ $lugar->id_lugar }}">
                            <td>{{ $lugar->nombre }}</td>
                            <td>{{ $lugar->estado ?? 'Sin especificar' }}</td>
                            <td>
                                <button class="btn btn-info btnVer" data-id="{{ $lugar->id_lugar }}">Ver</button>
                                <button class="btn btn-warning btnEditar" data-id="{{ $lugar->id_lugar }}">Editar</button>
                                <button class="btn btn-danger btnEliminar"
                                    data-id="{{ $lugar->id_lugar }}">Eliminar</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Registro/Edición -->
    <div class="modal fade" id="modalRegistro" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="formLugar">
                    @csrf
                    <input type="hidden" id="lugarId">
                    <div class="modal-header">
                        <h5 class="modal-title">Registrar / Editar Lugar</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <label for="nombre">Nombre:</label>
                        <input type="text" id="nombre" name="nombre" class="form-control" required>

                        <label for="estado" class="mt-2">Estado:</label>
                        <input type="text" id="estado" name="estado" class="form-control">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Ver Lugar -->
    <div class="modal fade" id="modalVer" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detalles del Lugar</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Nombre:</strong> <span id="verNombre"></span></p>
                    <p><strong>Estado:</strong> <span id="verEstado"></span></p>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            $('.btnEditar').click(function () {
                let id = $(this).data('id');
                $.get(`/lugares/${id}/edit`, function (response) {
                    $('#lugarId').val(response.data.id_lugar);
                    $('#nombre').val(response.data.nombre);
                    $('#estado').val(response.data.estado);
                    $('#modalRegistro').modal('show');
                });
            });

            $('.btnVer').click(function () {
                let id = $(this).data('id');
                $.get(`/lugares/${id}`, function (response) {
                    $('#verNombre').text(response.data.nombre);
                    $('#verEstado').text(response.data.estado || 'Sin especificar');
                    $('#modalVer').modal('show');
                });
            });

            $('.btnEliminar').click(function () {
                let id = $(this).data('id');
                if (confirm('¿Seguro que quieres eliminar este lugar?')) {
                    $.ajax({
                        url: `/lugares/${id}`,
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (response) {
                            alert(response.message);
                            location.reload();
                        },
                        error: function (xhr) {
                            alert('Error al eliminar lugar: ' + xhr.responseText);
                        }
                    });
                }
            });

            $('#formLugar').submit(function (event) {
                event.preventDefault();
                let id = $('#lugarId').val();
                let url = id ? `/lugares/${id}` : `/lugares`;
                let method = id ? 'PUT' : 'POST';

                $.ajax({
                    url: url,
                    type: method,
                    data: $(this).serialize(),
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        alert(response.message);
                        location.reload();
                    },
                    error: function (xhr) {
                        alert('Error al guardar: ' + xhr.responseText);
                    }
                });
            });
        });
            // Manejar el menú desplegable del perfil de usuario
            $('.user-profile').click(function(e) {
                e.stopPropagation();
                $('.user-dropdown').toggleClass('show');
            });

            // Cerrar el menú desplegable al hacer clic fuera
            $(document).click(function() {
                $('.user-dropdown').removeClass('show');
            });

    </script>
</body>

</html>