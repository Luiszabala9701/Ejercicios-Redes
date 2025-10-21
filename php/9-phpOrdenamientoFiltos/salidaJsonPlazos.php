<?php
// Devuelve plazos en JSON, con sleep y logging estilo apunte
header('Content-Type: application/json; charset=utf-8');
require_once "datosConexionBase.php";

try {
    // Simular latencia para la ALERTA de “carga de familias/plazos”
    sleep(2);

    // Log de llegada
    $puntero = fopen("./errores.log", "a");
    fwrite($puntero, "llega salidaJsonPlazos");
    fwrite($puntero, " | " . date("Y-m-d H:i"));
    fwrite($puntero, "\n");
    fclose($puntero);

    $pdo = conectarBaseDatos();

    $stmt = $pdo->prepare("SELECT cod, nroDias FROM plazos_entrega ORDER BY cod");
    $stmt->execute();
    $plazos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Log de éxito con cantidad
    $puntero = fopen("./errores.log", "a");
    fwrite($puntero, "ok salidaJsonPlazos rows=" . count($plazos));
    fwrite($puntero, " | " . date("Y-m-d H:i"));
    fwrite($puntero, "\n");
    fclose($puntero);

    echo json_encode(["plazos" => $plazos], JSON_UNESCAPED_UNICODE);
} catch (PDOException $error) {
    // Log de error
    $puntero = fopen("./errores.log", "a");
    fwrite($puntero, "Error en salidaJsonPlazos: " . $error->getMessage());
    fwrite($puntero, " | " . date("Y-m-d H:i"));
    fwrite($puntero, "\n");
    fclose($puntero);

    echo json_encode(["error" => "Error al obtener plazos."]);
}
