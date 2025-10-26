<?php
// modi.php
// Modifica un registro de la tabla factura según NroFactura
header('Content-Type: text/html; charset=utf-8');
require_once __DIR__ . '/datosConexionBase.php';

$respuesta = "Respuesta del servidor a la modificación.<br>";
try {
    $pdo = conectarBaseDatos();

    $NroFactura       = $_POST['NroFactura']        ?? '';
    $CodProveedor     = $_POST['CodProveedor']      ?? '';
    $DomicilioProv    = $_POST['DomicilioProveedor']?? '';
    $FechaFactura     = $_POST['FechaFactura']      ?? '';
    $CodPlazosEntrega = $_POST['CodPlazosEntrega']  ?? '';
    $TotalNeto        = $_POST['TotalNetoFactura']  ?? 0;

    $respuesta .= "Entradas: Nro=$NroFactura, Prov=$CodProveedor, Fecha=$FechaFactura<br>";

    $sql = "UPDATE factura SET CodProveedor=:CodProveedor, DomicilioProveedor=:DomicilioProveedor,
            FechaFactura=:FechaFactura, CodPlazosEntrega=:CodPlazosEntrega, TotalNetoFactura=:TotalNetoFactura
            WHERE NroFactura=:NroFactura";

    $stmt = $pdo->prepare($sql);
    $respuesta .= "Prepare OK<br>";

    $stmt->bindParam(':CodProveedor', $CodProveedor);
    $stmt->bindParam(':DomicilioProveedor', $DomicilioProv);
    $stmt->bindParam(':FechaFactura', $FechaFactura);
    $stmt->bindParam(':CodPlazosEntrega', $CodPlazosEntrega);
    $stmt->bindParam(':TotalNetoFactura', $TotalNeto);
    $stmt->bindParam(':NroFactura', $NroFactura);
    $respuesta .= "Bind OK<br>";

    $stmt->execute();
    $affected = $stmt->rowCount();
    $respuesta .= "Execute OK, filas afectadas: $affected<br>";

    // Manejo de PDF opcional (reemplazo)
    if (isset($_FILES['PdfComprobante']) && $_FILES['PdfComprobante']['error'] === UPLOAD_ERR_OK) {
        $pdfData = file_get_contents($_FILES['PdfComprobante']['tmp_name']);
        $sql2 = "UPDATE factura SET PdfComprobante=:pdf WHERE NroFactura=:NroFactura";
        $stmt2 = $pdo->prepare($sql2);
        $stmt2->bindParam(':pdf', $pdfData, PDO::PARAM_LOB);
        $stmt2->bindParam(':NroFactura', $NroFactura);
        $stmt2->execute();
        $respuesta .= "PDF actualizado<br>";
    } else {
        $respuesta .= "No se envió PDF para reemplazar<br>";
    }

} catch (Exception $e) {
    $respuesta .= "Error: " . htmlspecialchars($e->getMessage()) . "<br>";
}

$puntero = fopen(__DIR__ . "/errores.log","a");
fwrite($puntero, date("Y-m-d H:i") . " | modi: " . strip_tags($respuesta) . "\n");
fclose($puntero);

echo $respuesta;

?>
<?php
require_once "datosConexionBase.php";
header('Content-Type: text/html; charset=utf-8');

$respuesta_estado = "Parte Modificación simple de datos<br>";

try {
  $pdo = conectarBaseDatos();

  $NroFactura       = $_POST['NroFactura']        ?? '';
  $CodProveedor     = $_POST['CodProveedor']      ?? '';
  $DomicilioProv    = $_POST['DomicilioProveedor']?? '';
  $FechaFactura     = $_POST['FechaFactura']      ?? '';
  $CodPlazosEntrega = $_POST['CodPlazosEntrega']  ?? '';
  $TotalNeto        = $_POST['TotalNetoFactura']  ?? 0;

  $sql = "UPDATE factura SET
            CodProveedor=:CodProveedor,
            DomicilioProveedor=:DomicilioProveedor,
            FechaFactura=:FechaFactura,
            CodPlazosEntrega=:CodPlazosEntrega,
            TotalNetoFactura=:TotalNetoFactura
          WHERE NroFactura=:NroFactura";

  try { $stmt = $pdo->prepare($sql); $respuesta_estado .= "Preparación exitosa<br>"; }
  catch(PDOException $e){ $respuesta_estado .= "Error prepare: ".$e->getMessage()."<br>"; }

  try {
    $stmt->bindParam(':CodProveedor', $CodProveedor);
    $stmt->bindParam(':DomicilioProveedor', $DomicilioProv);
    $stmt->bindParam(':FechaFactura', $FechaFactura);
    $stmt->bindParam(':CodPlazosEntrega', $CodPlazosEntrega);
    $stmt->bindParam(':TotalNetoFactura', $TotalNeto);
    $stmt->bindParam(':NroFactura', $NroFactura);
    $respuesta_estado .= "Binding exitoso<br>";
  } catch(PDOException $e){ $respuesta_estado .= "Error bind: ".$e->getMessage()."<br>"; }

  try { $stmt->execute(); $respuesta_estado .= "Ejecución exitosa<br>"; }
  catch(PDOException $e){ $respuesta_estado .= "Error execute: ".$e->getMessage()."<br>"; }

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
    } catch(PDOException $e){
      $respuesta_estado .= "Error PDF: ".$e->getMessage()."<br>";
    }
  } else {
    $respuesta_estado .= "No se adjuntó PDF<br>";
  }

} catch(PDOException $e){
  $respuesta_estado .= "Error general: ".$e->getMessage()."<br>";
}

$puntero = fopen("./errores.log","a");
fwrite($puntero, date("Y-m-d H:i")." | modi: ".$respuesta_estado."\n");
fclose($puntero);

echo $respuesta_estado;
