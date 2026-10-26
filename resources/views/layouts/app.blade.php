<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Delicias Dulces - Sistema ERP')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @font-face {
            font-family: 'Lobster';
            src: url('{{ asset('fonts/Lobster-Regular.ttf') }}') format('truetype');
            font-style: normal;
            font-weight: 400;
            font-display: swap;
        }
        :root {
            --primary-color: #d51d3e;
            --secondary-color: #fff0e6;
            --dark-color: #571127;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #fff9f3;
        }

        .app-shell { display: flex; min-height: 100vh; }
        .main-wrap { flex: 1; min-width: 0; }

        .sidebar {
            width: 268px;
            flex: 0 0 268px;
            background: linear-gradient(180deg, #4b0c20 0%, #591027 54%, #65182d 100%);
            color: white;
            min-height: 100vh;
            height: 100vh;
            overflow-y: auto;
            padding: 18px 18px 26px;
            position: sticky;
            top: 0;
            border-right: 1px solid rgba(255, 255, 255, 0.1);
        }
        .sidebar::after {
            content: '';
            position: absolute;
            right: -27px;
            bottom: -14px;
            width: 270px;
            height: 190px;
            background: url('{{ asset('images/ilustraciones/queque-entero.png') }}') center/contain no-repeat;
            opacity: .16;
            pointer-events: none;
        }
        .sidebar-logo, .sidebar-section-title, .sidebar-menu { position: relative; z-index: 1; }

        .sidebar-logo {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0;
            margin-bottom: 22px;
            padding: 0 2px;
            text-align: center;
        }

        .sidebar-logo img { width: 132px; height: 132px; object-fit: contain; border-radius: 50%; }
        .sidebar-logo-name { margin-top: -4px; color: #fff; font-family: 'Lobster', cursive; font-size: 31px; line-height: 1.1; white-space: nowrap; }
        .sidebar-logo-tagline { margin-top: 5px; color: #ffe4dc; font-size: 11px; }
        .sidebar-logo-tagline::before, .sidebar-logo-tagline::after { content: '—'; margin: 0 5px; color: #ef7d4f; }
        .sidebar-toggle { display: none; }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            gap: 12px;
            min-height: 43px;
            padding: 10px 13px;
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            border-radius: 10px;
            margin-bottom: 2px;
            font-size: .91rem;
            transition: background-color .16s ease, color .16s ease;
        }

        .sidebar-menu a i {
            width: 19px;
            text-align: center;
            color: rgba(255, 255, 255, 0.72);
        }

        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background: linear-gradient(100deg, #ed7233, #d53618);
            color: white;
            box-shadow: inset 3px 0 0 #ffd6a0, 0 5px 12px rgba(52, 0, 5, .14);
        }

        .sidebar-menu a:hover i,
        .sidebar-menu a.active i {
            color: white;
        }

        .sidebar-section-title {
            font-size: 11px;
            color: rgba(255, 255, 255, 0.64);
            text-transform: uppercase;
            letter-spacing: .35px;
            margin: 22px 10px 8px;
            font-weight: 700;
        }

        .sidebar-logout {
            min-height: 42px;
            padding: 9px 11px !important;
            border: 0 !important;
            border-radius: 6px !important;
            color: rgba(255, 255, 255, 0.82) !important;
            font-size: .9rem !important;
        }

        .sidebar-logout:hover {
            background: rgba(255, 255, 255, 0.1) !important;
            color: #fff !important;
        }

        .topbar {
            position: relative;
            z-index: 20;
            min-height: 97px;
            background: #fffdfa;
            padding: 14px 26px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 12px rgba(120, 52, 39, 0.1);
        }
        .topbar::after { content: ''; position: absolute; top: 0; right: 180px; width: 310px; height: 100%; background: url('{{ asset('images/ilustraciones/queque-rebanado.webp') }}') center 45%/280px auto no-repeat; opacity: .25; pointer-events: none; }
        .topbar > * { position: relative; z-index: 1; }
        .topbar-brand { display: flex; align-items: center; margin: 0; color: #570c25; font-family: 'Lobster', cursive; font-size: 38px; font-weight: 400; line-height: 1.05; white-space: nowrap; }
        .topbar-brand-cake { width: 112px; height: 43px; margin: -5px -12px -5px -5px; object-fit: contain; }
        .topbar-date { display: block; margin-top: 3px; color: #87909e; font-size: 14px; }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #b51e52, #dc4a71);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        .main-content {
            min-height: calc(100vh - 97px);
            padding: 23px 26px 40px;
            background: #fffaf6;
        }
        .main-content > .alert-success { display: flex; align-items: center; gap: 15px; min-height: 63px; border: 1px solid #aad5b8; background: #eaf5ec; color: #087249; box-shadow: 0 4px 14px rgba(71, 108, 70, .06); position: relative; overflow: hidden; }
        .main-content > .alert-success::before { content: '✓'; display: grid; flex: 0 0 27px; width: 27px; height: 27px; place-items: center; border-radius: 50%; background: #078454; color: white; font-weight: 700; }
        .main-content > .alert-success::after { content: ''; position: absolute; right: 45px; top: -45px; width: 220px; height: 140px; background: url('{{ asset('images/ilustraciones/queque-entero.png') }}') center/contain no-repeat; opacity: .15; pointer-events: none; }
        .main-content > .alert-success .btn-close { margin-left: auto; }

        .page-header {
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-title {
            font-size: 28px;
            font-weight: bold;
            color: var(--dark-color);
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: #b02d5f;
            border-color: #b02d5f;
        }

        .card {
            border: none;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .card-header {
            background-color: var(--secondary-color);
            border-bottom: 1px solid #e0d0ca;
        }

        .table-hover tbody tr:hover {
            background-color: var(--secondary-color);
        }

        .badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
        }

        .badge-pending {
            background-color: #ffc107;
            color: #000;
        }

        .badge-process {
            background-color: #0dcaf0;
            color: #000;
        }

        .badge-completed {
            background-color: #198754;
            color: white;
        }

        .badge-cancelled {
            background-color: #dc3545;
            color: white;
        }

        .alert {
            border-radius: 10px;
        }

        .app-pagination {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
            padding: 16px 0 2px;
            border-top: 1px solid #edf0f4;
            margin-top: 12px;
        }

        .app-pagination__summary { color: #71809a; font-size: .86rem; }
        .app-pagination__summary strong { color: #394861; }
        .app-pagination__links { display: flex; align-items: center; gap: 6px; list-style: none; margin: 0; padding: 0; }
        .app-pagination__link, .app-pagination__ellipsis {
            min-width: 34px;
            height: 34px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            padding: 0 10px;
            font-size: .84rem;
            font-weight: 700;
        }
        .app-pagination__link { color: #52617a; background: #f5f7fa; border: 1px solid #e4e9f0; text-decoration: none; transition: .15s ease; }
        .app-pagination__link:hover { color: #fff; background: #c7436f; border-color: #c7436f; }
        .app-pagination__link--active { color: #fff; background: #c7436f; border-color: #c7436f; }
        .app-pagination__link--disabled { color: #b5bfcd; background: #fafbfc; border-color: #edf0f4; cursor: not-allowed; }
        .app-pagination__ellipsis { color: #8e9bb0; }

        @media (max-width: 575.98px) {
            .app-pagination { align-items: flex-start; flex-direction: column; }
            .app-pagination__links { gap: 4px; }
        }

        .form-control,
        .form-select {
            border-radius: 8px;
            border: 1px solid #ddd;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(199, 67, 111, 0.25);
        }

        @media (max-width: 991.98px) {
            .app-shell { display: block; }
            .sidebar { position: relative; width: 100%; height: auto; min-height: 0; padding: 12px 18px; overflow: visible; }
            .sidebar::after { display: none; }
            .sidebar-logo { flex-direction: row; justify-content: flex-start; gap: 10px; margin: 0; text-align: left; }
            .sidebar-logo img { width: 54px; height: 54px; }
            .sidebar-logo-name { margin: 0; font-size: 25px; }
            .sidebar-logo-tagline { display: none; }
            .sidebar-toggle { display: inline-flex; align-items: center; justify-content: center; width: 40px; height: 40px; margin-left: auto; border: 1px solid #bc7892; border-radius: 8px; background: transparent; color: white; }
            .sidebar:not(.sidebar-open) > .sidebar-section-title, .sidebar:not(.sidebar-open) > .sidebar-menu { display: none; }
            .topbar { min-height: 78px; }
            .topbar::after { display: none; }
        }
        @media (max-width: 575.98px) {
            .topbar { gap: 10px; padding: 12px 16px; }
            .topbar-brand { font-size: 28px; }
            .topbar-brand-cake { width: 67px; height: 32px; margin-right: -8px; }
            .topbar-date { font-size: 11px; }
            .topbar-right { gap: 11px; }
            .user-info > div:last-child { display: none; }
            .main-content { padding: 18px 14px 30px; }
        }
    </style>
    @yield('styles')
</head>
<body>
    <div class="app-shell">
        <!-- Sidebar -->
        <aside class="sidebar" id="appSidebar">
            <div class="sidebar-logo">
                <img src="{{ asset('images/logo-delicias-dulces.webp') }}" alt="Logo de Delicias Dulces" width="132" height="132">
                <span class="sidebar-logo-name">Delicias Dulces</span>
                <span class="sidebar-logo-tagline">♥ Repostería con amor ♥</span>
                <button class="sidebar-toggle" type="button" aria-controls="appSidebar" aria-expanded="false" aria-label="Abrir menú" onclick="const open = this.closest('.sidebar').classList.toggle('sidebar-open'); this.setAttribute('aria-expanded', String(open)); this.setAttribute('aria-label', open ? 'Cerrar menú' : 'Abrir menú');"><i class="fas fa-bars" aria-hidden="true"></i></button>
            </div>

            <div class="sidebar-section-title">Principal</div>
            <div class="sidebar-menu">
                <a href="{{ route('dashboard') }}" class="@if(request()->routeIs('dashboard')) active @endif">
                    <i class="fas fa-chart-line"></i>
                    <span>Dashboard</span>
                </a>
            </div>

            <div class="sidebar-section-title">Clientes</div>
<div class="sidebar-menu">
    <a href="{{ route('clientes.index') }}" class="@if(request()->routeIs('clientes.*')) active @endif">
        <i class="fas fa-users"></i>
        <span>Lista de Clientes</span>
    </a>
</div>

            <div class="sidebar-section-title">Pedidos</div>
            <div class="sidebar-menu">
                <a href="{{ route('pedidos.index') }}" class="@if(request()->routeIs('pedidos.*')) active @endif">
                    <i class="fas fa-receipt"></i>
                    <span>Gestión de Pedidos</span>
                </a>
            </div>

            <div class="sidebar-menu">
                <a href="{{ route('ventas.index') }}" class="@if(request()->routeIs('ventas.*')) active @endif">
                    <i class="fas fa-coins"></i>
                    <span>Ventas</span>
                </a>
            </div>

            @if(Auth::user()->esAdministrador())
                <div class="sidebar-section-title">Administración</div>
                <div class="sidebar-menu">
                    <a href="{{ route('empleados.index') }}" class="@if(request()->routeIs('empleados.*')) active @endif">
                        <i class="fas fa-user-group"></i>
                        <span>Empleados registrados</span>
                    </a>
                </div>
                <div class="sidebar-menu">
                    <a href="{{ route('register') }}" class="@if(request()->routeIs('register')) active @endif">
                        <i class="fas fa-user-plus"></i>
                        <span>Nuevo empleado</span>
                    </a>
                </div>
                <div class="sidebar-menu">
                    <a href="{{ route('reportes.index') }}" class="@if(request()->routeIs('reportes.*')) active @endif">
                        <i class="fas fa-chart-bar"></i>
                        <span>Reportes</span>
                    </a>
                </div>
                <div class="sidebar-section-title">Inventario</div>
                <div class="sidebar-menu">
                    <a href="{{ route('productos.index') }}" class="@if(request()->routeIs('productos.*')) active @endif">
                        <i class="fas fa-box"></i>
                        <span>Productos</span>
                    </a>
                    <a href="{{ route('insumos.index') }}" class="@if(request()->routeIs('insumos.*')) active @endif">
                        <i class="fas fa-flask"></i>
                        <span>Insumos</span>
                    </a>
                    <a href="{{ route('categorias.index') }}" class="@if(request()->routeIs('categorias.*')) active @endif">
                        <i class="fas fa-tags"></i>
                        <span>Sabores</span>
                    </a>
                </div>
            @endif

            <div class="sidebar-section-title">Cuenta</div>
            <div class="sidebar-menu">
                <a href="{{ route('compartir') }}">
                    <i class="fas fa-qrcode"></i>
                    <span>Compartir sistema</span>
                </a>
                <a href="{{ route('profile.show') }}" class="@if(request()->routeIs('profile.*')) active @endif">
    <i class="fas fa-user-circle"></i>
    <span>Mi Perfil</span>
</a>
                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-link text-white text-start w-100 d-flex align-items-center gap-2 sidebar-logout" style="text-decoration: none;">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Cerrar Sesión</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="main-wrap">
            <!-- Topbar -->
            <div class="topbar">
                <div>
                    <h5 class="topbar-brand">Delicias Dulces <img class="topbar-brand-cake" src="{{ asset('images/ilustraciones/queque-color.png') }}" alt="" aria-hidden="true"></h5>
                    <small class="topbar-date">{{ ucfirst(now()->translatedFormat('l, d \d\e F \d\e Y')) }}</small>
                </div>
                <div class="topbar-right">
                    <div class="dropdown">
                        <i class="fas fa-bell" id="botonNotificaciones" data-bs-toggle="dropdown" aria-expanded="false" style="cursor: pointer; font-size: 18px; position: relative;">
                            @if($notificaciones->count() > 0)
                                <span style="position: absolute; top: -4px; right: -6px; width: 8px; height: 8px; background: #dc3545; border-radius: 50%;"></span>
                            @endif
                        </i>
                        <div class="dropdown-menu dropdown-menu-end p-0" style="width: 340px; max-height: 420px; overflow-y: auto;" aria-labelledby="botonNotificaciones">
                            <div class="p-3" style="border-bottom: 1px solid #e0d0ca;">
                                <strong><i class="fas fa-bell" style="color: #d4a300;"></i> Notificaciones</strong>
                            </div>
                            @forelse($notificaciones as $notificacion)
                                <a href="{{ $notificacion['url'] ?? '#' }}" class="p-3 d-flex text-decoration-none text-reset" style="gap: 10px; border-bottom: 1px solid #f0e8e4;">
                                    <i class="fas {{ $notificacion['icono'] }}" style="color: {{ $notificacion['color'] }}; font-size: 12px; margin-top: 5px;"></i>
                                    <div>
                                        <div style="font-size: 14px;">{{ $notificacion['titulo'] }}</div>
                                        @if(!empty($notificacion['detalle']))
                                            <div class="text-muted" style="font-size: 12px;">{{ $notificacion['detalle'] }}</div>
                                        @endif
                                        <small class="text-muted">{{ $notificacion['fecha']->diffForHumans() }}</small>
                                    </div>
                                </a>
                            @empty
                                <div class="p-3 text-center text-muted">Sin notificaciones</div>
                            @endforelse
                        </div>
                    </div>
                    <a href="{{ route('profile.show') }}" class="user-info" style="text-decoration: none; color: inherit; cursor: pointer;">
                        <div class="user-avatar">
                            {{ strtoupper(substr(Auth::user()->nombre, 0, 1)) }}
                        </div>
                        <div>
                            <small class="text-muted">{{ Auth::user()->cargo }}</small>
                            <div class="font-weight-bold">{{ Auth::user()->nombre }}</div>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Content -->
            <div class="main-content">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @if(request()->routeIs('dashboard', 'reportes.index'))
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @endif
    @yield('scripts')
</body>
</html>
