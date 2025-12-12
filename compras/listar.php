<?php
// compras/listar.php
// Lista de compras realizadas.

require_once __DIR__ . '/../helpers/auth.php';
require_admin();
require_once __DIR__ . '/../config/db.php';

$pdo = getPDO();

$stmt = $pdo->query('
    SELECT c.id_compra, c.fecha, c.total,
           p.nombre AS proveedor,
           u.usuario AS usuario
    FROM compras c
    JOIN proveedores p ON c.id_proveedor = p.id_proveedor
    JOIN usuarios u ON c.id_usuario = u.id_usuario
    ORDER BY c.fecha DESC
    LIMIT 100
');
$compras = $stmt->fetchAll();

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Compras</h1>
        <a href="nueva.php" class="btn btn-primary btn-sm">Nueva compra</a>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table table-sm table-striped align-middle">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Fecha</th>
                    <th>Proveedor</th>
                    <th>Usuario</th>
                    <th>Total</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($compras as $c): ?>
                    <tr>
                        <td><?php echo (int)$c['id_compra']; ?></td>
                        <td><?php echo htmlspecialchars($c['fecha']); ?></td>
                        <td><?php echo htmlspecialchars($c['proveedor']); ?></td>
                        <td><?php echo htmlspecialchars($c['usuario']); ?></td>
                        <td>$<?php echo number_format((float)$c['total'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>












