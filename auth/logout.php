<?php
// auth/logout.php
// Cierra la sesión del usuario y redirige al login.

session_start();
session_unset();
session_destroy();

header('Location: /auth/login.php');
exit;







