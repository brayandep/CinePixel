<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Cine Pixel')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

      {{-- Bootstrap 5 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    {{-- Aquí puedes poner un CSS general, por ahora usamos login.css --}}
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body>

<header class="site-header">
    <div class="header-inner">
        <div class="header-logo">Cine Pixel</div>

        <nav class="header-nav">
            <a href="#">Quiénes somos</a>
            <a href="#">Ubicación</a>
            <a href="#">Cómo hacer una reserva</a>
            <a href="#">Contacto</a>
        </nav>
    </div>
</header>

{{-- Si el usuario está logueado, mostramos layout con sidebar --}}
@if(auth()->check())
    <div class="layout-shell">
        <aside class="sidebar">
            <div class="sidebar-title">
                Panel Cine Pixel
            </div>

            <nav class="sidebar-nav">
                <a href="{{ route('dashboard') }}">🏠 Inicio</a>
                <a href="{{ route('reservas.index') }}">🎬 Reservar salas</a>
                <a href="{{ route('reports.sales') }}">📊 Ver reportes de ventas</a>
                <a href="{{ route('products.create') }}">➕ Registrar artículo de tienda de regalos</a>
                <a href="{{ route('products.index') }}">🛒 Ver tienda</a>
                <a href="#">📑 Ver registros de venta</a>
            </nav>
        </aside>

        <main class="site-main with-sidebar">
            @yield('content')
        </main>
    </div>
@else
    {{-- Para login u otras vistas sin sesión --}}
    <main class="site-main">
        @yield('content')
    </main>
@endif

<footer class="site-footer">
    <div class="footer-inner">

        <div class="footer-column">
            <h4>Cine Pixel</h4>
            <p>Tu espacio para vivir las mejores historias en la gran pantalla.</p>
        </div>

        <div class="footer-column">
            <h4>Contacto</h4>
            <p>Correo:
                <a href="mailto:cinepixel40@gmail.com">cinepixel40@gmail.com</a>
            </p>
            <p>Teléfono: +591 68541929</p>
        </div>

        <div class="footer-column">
            <h4>Ubicación</h4>
            <p>Av. Aroma y Ayacucho<br>Cochabamba - Bolivia</p>
        </div>

        <div class="footer-column">
            <h4>Redes sociales</h4>
            <p>
                <a href="#">Facebook @cinepixel</a><br>
                <a href="#">TikTok @Cine Pixel</a><br>
            </p>
        </div>
    </div>

    <div class="footer-bottom">
        © {{ date('Y') }} Cine Pixel. Todos los derechos reservados.
    </div>
</footer>

</body>
</html>
