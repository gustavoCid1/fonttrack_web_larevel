<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Reportes de Fallas - FontTrack</title>
    <link rel="icon" href="{{ asset('img/logo.png') }}" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        /* Animaciones modales */
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

        .modal.show .modal-dialog {
            animation: modalBubbleIn .6s cubic-bezier(.68, -.55, .265, 1.55);
        }

        .modal-content {
            overflow: hidden;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, .3);
        }

        .modal-backdrop {
            background: linear-gradient(45deg, rgba(0, 0, 0, .5), rgba(0, 0, 0, .7));
            backdrop-filter: blur(3px);
        }

        /* Navbar */
        .navbar {
            background-color: #F6B88F;
            box-shadow: 0 4px 8px rgba(0, 0, 0, .15);
            padding: 12px 20px;
            border-bottom: 4px solid #E38B5B;
        }

        .navbar .logo {
            cursor: pointer;
            transition: .3s ease;
        }

        .navbar .logo:hover {
            transform: scale(1.05);
            filter: brightness(1.1);
        }

        .navbar .navbar-brand {
            color: #634D3B;
            font-weight: bold;
            font-size: 1.6em;
            transition: .3s;
        }

        .navbar .navbar-brand:hover {
            color: #E38B5B;
        }

        .navbar .nav-link {
            color: #634D3B;
            font-size: 1em;
            font-weight: bold;
            padding: 10px 15px;
            transition: .3s ease;
        }

        .navbar .nav-link:hover {
            background: rgba(227, 139, 91, .2);
            border-radius: 6px;
            transform: scale(1.05);
        }

        .navbar-toggler-icon {
            filter: invert(50%);
        }

        /* User dropdown */
        .user-profile {
            display: flex;
            align-items: center;
            position: relative;
            cursor: pointer;
        }

        .user-profile img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
            border: 2px solid #E38B5B;
        }

        .user-name {
            font-weight: bold;
            color: #634D3B;
        }

        .user-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, .15);
            padding: 10px 0;
            min-width: 180px;
            display: none;
            z-index: 1000;
        }

        .user-dropdown.show {
            display: block;
            animation: fadeIn .3s ease;
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

        .user-dropdown a {
            display: block;
            padding: 8px 15px;
            color: #634D3B;
            text-decoration: none;
            transition: .2s;
        }

        .user-dropdown a:hover {
            background: rgba(227, 139, 91, .1);
            color: #E38B5B;
        }

        /* Body & Container */
        body {
            font-family: Arial, sans-serif;
            background-color: #FCE8D5;
            color: #634D3B;
            margin: 0;
            padding: 0;
            text-align: center;
        }

        .container {
            background: #FFF;
            margin: 20px auto;
            padding: 30px;
            max-width: 1100px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, .1);
        }

        h2 {
            font-size: 2em;
            color: #E38B5B;
            margin-bottom: 1rem;
        }

        /* Buscador */
        .search-container {
            background: linear-gradient(135deg, #F6B88F, #E38B5B);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, .1);
        }

        .search-container .input-group {
            max-width: 600px;
            margin: 0 auto;
        }

        .search-input {
            border: none;
            border-radius: 25px;
            padding: 12px 20px;
            font-size: 16px;
            box-shadow: inset 0 2px 5px rgba(0, 0, 0, .1);
            transition: .3s;
        }

        .search-input:focus {
            outline: none;
            box-shadow: 0 0 20px rgba(227, 139, 91, .5);
            transform: scale(1.02);
        }

        /* Tabla */
        .table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .table thead {
            background: #F6B88F;
            color: #fff;
        }

        .table th,
        .table td {
            padding: 15px;
        }

        .table-hover tbody tr:hover {
            background: rgba(246, 184, 143, .1);
            transform: scale(1.01);
            transition: .3s;
        }

        /* Botones */
        .btn {
            font-size: 1.2em;
            font-weight: bold;
            border-radius: 8px;
            padding: 12px 24px;
            transition: .3s ease;
        }

        .btn-info {
            background: #88C0D0;
            border: none;
            color: #fff;
        }

        .btn-warning {
            background: #E5A34D;
            border: none;
            color: #fff;
        }

        .btn-danger {
            background: #D9534F;
            border: none;
            color: #fff;
        }

        /* Paginación */
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
            background: #E38B5B;
            color: #fff;
            border-radius: 8px;
            font-weight: bold;
            transition: .3s;
            border: 2px solid transparent;
        }

        .pagination li a:hover {
            background: #D1784C;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(227, 139, 91, .4);
        }

        .pagination .active span {
            background: #F6B88F;
            border: 2px solid #E38B5B;
            transform: scale(1.1);
        }

        .pagination .disabled span {
            background: #ccc;
            color: #666;
        }

        /* Iconos más pequeños */
        .btn i,
        .nav-link i,
        .navbar-brand i {
            font-size: 0.75rem;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <img src="{{ asset('img/FontTrack.png') }}" alt="logo" width="100" height="70" class="logo me-2"
                onclick="location.href='{{ route('reportes.index') }}'">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="{{ route('materials') }}">Materiales</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('lugares.index') }}">Lugares</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('users') }}"></a></li>
                </ul>
                <div class="user-profile" onclick="$('.user-dropdown').toggleClass('show')">
                    <img src="{{ Auth::user()->foto_usuario_url ?? asset('img/usuario_default.png') }}" alt="Perfil">
                    <span class="user-name">{{ Auth::user()->nombre }}</span>
                    <div class="user-dropdown">
                        <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="bi bi-box-arrow-right"></i> Cerrar sesión
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST">@csrf</form>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mt-3">
        <h2>Reportes de Fallas</h2>

        <!-- Buscador -->
        <div class="search-container mb-3">
            <div class="input-group">
                <input type="text" id="searchFallas" class="search-input" placeholder="Buscar fallas…">
                <button class="btn btn-outline-light"><i class="bi bi-search"></i></button>
            </div>
        </div>

        <!-- Tabla de Fallas -->
        <div class="table-responsive">
            <table class="table table-hover" id="tablaFallas">
                <thead>
                    <tr>
                        <th>Lugar</th>
                        <th>Fecha</th>
                        <th>Conductor</th>
                        <th>Descripción</th>
                        <th>Materiales</th>
                        <th>Cant.</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="bodyFallas">
                    @foreach($fallas as $falla)
                        <tr data-id="{{ $falla->id }}">
                            <td>{{ optional($falla->lugar)->nombre }}</td>
                            <td>{{ $falla->fecha }}</td>
                            <td>{{ $falla->nombre_conductor }}</td>
                            <td>{{ Str::limit($falla->descripcion, 30) }}</td>
                            <td>{{ Str::limit($falla->material, 25) }}</td>
                            <td>{{ $falla->cantidad }}</td>
                            <td>
                                <a href="{{ route('fallas.pdf', $falla->id) }}" class="btn btn-info btn-sm me-1">
                                    <i class="bi bi-file-earmark-pdf-fill"></i>
                                </a>
                                <button class="btn btn-warning btn-sm btnEnviar" data-id="{{ $falla->id }}">
                                    <i class="bi bi-envelope-fill"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        <nav>
            <ul class="pagination" id="fallaPagination"></ul>
        </nav>
    </div>

    <!-- Modal: Enviar Correo -->
    <div class="modal fade" id="modalEnviar" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <form id="formEnviar" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Enviar Reporte</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4">
                    <input type="hidden" id="fallaId">
                    <label class="form-label">Correo Destino</label>
                    <input type="email" id="correoDestino" name="correo_destino" class="form-control"
                        placeholder="usuario@dominio.com" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Enviar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        $(function () {
            // inicializar filas
            let all = $('#bodyFallas tr').toArray().map(r => ({ html: r.outerHTML, id: $(r).data('id') })),
                filtered = [...all], current = 1, perPage = 10;

            function showPage(page) {
                current = page;
                let start = (page - 1) * perPage, end = start + perPage;
                $('#bodyFallas').html(filtered.slice(start, end).map(u => u.html).join(''));
                renderPagination(); attachListeners();
            }
            function renderPagination() {
                let total = Math.ceil(filtered.length / perPage), nav = '';
                nav += `<li class="page-item ${current === 1 ? 'disabled' : ''}">
                  <a class="page-link" data-page="${current - 1}"><i class="bi bi-chevron-left"></i></a>
                </li>`;
                for (let i = 1; i <= total; i++) {
                    if (i === 1 || i === total || (i >= current - 2 && i <= current + 2)) {
                        nav += `<li class="page-item ${i === current ? 'active' : ''}">
                      <a class="page-link" data-page="${i}">${i}</a>
                    </li>`;
                    } else if (i === current - 3 || i === current + 3) {
                        nav += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
                    }
                }
                nav += `<li class="page-item ${current === total ? 'disabled' : ''}">
                  <a class="page-link" data-page="${current + 1}"><i class="bi bi-chevron-right"></i></a>
                </li>`;
                $('#fallaPagination').html(nav);
            }
            $('#searchFallas').on('input', function () {
                let q = $(this).val().toLowerCase();
                filtered = q ? all.filter(u => u.html.toLowerCase().includes(q)) : all;
                showPage(1);
            });
            $(document).on('click', '.page-link', function (e) {
                e.preventDefault(); let p = +$(this).data('page');
                if (p > 0 && p <= Math.ceil(filtered.length / perPage)) showPage(p);
            });

            // modal enviar correo
            let modal = new bootstrap.Modal($('#modalEnviar'));
            function attachListeners() {
                $('.btnEnviar').on('click', function () {
                    $('#fallaId').val($(this).data('id'));
                    $('#correoDestino').val('');
                    modal.show();
                });
            }
            attachListeners();
            $('#formEnviar').on('submit', function (e) {
                e.preventDefault();
                let id = $('#fallaId').val(), to = $('#correoDestino').val();
                $.post(
                    `{{ route('fallas.enviar', ['id' => '__ID']) }}`.replace('__ID', id),
                    { correo_destino: to, _token: $('meta[name="csrf-token"]').attr('content') },
                    json => { alert(json.message); modal.hide(); },
                    () => alert('Error al enviar correo')
                );
            });

            showPage(1);
        });
    </script>
</body>

</html>