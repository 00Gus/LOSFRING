<?php
// config/db.php
// Maneja la conexión a la base de datos usando PDO.

$DB_HOST = getenv('DB_HOST') ?: 'localhost';
$DB_NAME = getenv('DB_NAME') ?: 'minismart_db';
$DB_USER = getenv('DB_USER') ?: 'root';
$DB_PASS = getenv('DB_PASS') ?: '';
$DB_CHARSET = 'utf8mb4';

/**
 * Retorna una instancia PDO lista para usar en los módulos.
 */
function getPDO()
{
    static $pdo = null;
    global $DB_HOST, $DB_NAME, $DB_USER, $DB_PASS, $DB_CHARSET;

    if ($pdo === null) {
        $dsn = "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=$DB_CHARSET";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $pdo = new PDO($dsn, $DB_USER, $DB_PASS, $options);
        } catch (PDOException $e) {
            // En un entorno real se podría loguear el error.
            die('Error de conexión a la base de datos: ' . $e->getMessage());
        }
    }

    return $pdo;
}



