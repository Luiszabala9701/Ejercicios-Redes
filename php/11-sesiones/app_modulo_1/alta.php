<?php
// alta.php - Inserta nueva factura
session_start();
include __DIR__ . '/../manejoSesion.inc';

header('Content-Type: text/html; charset=utf-8');
include __DIR__ . '/../datosConexionBase.php';

$respuesta = "Alta de registro<br>";

try {
  $pdo = conectarBaseDatos();

  $nroFactura       = $_POST['NroFactura']        ?? '';
  $codProveedor     = $_POST['CodProveedor']      ?? '';
  $domicilioProv    = $_POST['DomicilioProveedor']?? '';
  $fechaFactura     = $_POST['FechaFactura']      ?? '';
  $codPlazosEntrega = $_POST['CodPlazosEntrega']  ?? '';
  $totalNeto        = $_POST['TotalNetoFactura']  ?? 0;

  // Verificar si ya existe el número de factura
  $sqlVerificar = "SELECT COUNT(*) as total FROM factura WHERE NroFactura = :NroFactura";
  $stmtVerificar = $pdo->prepare($sqlVerificar);
  $stmtVerificar->bindParam(':NroFactura', $nroFactura);
  $stmtVerificar->execute();
  $resultado = $stmtVerificar->fetch(PDO::FETCH_ASSOC);
  
  if ($resultado['total'] > 0) {
    throw new Exception("El número de factura '$nroFactura' ya existe en la base de datos");
  }

  $sql = "INSERT INTO factura
          (NroFactura, CodProveedor, DomicilioProveedor, FechaFactura, CodPlazosEntrega, TotalNetoFactura)
          VALUES (:NroFactura,:CodProveedor,:DomicilioProveedor,:FechaFactura,:CodPlazosEntrega,:TotalNetoFactura)";
  
  $stmt = $pdo->prepare($sql);
  $stmt->bindParam(':NroFactura', $nroFactura);
  $stmt->bindParam(':CodProveedor', $codProveedor);
  $stmt->bindParam(':DomicilioProveedor', $domicilioProv);
  $stmt->bindParam(':FechaFactura', $fechaFactura);
  $stmt->bindParam(':CodPlazosEntrega', $codPlazosEntrega);
  $stmt->bindParam(':TotalNetoFactura', $totalNeto);
  $stmt->execute();
  $respuesta .= "Registro insertado correctamente<br>";

  if (isset($_FILES['PdfComprobante']) && $_FILES['PdfComprobante']['error'] === UPLOAD_ERR_OK) {
    $contenidoPdf = file_get_contents($_FILES['PdfComprobante']['tmp_name']);
    $esPdf = (substr($contenidoPdf,0,5) === '%PDF-' || substr($contenidoPdf,0,4) === '%PDF');
    
    if ($esPdf) {
      $sql2 = "UPDATE factura SET PdfComprobante=:pdf WHERE NroFactura=:NroFactura";
      $stmt2 = $pdo->prepare($sql2);
      $stmt2->bindParam(':pdf', $contenidoPdf, PDO::PARAM_LOB);
      $stmt2->bindParam(':NroFactura', $nroFactura);
      $stmt2->execute();
      $respuesta .= "PDF adjunto guardado<br>";
    } else {
      $respuesta .= "Archivo rechazado: no es un PDF válido<br>";
    }
  }

} catch (Exception $e) {
  $respuesta .= "Error: " . htmlspecialchars($e->getMessage()) . "<br>";
}

echo $respuesta;

