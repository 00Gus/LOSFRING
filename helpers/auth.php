<?php
// helpers/auth.php
// Funciones sencillas para verificar sesión y roles.

session_start();

/**
 * Verifica que el usuario esté autenticado.
 * Si no lo está, redirige al formulario de login.
 */
function require_login(): void
{
    if (!isset($_SESSION['id_usuario'])) {
        header('Location: /auth/login.php');
        exit;
    }
}

/**
 * Verifica que el usuario autenticado tenga rol ADMIN.
 * Si no lo tiene, muestra mensaje simple y sale.
 */
function require_admin(): void
{
    require_login();
    if (($_SESSION['rol'] ?? '') !== 'ADMIN') {
        http_response_code(403);
        echo 'No tienes permisos para acceder a esta sección.';
        exit;
    }
}






