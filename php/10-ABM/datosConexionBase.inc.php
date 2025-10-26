<?php
// datosConexionBase.inc.php
// Variables de conexión. Cambialas según tu entorno (local o hosting).
// Recomendación: en producción no mantengas credenciales en archivos públicos.
$DB_HOST = 'localhost'; // o 'auth-db887.hstgr.io' en Hostinger
$DB_PORT = 3306; // opcional
$DB_NAME = 'u889835150_Encabezado_Fac';
$DB_USER = 'u889835150_luisz9701';
$DB_PASS = 's+XxKG4=bV';

function obtenerPdo() {
    global $DB_HOST, $DB_PORT, $DB_NAME, $DB_USER, $DB_PASS;
    $dsn = "mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4;port={$DB_PORT}";
    try {
        $pdo = new PDO($dsn, $DB_USER, $DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        return $pdo;
    } catch (PDOException $e) {
        // respuesta mínima para no filtrar credenciales
        header('Content-Type: text/html; charset=utf-8');
        echo "Conexión fallida: " . htmlspecialchars($e->getMessage()) . "<br>";
        exit;
    }
}

?>
