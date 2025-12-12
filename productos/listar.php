<?php
// productos/listar.php
// Lista de productos con opciones CRUD básicas (solo ADMIN).

require_once __DIR__ . '/../helpers/auth.php';
require_admin();
require_once __DIR__ . '/../config/db.php';

$pdo = getPDO();

// Eliminar producto
if (isset($_GET['eliminar'])) {
    $id = (int)$_GET['eliminar'];
    if ($id > 0) {
        $stmt = $pdo->prepare('DELETE FROM productos WHERE id_producto = :id');
        $stmt->execute([':id' => $id]);
        header('Location: listar.php');
        exit;
    }
}

// Obtener productos con nombre de categoría
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
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Productos</h1>
        <a href="crear.php" class="btn btn-primary btn-sm">Nuevo producto</a>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table table-striped table-sm align-middle">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Categoría</th>
                    <th>Precio compra</th>
                    <th>Precio venta</th>
                    <th>Stock actual</th>
                    <th>Stock mínimo</th>
                    <th>Acciones</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($productos as $p): ?>
                    <tr>
                        <td><?php echo (int)$p['id_producto']; ?></td>
                        <td><?php echo htmlspecialchars($p['nombre']); ?></td>
                        <td><?php echo htmlspecialchars($p['categoria'] ?? ''); ?></td>
                        <td>$<?php echo number_format((float)$p['precio_compra'], 2); ?></td>
                        <td>$<?php echo number_format((float)$p['precio_venta'], 2); ?></td>
                        <td><?php echo (int)$p['stock_actual']; ?></td>
                        <td><?php echo (int)$p['stock_minimo']; ?></td>
                        <td>
                            <a href="editar.php?id=<?php echo (int)$p['id_producto']; ?>" class="btn btn-warning btn-sm">Editar</a>
                            <a href="?eliminar=<?php echo (int)$p['id_producto']; ?>"
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('¿Eliminar este producto?');">
                                Eliminar
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>












