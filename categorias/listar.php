<?php
// categorias/listar.php
// Lista las categorías y permite crear/eliminar (solo ADMIN).

require_once __DIR__ . '/../helpers/auth.php';
require_admin();
require_once __DIR__ . '/../config/db.php';

$pdo = getPDO();

// Eliminar categoría (si no tiene productos asociados idealmente)
if (isset($_GET['eliminar'])) {
    $id = (int)$_GET['eliminar'];
    if ($id > 0) {
        $stmt = $pdo->prepare('DELETE FROM categorias WHERE id_categoria = :id');
        $stmt->execute([':id' => $id]);
        header('Location: listar.php');
        exit;
    }
}

// Insertar nueva categoría
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    if ($nombre === '') {
        $error = 'El nombre de la categoría es obligatorio.';
    } else {
        $stmt = $pdo->prepare('INSERT INTO categorias (nombre) VALUES (:nombre)');
        $stmt->execute([':nombre' => $nombre]);
        header('Location: listar.php');
        exit;
    }
}

$categorias = $pdo->query('SELECT * FROM categorias ORDER BY nombre')->fetchAll();

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<div class="container">
    <h1 class="h4 mb-3">Categorías</h1>

    <div class="row">
        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">Nueva categoría</h5>
                    <?php if ($error): ?>
                        <div class="alert alert-danger py-2"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>
                    <form method="post">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" name="nombre" id="nombre" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm">Guardar</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Listado</h5>
                    <table class="table table-sm table-striped align-middle">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Acciones</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($categorias as $cat): ?>
                            <tr>
                                <td><?php echo (int)$cat['id_categoria']; ?></td>
                                <td><?php echo htmlspecialchars($cat['nombre']); ?></td>
                                <td>
                                    <a href="?eliminar=<?php echo (int)$cat['id_categoria']; ?>"
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('¿Eliminar esta categoría?');">
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












