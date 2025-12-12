<?php
// ventas/listar.php
// Lista de ventas, filtrables por fecha.

require_once __DIR__ . '/../helpers/auth.php';
require_login();
require_once __DIR__ . '/../config/db.php';

$pdo = getPDO();

$fecha_desde = $_GET['desde'] ?? '';
$fecha_hasta = $_GET['hasta'] ?? '';

$where = [];
$params = [];

if ($fecha_desde !== '') {
    $where[] = 'DATE(v.fecha) >= :desde';
    $params[':desde'] = $fecha_desde;
}
if ($fecha_hasta !== '') {
    $where[] = 'DATE(v.fecha) <= :hasta';
    $params[':hasta'] = $fecha_hasta;
}

$sql = '
    SELECT v.id_venta, v.fecha, v.total, v.forma_pago, u.usuario
    FROM ventas v
    JOIN usuarios u ON v.id_usuario = u.id_usuario
';
if ($where) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}
$sql .= ' ORDER BY v.fecha DESC LIMIT 200';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$ventas = $stmt->fetchAll();

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Ventas</h1>
        <a href="nueva.php" class="btn btn-primary btn-sm">Nueva venta</a>
    </div>

    <form class="row g-2 mb-3">
        <div class="col-md-3">
            <label for="desde" class="form-label">Desde</label>
            <input type="date" id="desde" name="desde" class="form-control"
                   value="<?php echo htmlspecialchars($fecha_desde); ?>">
        </div>
        <div class="col-md-3">
            <label for="hasta" class="form-label">Hasta</label>
            <input type="date" id="hasta" name="hasta" class="form-control"
                   value="<?php echo htmlspecialchars($fecha_hasta); ?>">
        </div>
        <div class="col-md-3 d-flex align-items-end">
            <button type="submit" class="btn btn-secondary btn-sm me-2">Filtrar</button>
            <a href="listar.php" class="btn btn-outline-secondary btn-sm">Limpiar</a>
        </div>
    </form>

    <div class="card">
        <div class="card-body">
            <table class="table table-sm table-striped align-middle">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Fecha</th>
                    <th>Usuario</th>
                    <th>Forma pago</th>
                    <th>Total</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($ventas as $v): ?>
                    <tr>
                        <td><?php echo (int)$v['id_venta']; ?></td>
                        <td><?php echo htmlspecialchars($v['fecha']); ?></td>
                        <td><?php echo htmlspecialchars($v['usuario']); ?></td>
                        <td><?php echo htmlspecialchars($v['forma_pago']); ?></td>
                        <td>$<?php echo number_format((float)$v['total'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>







