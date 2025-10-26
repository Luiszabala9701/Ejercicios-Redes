<?php
// baja.php
// Borra un registro de la tabla factura por NroFactura
header('Content-Type: text/html; charset=utf-8');
require_once __DIR__ . '/datosConexionBase.php';

$respuesta = "Respuesta del servidor a la baja.<br>";
try {
    $pdo = conectarBaseDatos();
    $NroFactura = $_POST['NroFactura'] ?? '';
    $respuesta .= "Entradas: Nro=$NroFactura<br>";

    $sql = "DELETE FROM factura WHERE NroFactura = :NroFactura";
    $stmt = $pdo->prepare($sql);
    $respuesta .= "Prepare OK<br>";
    $stmt->bindParam(':NroFactura', $NroFactura);
    $respuesta .= "Bind OK<br>";
    $stmt->execute();
    $cnt = $stmt->rowCount();
    $respuesta .= "Execute OK, filas eliminadas: $cnt<br>";

} catch (Exception $e) {
    $respuesta .= "Error: " . htmlspecialchars($e->getMessage()) . "<br>";
}

$puntero = fopen(__DIR__ . "/errores.log","a");
fwrite($puntero, date("Y-m-d H:i") . " | baja: " . strip_tags($respuesta) . "\n");
fclose($puntero);

echo $respuesta;

?>
<?php
require_once "datosConexionBase.php";
header('Content-Type: text/html; charset=utf-8');

$respuesta_estado = "Baja de registro<br>";

try {
  $pdo = conectarBaseDatos();

  $NroFactura = $_POST['NroFactura'] ?? '';

  $sql = "DELETE FROM factura WHERE NroFactura=:NroFactura";
  try { $stmt = $pdo->prepare($sql); $respuesta_estado .= "Preparación exitosa<br>"; }
  catch(PDOException $e){ $respuesta_estado .= "Error prepare: ".$e->getMessage()."<br>"; }

  try { $stmt->bindParam(':NroFactura', $NroFactura); $respuesta_estado .= "Binding exitoso<br>"; }
  catch(PDOException $e){ $respuesta_estado .= "Error bind: ".$e->getMessage()."<br>"; }

  try { $stmt->execute(); $respuesta_estado .= "Ejecución exitosa<br>"; }
  catch(PDOException $e){ $respuesta_estado .= "Error execute: ".$e->getMessage()."<br>"; }

} catch(PDOException $e){
  $respuesta_estado .= "Error general: ".$e->getMessage()."<br>";
}

$puntero = fopen("./errores.log","a");
fwrite($puntero, date("Y-m-d H:i")." | baja: ".$respuesta_estado."\n");
fclose($puntero);

echo $respuesta_estado;
