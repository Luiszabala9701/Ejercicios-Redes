<?php
header('Content-Type: application/json; charset=utf-8');
require_once "datosConexionBase.php";

try {
    $puntero = fopen("./Errores.log", "a");
    fwrite($puntero, "llega salidaJsonFactura? " . ($_SERVER['QUERY_STRING'] ?? ''));
    fwrite($puntero, " | " . date("Y-m-d H:i"));
    fwrite($puntero, "\n");
    fclose($puntero);

    $pdo = conectarBaseDatos();

    // Filtros
    $pNroFactura   = isset($_GET['NroFactura']) ? trim($_GET['NroFactura']) : '';
    $pCodProveedor = isset($_GET['CodProveedor']) ? trim($_GET['CodProveedor']) : '';
    $pDomicilio    = isset($_GET['DomicilioProveedor']) ? trim($_GET['DomicilioProveedor']) : '';
    $pFecha        = isset($_GET['FechaFactura']) ? trim($_GET['FechaFactura']) : '';
    $pCodPlazo     = isset($_GET['CodPlazoEntrega']) ? trim($_GET['CodPlazoEntrega']) : '';

    // Orden - nombres de columnas corregidos según tu DB
    $allowedColumns = ['NroFactura','CodProveedor','DomicilioProveedor','FechaFactura','CodPlazosEntrega','TotalNetoFactura'];
    $order_by  = (isset($_GET['order_by']) && in_array($_GET['order_by'], $allowedColumns)) ? $_GET['order_by'] : 'NroFactura';
    $order_dir = (isset($_GET['order_dir']) && strtoupper($_GET['order_dir']) === 'DESC') ? 'DESC' : 'ASC';

    $where  = [];
    $params = [];

    if ($pNroFactura !== '') {
        $where[] = "NroFactura LIKE :NroFactura";
        $params[':NroFactura'] = "%$pNroFactura%";
    }
    if ($pCodProveedor !== '') {
        $where[] = "CodProveedor LIKE :CodProveedor";
        $params[':CodProveedor'] = "%$pCodProveedor%";
    }
    if ($pDomicilio !== '') {
        $where[] = "DomicilioProveedor LIKE :DomicilioProveedor";
        $params[':DomicilioProveedor'] = "%$pDomicilio%";
    }
    if ($pFecha !== '') {
        $where[] = "FechaFactura = :FechaFactura";
        $params[':FechaFactura'] = $pFecha;
    }
    if ($pCodPlazo !== '') {
        $where[] = "CodPlazosEntrega = :CodPlazosEntrega";
        $params[':CodPlazosEntrega'] = $pCodPlazo;
    }

    $sqlWhere = (count($where) > 0) ? (' WHERE ' . implode(' AND ', $where)) : '';

    $sql = "SELECT NroFactura, CodProveedor, DomicilioProveedor, FechaFactura,
                   CodPlazosEntrega, TotalNetoFactura, PdfComprobante
            FROM factura
            $sqlWhere
            ORDER BY $order_by $order_dir";

    $stmt = $pdo->prepare($sql);
    foreach ($params as $k => $v) {
        $stmt->bindValue($k, $v);
    }
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $countSql = "SELECT COUNT(*) FROM factura $sqlWhere";
    $countStmt = $pdo->prepare($countSql);
    foreach ($params as $k => $v) {
        $countStmt->bindValue($k, $v);
    }
    $countStmt->execute();
    $total = (int)$countStmt->fetchColumn();

    $puntero = fopen("./Errores.log", "a");
    fwrite($puntero, "ok salidaJsonFactura rows=" . count($rows) . " total=" . $total);
    fwrite($puntero, " | " . date("Y-m-d H:i"));
    fwrite($puntero, "\n");
    fclose($puntero);

    echo json_encode(["total" => $total, "rows" => $rows], JSON_UNESCAPED_UNICODE);

} catch (PDOException $error) {
    $puntero = fopen("./Errores.log", "a");
    fwrite($puntero, "Error en salidaJsonFactura: " . $error->getMessage());
    fwrite($puntero, " | " . date("Y-m-d H:i"));
    fwrite($puntero, "\n");
    fclose($puntero);

    echo json_encode(["error" => "Error al obtener los datos de facturas."]);
}
