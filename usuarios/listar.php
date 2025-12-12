<?php
// usuarios/listar.php
// Administración simple de usuarios del sistema (solo ADMIN).

require_once __DIR__ . '/../helpers/auth.php';
require_admin();
require_once __DIR__ . '/../config/db.php';

$pdo = getPDO();

// Eliminar usuario (no permitir eliminarse a sí mismo)
if (isset($_GET['eliminar'])) {
    $id = (int)$_GET['eliminar'];
    if ($id > 0 && $id !== (int)($_SESSION['id_usuario'] ?? 0)) {
        $stmt = $pdo->prepare('DELETE FROM usuarios WHERE id_usuario = :id');
        $stmt->execute([':id' => $id]);
        header('Location: listar.php');
        exit;
    }
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_edicion = (int)($_POST['id_edicion'] ?? 0);
    $nombre = trim($_POST['nombre'] ?? '');
    $usuario = trim($_POST['usuario'] ?? '');
    $rol = $_POST['rol'] ?? 'CAJERO';
    $password = $_POST['password'] ?? '';

    if ($nombre === '' || $usuario === '') {
        $error = 'Nombre y usuario son obligatorios.';
    } else {
        if ($id_edicion > 0) {
            // Actualizar, con cambio de contraseña opcional
            if ($password !== '') {
                $hash = password_hash($password, PASSWORD_BCRYPT);
                $stmt = $pdo->prepare('
                    UPDATE usuarios
                    SET nombre = :nombre, usuario = :usuario, rol = :rol, password = :password
                    WHERE id_usuario = :id
                ');
                $stmt->execute([
                    ':nombre' => $nombre,
                    ':usuario' => $usuario,
                    ':rol' => $rol,
                    ':password' => $hash,
                    ':id' => $id_edicion,
                ]);
            } else {
                $stmt = $pdo->prepare('
                    UPDATE usuarios
                    SET nombre = :nombre, usuario = :usuario, rol = :rol
                    WHERE id_usuario = :id
                ');
                $stmt->execute([
                    ':nombre' => $nombre,
                    ':usuario' => $usuario,
                    ':rol' => $rol,
                    ':id' => $id_edicion,
                ]);
            }
        } else {
            // Nuevo usuario (requiere contraseña)
            if ($password === '') {
                $error = 'Debes indicar una contraseña para el nuevo usuario.';
            } else {
                $hash = password_hash($password, PASSWORD_BCRYPT);
                $stmt = $pdo->prepare('
                    INSERT INTO usuarios (nombre, usuario, password, rol)
                    VALUES (:nombre, :usuario, :password, :rol)
                ');
                $stmt->execute([
                    ':nombre' => $nombre,
                    ':usuario' => $usuario,
                    ':password' => $hash,
                    ':rol' => $rol,
                ]);
            }
        }

        if ($error === '') {
            header('Location: listar.php');
            exit;
        }
    }
}

// Usuario en edición (opcional)
$usuarioEditar = null;
if (isset($_GET['editar'])) {
    $id = (int)$_GET['editar'];
    if ($id > 0) {
        $stmt = $pdo->prepare('SELECT * FROM usuarios WHERE id_usuario = :id');
        $stmt->execute([':id' => $id]);
        $usuarioEditar = $stmt->fetch();
    }
}

$usuarios = $pdo->query('SELECT * FROM usuarios ORDER BY creado_en DESC')->fetchAll();

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<div class="container">
    <h1 class="h4 mb-3">Usuarios</h1>

    <div class="row">
        <div class="col-md-5">
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">
                        <?php echo $usuarioEditar ? 'Editar usuario' : 'Nuevo usuario'; ?>
                    </h5>
                    <?php if ($error): ?>
                        <div class="alert alert-danger py-2"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>
                    <form method="post">
                        <input type="hidden" name="id_edicion"
                               value="<?php echo $usuarioEditar ? (int)$usuarioEditar['id_usuario'] : 0; ?>">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" name="nombre" id="nombre" class="form-control" required
                                   value="<?php echo $usuarioEditar ? htmlspecialchars($usuarioEditar['nombre']) : ''; ?>">
                        </div>
                        <div class="mb-3">
                            <label for="usuario" class="form-label">Usuario</label>
                            <input type="text" name="usuario" id="usuario" class="form-control" required
                                   value="<?php echo $usuarioEditar ? htmlspecialchars($usuarioEditar['usuario']) : ''; ?>">
                        </div>
                        <div class="mb-3">
                            <label for="rol" class="form-label">Rol</label>
                            <select name="rol" id="rol" class="form-select">
                                <option value="ADMIN" <?php echo ($usuarioEditar && $usuarioEditar['rol'] === 'ADMIN') ? 'selected' : ''; ?>>ADMIN</option>
                                <option value="CAJERO" <?php echo ($usuarioEditar && $usuarioEditar['rol'] === 'CAJERO') ? 'selected' : ''; ?>>CAJERO</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">
                                Contraseña <?php echo $usuarioEditar ? '(dejar en blanco para no cambiar)' : ''; ?>
                            </label>
                            <input type="password" name="password" id="password" class="form-control"
                                   <?php echo $usuarioEditar ? '' : 'required'; ?>>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <?php echo $usuarioEditar ? 'Actualizar' : 'Crear'; ?>
                        </button>
                        <?php if ($usuarioEditar): ?>
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
                            <th>Usuario</th>
                            <th>Rol</th>
                            <th>Creado en</th>
                            <th>Acciones</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($usuarios as $u): ?>
                            <tr>
                                <td><?php echo (int)$u['id_usuario']; ?></td>
                                <td><?php echo htmlspecialchars($u['nombre']); ?></td>
                                <td><?php echo htmlspecialchars($u['usuario']); ?></td>
                                <td><?php echo htmlspecialchars($u['rol']); ?></td>
                                <td><?php echo htmlspecialchars($u['creado_en']); ?></td>
                                <td>
                                    <a href="?editar=<?php echo (int)$u['id_usuario']; ?>"
                                       class="btn btn-warning btn-sm">Editar</a>
                                    <?php if ((int)$u['id_usuario'] !== (int)($_SESSION['id_usuario'] ?? 0)): ?>
                                        <a href="?eliminar=<?php echo (int)$u['id_usuario']; ?>"
                                           class="btn btn-danger btn-sm"
                                           onclick="return confirm('¿Eliminar este usuario?');">
                                            Eliminar
                                        </a>
                                    <?php endif; ?>
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





