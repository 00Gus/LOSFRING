<?php
// productos/editar.php
// Formulario para editar un producto existente.

require_once __DIR__ . '/../helpers/auth.php';
require_admin();
require_once __DIR__ . '/../config/db.php';

$pdo = getPDO();

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: listar.php');
    exit;
}

$stmt = $pdo->prepare('SELECT * FROM productos WHERE id_producto = :id');
$stmt->execute([':id' => $id]);
$producto = $stmt->fetch();

if (!$producto) {
    header('Location: listar.php');
    exit;
}

$categorias = $pdo->query('SELECT * FROM categorias ORDER BY nombre')->fetchAll();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigo_barras = trim($_POST['codigo_barras'] ?? '');
    $nombre = trim($_POST['nombre'] ?? '');
    $id_categoria = (int)($_POST['id_categoria'] ?? 0);
    $precio_compra = (float)($_POST['precio_compra'] ?? 0);
    $precio_venta = (float)($_POST['precio_venta'] ?? 0);
    $stock_actual = (int)($_POST['stock_actual'] ?? 0);
    $stock_minimo = (int)($_POST['stock_minimo'] ?? 0);
    $unidad = trim($_POST['unidad'] ?? 'pz');
    $fecha_caducidad = $_POST['fecha_caducidad'] ?? null;
    if ($fecha_caducidad === '') {
        $fecha_caducidad = null;
    }

    if ($nombre === '') {
        $error = 'El nombre del producto es obligatorio.';
    } else {
        $stmtUp = $pdo->prepare('
            UPDATE productos
            SET codigo_barras = :codigo_barras,
                nombre = :nombre,
                id_categoria = :id_categoria,
                precio_compra = :precio_compra,
                precio_venta = :precio_venta,
                stock_actual = :stock_actual,
                stock_minimo = :stock_minimo,
                unidad = :unidad,
                fecha_caducidad = :fecha_caducidad
            WHERE id_producto = :id
        ');
        $stmtUp->execute([
            ':codigo_barras' => $codigo_barras !== '' ? $codigo_barras : null,
            ':nombre' => $nombre,
            ':id_categoria' => $id_categoria ?: null,
            ':precio_compra' => $precio_compra,
            ':precio_venta' => $precio_venta,
            ':stock_actual' => $stock_actual,
            ':stock_minimo' => $stock_minimo,
            ':unidad' => $unidad,
            ':fecha_caducidad' => $fecha_caducidad,
            ':id' => $id,
        ]);

        header('Location: listar.php');
        exit;
    }
}

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<div class="container">
    <h1 class="h4 mb-3">Editar producto</h1>

    <?php if ($error): ?>
        <div class="alert alert-danger py-2"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="post">
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre</label>
                    <input type="text" class="form-control" id="nombre" name="nombre"
                           value="<?php echo htmlspecialchars($producto['nombre']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="codigo_barras" class="form-label">Código de barras (opcional)</label>
                    <input type="text" class="form-control" id="codigo_barras" name="codigo_barras"
                           value="<?php echo htmlspecialchars($producto['codigo_barras']); ?>">
                </div>
                <div class="mb-3">
                    <label for="id_categoria" class="form-label">Categoría</label>
                    <select class="form-select" id="id_categoria" name="id_categoria">
                        <option value="">-- Sin categoría --</option>
                        <?php foreach ($categorias as $cat): ?>
                            <option value="<?php echo (int)$cat['id_categoria']; ?>"
                                <?php echo ($producto['id_categoria'] == $cat['id_categoria']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="unidad" class="form-label">Unidad</label>
                    <input type="text" class="form-control" id="unidad" name="unidad"
                           value="<?php echo htmlspecialchars($producto['unidad']); ?>">
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="precio_compra" class="form-label">Precio de compra</label>
                    <input type="number" step="0.01" class="form-control" id="precio_compra" name="precio_compra"
                           value="<?php echo htmlspecialchars($producto['precio_compra']); ?>">
                </div>
                <div class="mb-3">
                    <label for="precio_venta" class="form-label">Precio de venta</label>
                    <input type="number" step="0.01" class="form-control" id="precio_venta" name="precio_venta"
                           value="<?php echo htmlspecialchars($producto['precio_venta']); ?>">
                </div>
                <div class="mb-3">
                    <label for="stock_actual" class="form-label">Stock actual</label>
                    <input type="number" class="form-control" id="stock_actual" name="stock_actual"
                           value="<?php echo htmlspecialchars($producto['stock_actual']); ?>">
                </div>
                <div class="mb-3">
                    <label for="stock_minimo" class="form-label">Stock mínimo</label>
                    <input type="number" class="form-control" id="stock_minimo" name="stock_minimo"
                           value="<?php echo htmlspecialchars($producto['stock_minimo']); ?>">
                </div>
                <div class="mb-3">
                    <label for="fecha_caducidad" class="form-label">Fecha de caducidad (opcional)</label>
                    <input type="date" class="form-control" id="fecha_caducidad" name="fecha_caducidad"
                           value="<?php echo htmlspecialchars($producto['fecha_caducidad']); ?>">
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Actualizar</button>
        <a href="listar.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>












