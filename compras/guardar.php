<?php
// compras/guardar.php
// Procesa el formulario de nueva compra: inserta encabezado y detalle y actualiza stock.

require_once __DIR__ . '/../helpers/auth.php';
require_admin();
require_once __DIR__ . '/../config/db.php';

$pdo = getPDO();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: nueva.php');
    exit;
}

$id_proveedor = (int)($_POST['id_proveedor'] ?? 0);
$items = $_POST['items'] ?? [];
$id_usuario = $_SESSION['id_usuario'] ?? 0;

if ($id_proveedor <= 0 || $id_usuario <= 0 || empty($items)) {
    die('Datos incompletos para registrar la compra.');
}

try {
    $pdo->beginTransaction();

    // Calcular total
    $total = 0;
    foreach ($items as $item) {
        $cantidad = (int)($item['cantidad'] ?? 0);
        $precio_compra = (float)($item['precio_compra'] ?? 0);
        if ($cantidad > 0 && $precio_compra >= 0) {
            $total += $cantidad * $precio_compra;
        }
    }

    // Insertar encabezado de compra
    $stmt = $pdo->prepare('
        INSERT INTO compras (fecha, id_proveedor, id_usuario, total)
        VALUES (NOW(), :id_proveedor, :id_usuario, :total)
    ');
    $stmt->execute([
        ':id_proveedor' => $id_proveedor,
        ':id_usuario' => $id_usuario,
        ':total' => $total,
    ]);

    $id_compra = (int)$pdo->lastInsertId();

    // Insertar detalle y actualizar stock
    $stmtDet = $pdo->prepare('
        INSERT INTO detalle_compra (id_compra, id_producto, cantidad, precio_compra)
        VALUES (:id_compra, :id_producto, :cantidad, :precio_compra)
    ');
    $stmtUpdStock = $pdo->prepare('
        UPDATE productos
        SET stock_actual = stock_actual + :cantidad,
            precio_compra = :precio_compra
        WHERE id_producto = :id_producto
    ');

    foreach ($items as $item) {
        $id_producto = (int)($item['id_producto'] ?? 0);
        $cantidad = (int)($item['cantidad'] ?? 0);
        $precio_compra = (float)($item['precio_compra'] ?? 0);

        if ($id_producto > 0 && $cantidad > 0) {
            $stmtDet->execute([
                ':id_compra' => $id_compra,
                ':id_producto' => $id_producto,
                ':cantidad' => $cantidad,
                ':precio_compra' => $precio_compra,
            ]);

            $stmtUpdStock->execute([
                ':cantidad' => $cantidad,
                ':precio_compra' => $precio_compra,
                ':id_producto' => $id_producto,
            ]);
        }
    }

    $pdo->commit();
    header('Location: listar.php');
    exit;
} catch (Exception $e) {
    $pdo->rollBack();
    die('Error al guardar la compra: ' . $e->getMessage());
}












