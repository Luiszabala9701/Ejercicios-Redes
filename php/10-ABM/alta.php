<?php
require_once "datosConexionBase.php";

header('Content-Type: text/html; charset=utf-8');
$respuesta_estado = "Respuesta del servidor al alta.<br>";

try {
  $pdo = conectarBaseDatos();

  // Entradas simples
  $NroFactura       = $_POST['NroFactura']        ?? '';
  $CodProveedor     = $_POST['CodProveedor']      ?? '';
  $DomicilioProv    = $_POST['DomicilioProveedor']?? '';
  $FechaFactura     = $_POST['FechaFactura']      ?? '';
  $CodPlazosEntrega = $_POST['CodPlazosEntrega']  ?? '';
  $TotalNeto        = $_POST['TotalNetoFactura']  ?? 0;

  $respuesta_estado .= "Entradas: Nro=$NroFactura, Prov=$CodProveedor, Fecha=$FechaFactura<br>";

  // Alta “simple”
  $sql = "INSERT INTO factura
          (NroFactura, CodProveedor, DomicilioProveedor, FechaFactura, CodPlazosEntrega, TotalNetoFactura)
          VALUES (:NroFactura,:CodProveedor,:DomicilioProveedor,:FechaFactura,:CodPlazosEntrega,:TotalNetoFactura)";
  try {
    $stmt = $pdo->prepare($sql);
    $respuesta_estado .= "Preparación exitosa<br>";
  } catch (PDOException $e) {
    $respuesta_estado .= "Error prepare: ".$e->getMessage()."<br>";
  }
  try {
    $stmt->bindParam(':NroFactura', $NroFactura);
    $stmt->bindParam(':CodProveedor', $CodProveedor);
    $stmt->bindParam(':DomicilioProveedor', $DomicilioProv);
    $stmt->bindParam(':FechaFactura', $FechaFactura);
    $stmt->bindParam(':CodPlazosEntrega', $CodPlazosEntrega);
    $stmt->bindParam(':TotalNetoFactura', $TotalNeto);
    $respuesta_estado .= "Binding exitoso<br>";
  } catch (PDOException $e) {
    $respuesta_estado .= "Error bind: ".$e->getMessage()."<br>";
  }
  try {
    $stmt->execute();
    $respuesta_estado .= "Ejecución exitosa<br>";
  } catch (PDOException $e) {
    $respuesta_estado .= "Error execute: ".$e->getMessage()."<br>";
  }

  // PDF opcional
  if (isset($_FILES['PdfComprobante']) && !empty($_FILES['PdfComprobante']['name'])) {
    $contenidoPdf = file_get_contents($_FILES['PdfComprobante']['tmp_name']);
    $sql2 = "UPDATE factura SET PdfComprobante=:pdf WHERE NroFactura=:NroFactura";
    try {
      $stmt2 = $pdo->prepare($sql2);
      $stmt2->bindParam(':pdf', $contenidoPdf, PDO::PARAM_LOB);
      $stmt2->bindParam(':NroFactura', $NroFactura);
      $stmt2->execute();
      $respuesta_estado .= "PDF actualizado correctamente<br>";
    } catch(PDOException $e) {
      $respuesta_estado .= "Error PDF: ".$e->getMessage()."<br>";
    }
  } else {
    $respuesta_estado .= "No se adjuntó PDF<br>";
  }

} catch (PDOException $e) {
  $respuesta_estado .= "Error general: ".$e->getMessage()."<br>";
}

// Log y salida
$puntero = fopen("./errores.log","a");
fwrite($puntero, date("Y-m-d H:i")." | alta: ".$respuesta_estado."\n");
fclose($puntero);

echo $respuesta_estado;
