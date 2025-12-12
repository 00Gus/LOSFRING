<?php
// reportes/ventas.php
// Reportes básicos: ventas por día (rango) y top productos más vendidos.

require_once __DIR__ . '/../helpers/auth.php';
require_login();
require_once __DIR__ . '/../config/db.php';

$pdo = getPDO();

$desde = $_GET['desde'] ?? date('Y-m-01');
$hasta = $_GET['hasta'] ?? date('Y-m-d');

// Ventas por día
$stmt = $pdo->prepare('
    SELECT DATE(fecha) AS dia, SUM(total) AS total_dia
    FROM ventas
    WHERE DATE(fecha) BETWEEN :desde AND :hasta
    GROUP BY DATE(fecha)
    ORDER BY dia
');
$stmt->execute([':desde' => $desde, ':hasta' => $hasta]);
$ventasDia = $stmt->fetchAll();

// Top productos por cantidad vendida
$stmt2 = $pdo->prepare('
    SELECT p.nombre, SUM(dv.cantidad) AS cantidad_total
    FROM detalle_venta dv
    JOIN ventas v ON dv.id_venta = v.id_venta
    JOIN productos p ON dv.id_producto = p.id_producto
    WHERE DATE(v.fecha) BETWEEN :desde AND :hasta
    GROUP BY dv.id_producto, p.nombre
    ORDER BY cantidad_total DESC
    LIMIT 10
');
$stmt2->execute([':desde' => $desde, ':hasta' => $hasta]);
$topProductos = $stmt2->fetchAll();

// Preparar datos para Chart.js
$labelsDias = [];
$datosDias = [];
foreach ($ventasDia as $fila) {
    $labelsDias[] = $fila['dia'];
    $datosDias[] = (float)$fila['total_dia'];
}

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<div class="container">
    <h1 class="h4 mb-3">Reportes de ventas</h1>

    <form class="row g-2 mb-3">
        <div class="col-md-3">
            <label for="desde" class="form-label">Desde</label>
            <input type="date" id="desde" name="desde" class="form-control"
                   value="<?php echo htmlspecialchars($desde); ?>">
        </div>
        <div class="col-md-3">
            <label for="hasta" class="form-label">Hasta</label>
            <input type="date" id="hasta" name="hasta" class="form-control"
                   value="<?php echo htmlspecialchars($hasta); ?>">
        </div>
        <div class="col-md-3 d-flex align-items-end">
            <button type="submit" class="btn btn-secondary btn-sm me-2">Aplicar</button>
            <a href="ventas.php" class="btn btn-outline-secondary btn-sm">Hoy</a>
        </div>
    </form>

    <div class="row mb-4">
        <div class="col-md-7">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Ventas por día</h5>
                    <canvas id="chartVentasDia" height="120"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Top productos por cantidad</h5>
                    <table class="table table-sm align-middle">
                        <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($topProductos as $t): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($t['nombre']); ?></td>
                                <td><?php echo (int)$t['cantidad_total']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    (function () {
        const ctx = document.getElementById('chartVentasDia');
        const labels = <?php echo json_encode($labelsDias, JSON_UNESCAPED_UNICODE); ?>;
        const data = <?php echo json_encode($datosDias, JSON_UNESCAPED_UNICODE); ?>;

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Ventas por día ($)',
                    data: data,
                    backgroundColor: 'rgba(13,110,253,0.5)',
                    borderColor: 'rgba(13,110,253,1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    })();
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>












