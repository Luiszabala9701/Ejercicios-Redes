<?php
session_start();
include __DIR__ . '/../manejoSesion.inc';

header('Content-Type: application/json; charset=utf-8');
include __DIR__ . '/../datosConexionBase.php';

try {
    $pdo = conectarBaseDatos();

    $stmt = $pdo->prepare("SELECT cod, nroDias FROM plazoentrega ORDER BY cod");
    $stmt->execute();
    $plazos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($plazos, JSON_UNESCAPED_UNICODE);
} catch (PDOException $error) {
    $puntero = fopen(__DIR__ . "/errores.log", "a");
    fwrite($puntero, "Error en salidaJsonPlazos: " . $error->getMessage());
    fwrite($puntero, " | " . date("Y-m-d H:i"));
    fwrite($puntero, "\n");
    fclose($puntero);

    echo json_encode(["error" => "Error al obtener plazos."]);
}
