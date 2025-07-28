<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Lista de Usuarios</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
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

        /* Estilo para botones de modales */
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
                        <a class="nav-link" href="{{ route('materials') }}">Materiales</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('lugares.index') }}">Lugares</a>
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

    <div class="container mt-3">
        <h2 class="mb-4">Lista de Usuarios</h2>
        
        <!-- Buscador de usuarios -->
        <div class="search-container">
            <div class="row">
                <div class="col-md-8 offset-md-2">
                    <div class="input-group">
                        <input type="text" id="searchUsers" class="form-control search-input" placeholder="Buscar usuarios por nombre, correo o tipo...">
                        <button class="btn btn-outline-light" type="button">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botón para abrir el modal de registro -->
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalRegistro" id="btnNuevoUsuario">
            <i class="bi bi-person-plus"></i> Registrar Usuario
        </button>

        <div class="table-responsive mt-3">
            <table class="table" id="tablaUsuarios">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Tipo</th>
                        <th>Foto</th>
                        <th>Lugar</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="userTableBody">
                    @foreach($usuarios as $usuario)
                        <tr data-id="{{ $usuario->id_usuario }}">
                            <td>{{ $usuario->nombre }}</td>
                            <td>{{ $usuario->correo }}</td>
                            <td>{{ $usuario->tipo_usuario == 1 ? 'Admin' : 'Usuario' }}</td>
                            <td>
                                <img src="{{ $usuario->foto_usuario_url ?? asset('img/usuario_default.png') }}" alt="Foto" width="50" class="rounded">
                            </td>
                            <td>{{ optional($usuario->lugar)->nombre ?? 'Sin asignar' }}</td>
                            <td>
                                <button class="btn btn-info btn-sm btnVer" data-id="{{ $usuario->id_usuario }}">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button class="btn btn-warning btn-sm btnEditar" data-id="{{ $usuario->id_usuario }}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-danger btn-sm btnEliminar" data-id="{{ $usuario->id_usuario }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        <nav aria-label="Navegación de usuarios">
            <ul class="pagination" id="userPagination">
                <!-- Se generará dinámicamente con JavaScript -->
            </ul>
        </nav>
    </div>

    <!-- Modal Registro/Edición -->
    <div class="modal fade" id="modalRegistro" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="formRegistro" enctype="multipart/form-data">
                    <input type="hidden" id="usuarioId" name="id_usuario">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle">Registrar Usuario</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre:</label>
                            <input type="text" id="nombre" name="nombre" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="correo" class="form-label">Correo:</label>
                            <input type="email" id="correo" name="correo" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Contraseña:</label>
                            <input type="password" id="password" name="password" class="form-control" placeholder="Ingresa una contraseña de 6 caracteres">
                        </div>
                        <div class="mb-3">
                            <label for="tipo_usuario" class="form-label">Tipo de Usuario:</label>
                            <select id="tipo_usuario" name="tipo_usuario" class="form-control">
                                <option value="1">Admin</option>
                                <option value="2">Usuario</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="foto_usuario" class="form-label">Seleccionar Foto:</label>
                            <input type="file" id="foto_usuario" name="foto_usuario" class="form-control" accept="image/png, image/jpeg">
                            <div id="imagePreview" class="mt-3 text-center" style="display: none;">
                                <img id="previewImg" src="" alt="Vista previa" class="image-preview">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="id_lugar" class="form-label">Seleccionar Lugar:</label>
                            <select id="id_lugar" name="id_lugar" class="form-control">
                                @foreach($lugares as $lugar)
                                    <option value="{{ $lugar->id_lugar }}">{{ $lugar->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Ver Usuario -->
    <div class="modal fade" id="modalVer" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detalles del Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-start">
                    <div class="row">
                        <div class="col-md-8">
                            <p><strong>Nombre:</strong> <span id="verNombre"></span></p>
                            <p><strong>Correo:</strong> <span id="verCorreo"></span></p>
                            <p><strong>Tipo:</strong> <span id="verTipo"></span></p>
                            <p><strong>Lugar:</strong> <span id="verLugar"></span></p>
                        </div>
                        <div class="col-md-4 text-center">
                            <img id="verFoto" src="" alt="Foto del usuario" class="image-preview img-fluid">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            // Variables para paginación
            let currentPage = 1;
            const itemsPerPage = 10;
            let filteredUsers = [];
            let allUsers = [];

            // Manejar el menú desplegable del perfil de usuario
            $('.user-profile').click(function(e) {
                e.stopPropagation();
                $('.user-dropdown').toggleClass('show');
            });

            // Cerrar el menú desplegable al hacer clic fuera
            $(document).click(function() {
                $('.user-dropdown').removeClass('show');
            });

            // Recopilar todos los usuarios al cargar la página
            function initializeUsers() {
                allUsers = [];
                $('#userTableBody tr').each(function() {
                    const row = $(this);
                    allUsers.push({
                        id: row.data('id'),
                        nombre: row.find('td:eq(0)').text(),
                        correo: row.find('td:eq(1)').text(),
                        tipo: row.find('td:eq(2)').text(),
                        foto: row.find('td:eq(3)').html(),
                        lugar: row.find('td:eq(4)').text(),
                        acciones: row.find('td:eq(5)').html(),
                        element: row[0].outerHTML
                    });
                });
                filteredUsers = [...allUsers];
                updatePagination();
                showPage(1);
            }

            // Función de búsqueda
            $('#searchUsers').on('keyup', function() {
                const searchTerm = $(this).val().toLowerCase();
                
                if (searchTerm === '') {
                    filteredUsers = [...allUsers];
                } else {
                    filteredUsers = allUsers.filter(user => {
                        return user.nombre.toLowerCase().includes(searchTerm) ||
                               user.correo.toLowerCase().includes(searchTerm) ||
                               user.tipo.toLowerCase().includes(searchTerm) ||
                               user.lugar.toLowerCase().includes(searchTerm);
                    });
                }
                
                currentPage = 1;
                updatePagination();
                showPage(1);
            });

            // Mostrar página específica
            function showPage(page) {
                currentPage = page;
                const startIndex = (page - 1) * itemsPerPage;
                const endIndex = startIndex + itemsPerPage;
                const pageUsers = filteredUsers.slice(startIndex, endIndex);

                $('#userTableBody').empty();
                pageUsers.forEach(user => {
                    const row = $(user.element);
                    $('#userTableBody').append(row);
                });

                // Reattach event listeners
                attachEventListeners();
                updatePaginationButtons();
            }

            // Actualizar paginación
            function updatePagination() {
                const totalPages = Math.ceil(filteredUsers.length / itemsPerPage);
                let paginationHtml = '';

                // Botón anterior
                paginationHtml += `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                    <a class="page-link" href="#" data-page="${currentPage - 1}">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                </li>`;

                // Números de página
                for (let i = 1; i <= totalPages; i++) {
                    if (i === 1 || i === totalPages || (i >= currentPage - 2 && i <= currentPage + 2)) {
                        paginationHtml += `<li class="page-item ${i === currentPage ? 'active' : ''}">
                            <a class="page-link" href="#" data-page="${i}">${i}</a>
                        </li>`;
                    } else if (i === currentPage - 3 || i === currentPage + 3) {
                        paginationHtml += '<li class="page-item disabled"><span class="page-link">...</span></li>';
                    }
                }

                // Botón siguiente
                paginationHtml += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                    <a class="page-link" href="#" data-page="${currentPage + 1}">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>`;

                $('#userPagination').html(paginationHtml);
            }

            // Actualizar botones de paginación
            function updatePaginationButtons() {
                $('#userPagination .page-link').off('click').on('click', function(e) {
                    e.preventDefault();
                    const page = parseInt($(this).data('page'));
                    if (page && page !== currentPage) {
                        showPage(page);
                    }
                });
            }

            // Adjuntar event listeners
            function attachEventListeners() {
                // Evento para ver detalles del usuario
                $('.btnVer').off('click').on('click', function () {
                    let id = $(this).data('id');
                    $.get(`/modal/user/${id}`, function (response) {
                        let data = response.data;
                        $('#verNombre').text(data.nombre);
                        $('#verCorreo').text(data.correo);
                        $('#verTipo').text(data.tipo_usuario == 1 ? 'Admin' : 'Usuario');
                        $('#verLugar').text(data.lugar ? data.lugar.nombre : 'Sin asignar');
                        let fotoUrl = data.foto_usuario_url || '{{ asset('img/usuario_default.png') }}';
                        $('#verFoto').attr('src', fotoUrl);
                        $('#modalVer').modal('show');
                    });
                });

                // Evento para cargar datos en el modal de edición
                $('.btnEditar').off('click').on('click', function () {
                    let id = $(this).data('id');
                    $.get(`/modal/edit_user/${id}`, function (response) {
                        let data = response.data;
                        $('#modalTitle').text('Editar Usuario');
                        $('#usuarioId').val(data.id_usuario);
                        $('#nombre').val(data.nombre);
                        $('#correo').val(data.correo);
                        $('#tipo_usuario').val(data.tipo_usuario);
                        $('#id_lugar').val(data.id_lugar);
                        $('#password').val('');
                        
                        // Mostrar imagen actual si existe
                        if (data.foto_usuario_url) {
                            $('#imagePreview').show();
                            $('#previewImg').attr('src', data.foto_usuario_url);
                        } else {
                            $('#imagePreview').hide();
                        }
                        
                        $('#modalRegistro').modal('show');
                    });
                });

                // Evento para eliminar usuario
                $('.btnEliminar').off('click').on('click', function () {
                    let id = $(this).data('id');
                    if (confirm('¿Estás seguro de que quieres eliminar este usuario?')) {
                        $.ajax({
                            url: `/modal/delete_user/${id}`,
                            type: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function (response) {
                                alert(response.message);
                                location.reload();
                            }
                        });
                    }
                });
            }

            // Vista previa de imagen
            $('#foto_usuario').on('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('#previewImg').attr('src', e.target.result);
                        $('#imagePreview').show();
                    };
                    reader.readAsDataURL(file);
                } else {
                    $('#imagePreview').hide();
                }
            });

            // Envío del formulario (crear/actualizar usuario) mediante AJAX
            $('#formRegistro').submit(function (e) {
                e.preventDefault();
                let usuarioId = $('#usuarioId').val();
                let formData = new FormData(this);
                let url = usuarioId ? `/modal/update_user/${usuarioId}` : `/modal/register_user`;
                let method = usuarioId ? 'PUT' : 'POST';

                // Para métodos PUT en FormData
                if (method === 'PUT') {
                    formData.append('_method', 'PUT');
                }

                $.ajax({
                    url: url,
                    type: 'POST', // Siempre POST para FormData
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        alert(response.message);
                        $('#modalRegistro').modal('hide');
                        location.reload();
                    },
                    error: function (xhr) {
                        let errors = xhr.responseJSON?.errors || {general: 'Error al procesar la solicitud'};
                        let errorMsg = '';
                        $.each(errors, function (key, value) {
                            errorMsg += (Array.isArray(value) ? value.join(', ') : value) + '\n';
                        });
                        alert(errorMsg);
                    }
                });
            });

            // Al abrir el modal para nuevo usuario, se resetea el formulario
            $('#btnNuevoUsuario').click(function () {
                $('#modalTitle').text('Registrar Usuario');
                $('#formRegistro')[0].reset();
                $('#usuarioId').val('');
                $('#imagePreview').hide();
            });

            // Inicializar la tabla con paginación
            initializeUsers();
        });
    </script>
</body>

</html>