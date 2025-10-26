<?php
require_once "datosConexionBase.php";
header('Content-Type: application/json; charset=utf-8');

$respuesta_estado = "traeDoc<br>";

try {
  $pdo = conectarBaseDatos();
  $NroFactura = $_POST['NroFactura'] ?? '';

  $sql = "SELECT PdfComprobante FROM factura WHERE NroFactura = :NroFactura";
  $stmt = $pdo->prepare($sql);
  $stmt->bindParam(':NroFactura', $NroFactura);
  $stmt->execute();
  $fila = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$fila || is_null($fila['PdfComprobante'])) {
    echo json_encode(['error' => 'No existe PDF para este registro']);
    exit;
  }

  $obj = new stdClass();
  $obj->pdf = base64_encode($fila['PdfComprobante']);

  echo json_encode($obj, JSON_INVALID_UTF8_SUBSTITUTE);
} catch (PDOException $e) {
  $puntero = fopen("./errores.log","a");
  fwrite($puntero, date("Y-m-d H:i")." | traeDoc: ".$e->getMessage()."\n");
  fclose($puntero);
  echo json_encode(['error' => 'Error al traer documento']);
}
