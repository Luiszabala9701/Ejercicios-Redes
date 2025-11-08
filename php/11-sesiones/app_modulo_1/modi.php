<?php
// modi.php - Modifica un registro de factura
session_start();
include __DIR__ . '/../manejoSesion.inc';

header('Content-Type: text/html; charset=utf-8');
include __DIR__ . '/../datosConexionBase.php';

$respuesta = "Modificación de registro<br>";

try {
  $pdo = conectarBaseDatos();

  $nroFactura       = $_POST['NroFactura']        ?? '';
  $codProveedor     = $_POST['CodProveedor']      ?? '';
  $domicilioProv    = $_POST['DomicilioProveedor']?? '';
  $fechaFactura     = $_POST['FechaFactura']      ?? '';
  $codPlazosEntrega = $_POST['CodPlazosEntrega']  ?? '';
  $totalNeto        = $_POST['TotalNetoFactura']  ?? 0;

  $sql = "UPDATE factura SET
            CodProveedor=:CodProveedor,
            DomicilioProveedor=:DomicilioProveedor,
            FechaFactura=:FechaFactura,
            CodPlazosEntrega=:CodPlazosEntrega,
            TotalNetoFactura=:TotalNetoFactura
          WHERE NroFactura=:NroFactura";

  $stmt = $pdo->prepare($sql);
  $stmt->bindParam(':CodProveedor', $codProveedor);
  $stmt->bindParam(':DomicilioProveedor', $domicilioProv);
  $stmt->bindParam(':FechaFactura', $fechaFactura);
  $stmt->bindParam(':CodPlazosEntrega', $codPlazosEntrega);
  $stmt->bindParam(':TotalNetoFactura', $totalNeto);
  $stmt->bindParam(':NroFactura', $nroFactura);
  $stmt->execute();
  $respuesta .= "Datos actualizados correctamente<br>";

  if (isset($_FILES['PdfComprobante']) && $_FILES['PdfComprobante']['error'] === UPLOAD_ERR_OK) {
    $contenidoPdf = file_get_contents($_FILES['PdfComprobante']['tmp_name']);
    $esPdf = (substr($contenidoPdf,0,5) === '%PDF-' || substr($contenidoPdf,0,4) === '%PDF');
    
    if ($esPdf) {
      $sql2 = "UPDATE factura SET PdfComprobante=:pdf WHERE NroFactura=:NroFactura";
      $stmt2 = $pdo->prepare($sql2);
      $stmt2->bindParam(':pdf', $contenidoPdf, PDO::PARAM_LOB);
      $stmt2->bindParam(':NroFactura', $nroFactura);
      $stmt2->execute();
      $respuesta .= "PDF actualizado<br>";
    } else {
      $respuesta .= "Archivo rechazado: no es un PDF válido<br>";
    }
  }

} catch (Exception $e) {
  $respuesta .= "Error: ".htmlspecialchars($e->getMessage())."<br>";
}

echo $respuesta;

