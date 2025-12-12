<?php
// prediccion/producto.php
// Vista para seleccionar un producto, ver su historial de ventas mensuales y una predicción simple.

require_once __DIR__ . '/../helpers/auth.php';
require_login();
require_once __DIR__ . '/../config/db.php';

$pdo = getPDO();

// Listado de productos para el selector
$productos = $pdo->query('SELECT id_producto, nombre FROM productos ORDER BY nombre')->fetchAll();

$id_producto = (int)($_GET['id_producto'] ?? 0);

$historial = [];
$labelsMeses = [];
$datosCantidades = [];
$prediccion = null;
$stockActual = null;
$sugerenciaCompra = null;
$nombreProductoSel = '';

if ($id_producto > 0) {
    // Obtener nombre y stock actual
    $stmt = $pdo->prepare('SELECT nombre, stock_actual FROM productos WHERE id_producto = :id');
    $stmt->execute([':id' => $id_producto]);
    $infoProd = $stmt->fetch();

    if ($infoProd) {
        $nombreProductoSel = $infoProd['nombre'];
        $stockActual = (int)$infoProd['stock_actual'];

        // Historial de ventas mensuales (últimos 12 meses)
        $stmtHist = $pdo->prepare('
            SELECT DATE_FORMAT(v.fecha, "%Y-%m") AS periodo,
                   SUM(dv.cantidad) AS cantidad
            FROM detalle_venta dv
            JOIN ventas v ON dv.id_venta = v.id_venta
            WHERE dv.id_producto = :id
              AND v.fecha >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
            GROUP BY DATE_FORMAT(v.fecha, "%Y-%m")
            ORDER BY periodo
        ');
        $stmtHist->execute([':id' => $id_producto]);
        $historial = $stmtHist->fetchAll();

        foreach ($historial as $fila) {
            $labelsMeses[] = $fila['periodo'];
            $datosCantidades[] = (int)$fila['cantidad'];
        }

        // Predicción sencilla: promedio de los últimos 3 meses (o menos si no hay tantos)
        $ultimos = array_slice($datosCantidades, -3);
        if (count($ultimos) > 0) {
            $prediccion = array_sum($ultimos) / count($ultimos);
            // Sugerencia de compra
            if ($stockActual !== null && $prediccion > $stockActual) {
                $sugerenciaCompra = ceil($prediccion - $stockActual);
            } else {
                $sugerenciaCompra = 0;
            }
        }
    }
}

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<div class="container">
    <h1 class="h4 mb-3">Predicción de consumo por producto</h1>

    <form class="row g-2 mb-3">
        <div class="col-md-6">
            <label for="id_producto" class="form-label">Producto</label>
            <select name="id_producto" id="id_producto" class="form-select" onchange="this.form.submit()">
                <option value="">-- Selecciona un producto --</option>
                <?php foreach ($productos as $p): ?>
                    <option value="<?php echo (int)$p['id_producto']; ?>"
                        <?php echo ($id_producto === (int)$p['id_producto']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($p['nombre']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <button type="submit" class="btn btn-secondary btn-sm">Ver</button>
        </div>
    </form>

    <?php if ($id_producto > 0 && $nombreProductoSel): ?>
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title"><?php echo htmlspecialchars($nombreProductoSel); ?></h5>
                <?php if ($stockActual !== null): ?>
                    <p class="mb-1">Stock actual: <strong><?php echo $stockActual; ?></strong></p>
                <?php endif; ?>

                <?php if ($prediccion !== null): ?>
                    <p class="mb-1">
                        Basado en los últimos
                        <strong><?php echo min(3, count($datosCantidades)); ?></strong>
                        meses, se estima que el próximo mes se venderán
                        aproximadamente <strong><?php echo number_format($prediccion, 1); ?></strong> unidades.
                    </p>
                    <p class="mb-0">
                        Sugerencia de pedido:
                        <?php if ($sugerenciaCompra > 0): ?>
                            compra recomendada de <strong><?php echo (int)$sugerenciaCompra; ?></strong> unidades.
                        <?php else: ?>
                            no se requiere compra adicional con el stock actual.
                        <?php endif; ?>
                    </p>
                <?php else: ?>
                    <p class="text-muted mb-0">
                        Aún no hay ventas suficientes para calcular una predicción.
                    </p>
                <?php endif; ?>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">Historial de consumo (ventas por mes)</h5>
                <canvas id="chartHistorial" height="120"></canvas>
            </div>
        </div>

        <script>
            (function () {
                const ctx = document.getElementById('chartHistorial');
                const labels = <?php echo json_encode($labelsMeses, JSON_UNESCAPED_UNICODE); ?>;
                const data = <?php echo json_encode($datosCantidades, JSON_UNESCAPED_UNICODE); ?>;

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Unidades vendidas por mes',
                            data: data,
                            borderColor: 'rgba(25,135,84,1)',
                            backgroundColor: 'rgba(25,135,84,0.2)',
                            tension: 0.2
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
    <?php elseif ($id_producto > 0): ?>
        <div class="alert alert-warning">No se encontró información para el producto seleccionado.</div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>







