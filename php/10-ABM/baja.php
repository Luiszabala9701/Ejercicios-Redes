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
