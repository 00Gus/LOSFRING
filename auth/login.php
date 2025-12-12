<?php
// auth/login.php
// Pantalla de inicio de sesión y validación de credenciales.

session_start();

// Si ya está logueado, ir al dashboard
if (isset($_SESSION['id_usuario'])) {
    header('Location: /index.php');
    exit;
}

require_once __DIR__ . '/../config/db.php';
$pdo = getPDO();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($usuario === '' || $password === '') {
        $error = 'Por favor, ingresa usuario y contraseña.';
    } else {
        // Buscar usuario por nombre de usuario
        $stmt = $pdo->prepare('SELECT * FROM usuarios WHERE usuario = :usuario LIMIT 1');
        $stmt->execute([':usuario' => $usuario]);
        $row = $stmt->fetch();

        if ($row && password_verify($password, $row['password'])) {
            // Login correcto
            $_SESSION['id_usuario'] = $row['id_usuario'];
            $_SESSION['usuario'] = $row['usuario'];
            $_SESSION['rol'] = $row['rol'];

            header('Location: /index.php');
            exit;
        } else {
            $error = 'Usuario o contraseña incorrectos.';
        }
    }
}

include __DIR__ . '/../includes/header.php';
?>

<div class="container">
    <div class="card card-login shadow-sm">
        <div class="card-body">
            <h1 class="h4 mb-3 text-center">LOS FRING - Iniciar sesión</h1>

            <?php if ($error): ?>
                <div class="alert alert-danger py-2"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="post" autocomplete="off">
                <div class="mb-3">
                    <label for="usuario" class="form-label">Usuario</label>
                    <input type="text" name="usuario" id="usuario" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <input type="password" name="password" id="password" class="form-control" required>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">Entrar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>







