<?php
// salidaJsonFamilia.php
// Devuelve JSON con las familias (id y descripcion) para poblar selects.
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/datosConexionBase.inc.php';

try {
    $pdo = obtenerPdo();
    $sql = "SELECT id, descripcion FROM familia ORDER BY descripcion";
    $stmt = $pdo->query($sql);
    $rows = $stmt->fetchAll();
    echo json_encode($rows, JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    // devolver un JSON con error
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
