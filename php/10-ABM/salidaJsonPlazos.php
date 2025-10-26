<?php
header('Content-Type: application/json; charset=utf-8');
require_once "datosConexionBase.php";

try {
    sleep(1);

    $puntero = fopen("./errores.log", "a");
    fwrite($puntero, "llega salidaJsonPlazos");
    fwrite($puntero, " | " . date("Y-m-d H:i"));
    fwrite($puntero, "\n");
    fclose($puntero);

    $pdo = conectarBaseDatos();

    $stmt = $pdo->prepare("SELECT cod, nroDias FROM plazoentrega ORDER BY cod");
    $stmt->execute();
    $plazos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $puntero = fopen("./errores.log", "a");
    fwrite($puntero, "ok salidaJsonPlazos rows=" . count($plazos));
    fwrite($puntero, " | " . date("Y-m-d H:i"));
    fwrite($puntero, "\n");
    fclose($puntero);

    echo json_encode(["plazos" => $plazos], JSON_UNESCAPED_UNICODE);
} catch (PDOException $error) {
    $puntero = fopen("./errores.log", "a");
    fwrite($puntero, "Error en salidaJsonPlazos: " . $error->getMessage());
    fwrite($puntero, " | " . date("Y-m-d H:i"));
    fwrite($puntero, "\n");
    fclose($puntero);

    echo json_encode(["error" => "Error al obtener plazos."]);
}
