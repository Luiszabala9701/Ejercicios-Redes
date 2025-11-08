<?php
// datosConexionBase.php - Conexión a la base de datos
function conectarBaseDatos() {
  $servidor = "localhost";
  $usuario  = "u889835150_luis9701";
  $clave    = "Facturita123";
  $base     = "u889835150_encabezadoFact";

  try {
    $pdo = new PDO("mysql:host=$servidor;dbname=$base;charset=utf8mb4",$usuario,$clave,[
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    return $pdo;
  } catch (PDOException $e) {
    $puntero = fopen(__DIR__."/errores.log","a");
    fwrite($puntero, date("Y-m-d H:i")." | Error conexión: ".$e->getMessage()."\n");
    fclose($puntero);
    http_response_code(500);
    echo "Error en conexión con la base.";
    exit;
  }
}
