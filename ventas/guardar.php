<?php
// ventas/guardar.php
// Procesa el formulario de nueva venta: inserta encabezado y detalle y descuenta stock.

require_once __DIR__ . '/../helpers/auth.php';
require_login();
require_once __DIR__ . '/../config/db.php';

$pdo = getPDO();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: nueva.php');
    exit;
}

$forma_pago = trim($_POST['forma_pago'] ?? 'EFECTIVO');
$items = $_POST['items'] ?? [];
$id_usuario = $_SESSION['id_usuario'] ?? 0;

if ($id_usuario <= 0 || empty($items)) {
    die('Datos incompletos para registrar la venta.');
}

try {
    $pdo->beginTransaction();

    // Calcular total
    $total = 0;
    foreach ($items as $item) {
        $cantidad = (int)($item['cantidad'] ?? 0);
        $precio_venta = (float)($item['precio_venta'] ?? 0);
        if ($cantidad > 0 && $precio_venta >= 0) {
            $total += $cantidad * $precio_venta;
        }
    }

    // Insertar encabezado de venta
    $stmt = $pdo->prepare('
        INSERT INTO ventas (fecha, id_usuario, total, forma_pago)
        VALUES (NOW(), :id_usuario, :total, :forma_pago)
    ');
    $stmt->execute([
        ':id_usuario' => $id_usuario,
        ':total' => $total,
        ':forma_pago' => $forma_pago,
    ]);

    $id_venta = (int)$pdo->lastInsertId();

    // Insertar detalle y actualizar stock
    $stmtDet = $pdo->prepare('
        INSERT INTO detalle_venta (id_venta, id_producto, cantidad, precio_venta)
        VALUES (:id_venta, :id_producto, :cantidad, :precio_venta)
    ');
    $stmtUpdStock = $pdo->prepare('
        UPDATE productos
        SET stock_actual = stock_actual - :cantidad,
            precio_venta = :precio_venta
        WHERE id_producto = :id_producto
    ');

    foreach ($items as $item) {
        $id_producto = (int)($item['id_producto'] ?? 0);
        $cantidad = (int)($item['cantidad'] ?? 0);
        $precio_venta = (float)($item['precio_venta'] ?? 0);

        if ($id_producto > 0 && $cantidad > 0) {
            $stmtDet->execute([
                ':id_venta' => $id_venta,
                ':id_producto' => $id_producto,
                ':cantidad' => $cantidad,
                ':precio_venta' => $precio_venta,
            ]);

            $stmtUpdStock->execute([
                ':cantidad' => $cantidad,
                ':precio_venta' => $precio_venta,
                ':id_producto' => $id_producto,
            ]);
        }
    }

    $pdo->commit();
    header('Location: listar.php');
    exit;
} catch (Exception $e) {
    $pdo->rollBack();
    die('Error al guardar la venta: ' . $e->getMessage());
}







