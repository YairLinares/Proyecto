<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Delicias Dulces - Sistema ERP')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #c7436f;
            --secondary-color: #f5e6e0;
            --dark-color: #5a3d4f;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f6f4;
        }

        .sidebar {
            background-color: var(--dark-color);
            color: white;
            min-height: 100vh;
            padding: 20px;
            position: sticky;
            top: 0;
        }

        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 30px;
            font-weight: bold;
            font-size: 18px;
        }

        .sidebar-logo i {
            font-size: 24px;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 15px;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            border-radius: 5px;
            margin-bottom: 5px;
            transition: all 0.3s;
        }

        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background-color: var(--primary-color);
            color: white;
        }

        .sidebar-section-title {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.5);
            text-transform: uppercase;
            margin-top: 20px;
            margin-bottom: 10px;
            font-weight: bold;
        }

        .topbar {
            background-color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

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
            background-color: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        .main-content {
            padding: 30px;
        }

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
    </style>
    @yield('styles')
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar" style="width: 250px;">
            <div class="sidebar-logo">
                <i class="fas fa-cupcake"></i>
                <span>Delicias Dulces</span>
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
                <a href="{{ route('clientes.create') }}">
                    <i class="fas fa-user-plus"></i>
                    <span>Registrar Cliente</span>
                </a>
            </div>

            <div class="sidebar-section-title">Inventario</div>
            <div class="sidebar-menu">
                <a href="{{ route('productos.index') }}" class="@if(request()->routeIs('productos.*')) active @endif">
                    <i class="fas fa-box"></i>
                    <span>Productos</span>
                </a>
                <a href="{{ route('productos.create') }}">
                    <i class="fas fa-plus-circle"></i>
                    <span>Registrar Producto</span>
                </a>
                <a href="{{ route('insumos.index') }}" class="@if(request()->routeIs('insumos.*')) active @endif">
                    <i class="fas fa-flask"></i>
                    <span>Insumos</span>
                </a>
                <a href="{{ route('insumos.create') }}">
                    <i class="fas fa-plus-circle"></i>
                    <span>Registrar Insumo</span>
                </a>
                <a href="{{ route('categorias.index') }}" class="@if(request()->routeIs('categorias.*')) active @endif">
                    <i class="fas fa-tags"></i>
                    <span>Categorías</span>
                </a>
            </div>

            <div class="sidebar-section-title">Pedidos</div>
            <div class="sidebar-menu">
                <a href="{{ route('pedidos.index') }}" class="@if(request()->routeIs('pedidos.*')) active @endif">
                    <i class="fas fa-receipt"></i>
                    <span>Gestión de Pedidos</span>
                </a>
                <a href="{{ route('pedidos.create') }}">
                    <i class="fas fa-plus-circle"></i>
                    <span>Registrar Pedido</span>
                </a>
            </div>

            <div class="sidebar-section-title">Cuenta</div>
            <div class="sidebar-menu">
                <a href="{{ route('profile.show') }}" class="@if(request()->routeIs('profile.*')) active @endif">
    <i class="fas fa-user-circle"></i>
    <span>Mi Perfil</span>
</a>
                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-link text-white text-start w-100 d-flex align-items-center gap-2" style="text-decoration: none;">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Cerrar Sesión</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Main Content -->
        <div style="flex: 1;">
            <!-- Topbar -->
            <div class="topbar">
                <div>
                    <h5 class="mb-0">Miércoles, 1 de julio de 2026</h5>
                </div>
                <div class="topbar-right">
                    <input type="text" class="form-control" style="width: 250px;" placeholder="Buscar...">
                    <i class="fas fa-bell" style="cursor: pointer; font-size: 18px;"></i>
                    <div class="user-info">
                        <div class="user-avatar">
                            {{ strtoupper(substr(Auth::user()->nombre, 0, 1)) }}
                        </div>
                        <div>
                            <small class="text-muted">{{ Auth::user()->cargo }}</small>
                            <div class="font-weight-bold">{{ Auth::user()->nombre }}</div>
                        </div>
                    </div>
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @yield('scripts')
</body>
</html>