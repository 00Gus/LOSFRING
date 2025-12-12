<?php
// includes/navbar.php
// Barra de navegación principal, se puede adaptar según el rol del usuario.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$usuario = $_SESSION['usuario'] ?? null;
$rol = $_SESSION['rol'] ?? null;
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
    <div class="container-fluid">
        <a class="navbar-brand" href="/index.php">LOS FRING</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <?php if ($usuario): ?>
                    <li class="nav-item"><a class="nav-link" href="/inventario/estado.php">Inventario</a></li>
                    <li class="nav-item"><a class="nav-link" href="/ventas/nueva.php">Ventas</a></li>
                    <li class="nav-item"><a class="nav-link" href="/compras/nueva.php">Compras</a></li>
                    <li class="nav-item"><a class="nav-link" href="/prediccion/producto.php">Predicción</a></li>
                    <?php if ($rol === 'ADMIN'): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarAdmin" role="button" data-bs-toggle="dropdown">
                                Administración
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="/productos/listar.php">Productos</a></li>
                                <li><a class="dropdown-item" href="/categorias/listar.php">Categorías</a></li>
                                <li><a class="dropdown-item" href="/proveedores/listar.php">Proveedores</a></li>
                                <li><a class="dropdown-item" href="/usuarios/listar.php">Usuarios</a></li>
                            </ul>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>
            </ul>

            <ul class="navbar-nav ms-auto">
                <?php if ($usuario): ?>
                    <li class="nav-item">
                        <span class="navbar-text me-3">
                            <?php echo htmlspecialchars($usuario); ?> (<?php echo htmlspecialchars($rol); ?>)
                        </span>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-outline-light btn-sm" href="/auth/logout.php">Cerrar sesión</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="btn btn-outline-light btn-sm" href="/auth/login.php">Iniciar sesión</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>


