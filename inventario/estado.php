<?php
// inventario/estado.php
// Vista de inventario con indicador visual de stock bajo/ok.

require_once __DIR__ . '/../helpers/auth.php';
require_login(); // ADMIN y CAJERO pueden ver inventario
require_once __DIR__ . '/../config/db.php';

$pdo = getPDO();

$stmt = $pdo->query('
    SELECT p.*, c.nombre AS categoria
    FROM productos p
    LEFT JOIN categorias c ON p.id_categoria = c.id_categoria
    ORDER BY p.nombre
');
$productos = $stmt->fetchAll();

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<div class="container">
    <h1 class="h4 mb-3">Inventario</h1>
    <p class="text-muted">Estado actual del stock por producto.</p>

    <div class="card">
        <div class="card-body">
            <table class="table table-striped table-sm align-middle">
                <thead>
                <tr>
                    <th>Producto</th>
                    <th>Categoría</th>
                    <th>Stock actual</th>
                    <th>Stock mínimo</th>
                    <th>Estado</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($productos as $p): ?>
                    <?php
                    $stockActual = (int)$p['stock_actual'];
                    $stockMinimo = (int)$p['stock_minimo'];
                    $esBajo = $stockActual < $stockMinimo;
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($p['nombre']); ?></td>
                        <td><?php echo htmlspecialchars($p['categoria'] ?? ''); ?></td>
                        <td><?php echo $stockActual; ?> <?php echo htmlspecialchars($p['unidad']); ?></td>
                        <td><?php echo $stockMinimo; ?> <?php echo htmlspecialchars($p['unidad']); ?></td>
                        <td>
                            <?php if ($esBajo): ?>
                                <span class="badge badge-stock-bajo">Stock bajo</span>
                            <?php else: ?>
                                <span class="badge badge-stock-ok">OK</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>












