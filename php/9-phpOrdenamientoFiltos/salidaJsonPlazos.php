<?php
header('Content-Type: application/json; charset=utf-8');
require_once "datosConexionBase.php";

try {
    sleep(2);
    $pdo = conectarBaseDatos();

    $stmt = $pdo->prepare("SELECT cod, nroDias FROM plazos_entrega ORDER BY cod");
    $stmt->execute();
    $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(["plazos" => $resultado], JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    escribirLogErrores("Error en salidaJsonPlazos: " . $e->getMessage());
    echo json_encode(["error" => "Error al obtener plazos."]);
}
