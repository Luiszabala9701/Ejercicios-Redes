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
    echo "<h3>Error en conexión con la base.</h3>";
    echo "<p><strong>Detalles del error:</strong></p>";
    echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
    echo "<p><strong>Información de conexión:</strong></p>";
    echo "<ul>";
    echo "<li>Servidor: $servidor</li>";
    echo "<li>Usuario: $usuario</li>";
    echo "<li>Base de datos: $base</li>";
    echo "<li>Código de error: " . $e->getCode() . "</li>";
    echo "</ul>";
    exit;
  }
}
