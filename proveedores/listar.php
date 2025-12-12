<?php
// proveedores/listar.php
// CRUD sencillo de proveedores (solo ADMIN).

require_once __DIR__ . '/../helpers/auth.php';
require_admin();
require_once __DIR__ . '/../config/db.php';

$pdo = getPDO();

// Eliminar proveedor
if (isset($_GET['eliminar'])) {
    $id = (int)$_GET['eliminar'];
    if ($id > 0) {
        $stmt = $pdo->prepare('DELETE FROM proveedores WHERE id_proveedor = :id');
        $stmt->execute([':id' => $id]);
        header('Location: listar.php');
        exit;
    }
}

// Crear o actualizar proveedor
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_edicion = (int)($_POST['id_edicion'] ?? 0);
    $nombre = trim($_POST['nombre'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $direccion = trim($_POST['direccion'] ?? '');

    if ($nombre === '') {
        $error = 'El nombre del proveedor es obligatorio.';
    } else {
        if ($id_edicion > 0) {
            $stmt = $pdo->prepare('
                UPDATE proveedores
                SET nombre = :nombre, telefono = :telefono, email = :email, direccion = :direccion
                WHERE id_proveedor = :id
            ');
            $stmt->execute([
                ':nombre' => $nombre,
                ':telefono' => $telefono ?: null,
                ':email' => $email ?: null,
                ':direccion' => $direccion ?: null,
                ':id' => $id_edicion,
            ]);
        } else {
            $stmt = $pdo->prepare('
                INSERT INTO proveedores (nombre, telefono, email, direccion)
                VALUES (:nombre, :telefono, :email, :direccion)
            ');
            $stmt->execute([
                ':nombre' => $nombre,
                ':telefono' => $telefono ?: null,
                ':email' => $email ?: null,
                ':direccion' => $direccion ?: null,
            ]);
        }

        header('Location: listar.php');
        exit;
    }
}

// Si se va a editar
$proveedorEditar = null;
if (isset($_GET['editar'])) {
    $id = (int)$_GET['editar'];
    if ($id > 0) {
        $stmt = $pdo->prepare('SELECT * FROM proveedores WHERE id_proveedor = :id');
        $stmt->execute([':id' => $id]);
        $proveedorEditar = $stmt->fetch();
    }
}

$proveedores = $pdo->query('SELECT * FROM proveedores ORDER BY nombre')->fetchAll();

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<div class="container">
    <h1 class="h4 mb-3">Proveedores</h1>

    <div class="row">
        <div class="col-md-5">
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">
                        <?php echo $proveedorEditar ? 'Editar proveedor' : 'Nuevo proveedor'; ?>
                    </h5>
                    <?php if ($error): ?>
                        <div class="alert alert-danger py-2"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>
                    <form method="post">
                        <input type="hidden" name="id_edicion"
                               value="<?php echo $proveedorEditar ? (int)$proveedorEditar['id_proveedor'] : 0; ?>">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" name="nombre" id="nombre" class="form-control" required
                                   value="<?php echo $proveedorEditar ? htmlspecialchars($proveedorEditar['nombre']) : ''; ?>">
                        </div>
                        <div class="mb-3">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input type="text" name="telefono" id="telefono" class="form-control"
                                   value="<?php echo $proveedorEditar ? htmlspecialchars($proveedorEditar['telefono']) : ''; ?>">
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" id="email" class="form-control"
                                   value="<?php echo $proveedorEditar ? htmlspecialchars($proveedorEditar['email']) : ''; ?>">
                        </div>
                        <div class="mb-3">
                            <label for="direccion" class="form-label">Dirección</label>
                            <textarea name="direccion" id="direccion" class="form-control" rows="2"><?php
                                echo $proveedorEditar ? htmlspecialchars($proveedorEditar['direccion']) : '';
                                ?></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <?php echo $proveedorEditar ? 'Actualizar' : 'Guardar'; ?>
                        </button>
                        <?php if ($proveedorEditar): ?>
                            <a href="listar.php" class="btn btn-secondary btn-sm">Cancelar</a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Listado</h5>
                    <table class="table table-sm table-striped align-middle">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Teléfono</th>
                            <th>Email</th>
                            <th>Acciones</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($proveedores as $p): ?>
                            <tr>
                                <td><?php echo (int)$p['id_proveedor']; ?></td>
                                <td><?php echo htmlspecialchars($p['nombre']); ?></td>
                                <td><?php echo htmlspecialchars($p['telefono']); ?></td>
                                <td><?php echo htmlspecialchars($p['email']); ?></td>
                                <td>
                                    <a href="?editar=<?php echo (int)$p['id_proveedor']; ?>"
                                       class="btn btn-warning btn-sm">Editar</a>
                                    <a href="?eliminar=<?php echo (int)$p['id_proveedor']; ?>"
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('¿Eliminar este proveedor?');">
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
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>












