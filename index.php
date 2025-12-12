<?php
// index.php
// Si el usuario no ha iniciado sesión, redirige a login. Si sí, muestra un dashboard sencillo.
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header('Location: /auth/login.php');
    exit;
}

require_once __DIR__ . '/config/db.php';
$pdo = getPDO();

// Datos sencillos para mostrar en el dashboard (conteos básicos)
$totalProductos = $pdo->query('SELECT COUNT(*) AS c FROM productos')->fetch()['c'] ?? 0;
$totalVentasHoy = $pdo->prepare('SELECT IFNULL(SUM(total),0) AS t FROM ventas WHERE DATE(fecha) = CURDATE()');
$totalVentasHoy->execute();
$ventasHoy = $totalVentasHoy->fetch()['t'] ?? 0;

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
?>

<div class="container">
    <div class="row">
        <div class="col-12 mb-3">
            <h1 class="h3">Resumen Diario</h1>
            <h2>Hello and welcome to the Los Pollos Hermanos family!</h2>
            
        </div>
    </div>
    <div class="row g-3">
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Productos registrados</h5>
                    <p class="display-6"><?php echo (int)$totalProductos; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Ventas del día</h5>
                    <p class="display-6">$<?php echo number_format((float)$ventasHoy, 2); ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>






