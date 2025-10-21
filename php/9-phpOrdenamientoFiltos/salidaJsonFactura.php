<?php
// Listado/filtrado/orden de facturas en JSON + logging estilo apunte
header('Content-Type: application/json; charset=utf-8');
require_once "datosConexionBase.php";

try {
    // Log de llegada con query string
    $puntero = fopen("./errores.log", "a");
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

    // Orden
    $allowedColumns = ['NroFactura','CodProveedor','DomicilioProveedor','Fecha_factura','PlazoDeEntregaCod','Total_Neto_factura'];
    $order_by  = (isset($_GET['order_by']) && in_array($_GET['order_by'], $allowedColumns)) ? $_GET['order_by'] : 'NroFactura';
    $order_dir = (isset($_GET['order_dir']) && strtoupper($_GET['order_dir']) === 'DESC') ? 'DESC' : 'ASC';

    // WHERE dinámico
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
        $where[] = "Fecha_factura = :FechaFactura";
        $params[':FechaFactura'] = $pFecha;
    }
    if ($pCodPlazo !== '') {
        $where[] = "PlazoDeEntregaCod = :PlazoDeEntregaCod";
        $params[':PlazoDeEntregaCod'] = $pCodPlazo;
    }

    $sqlWhere = (count($where) > 0) ? (' WHERE ' . implode(' AND ', $where)) : '';

    // Consulta principal
    $sql = "SELECT NroFactura, CodProveedor, DomicilioProveedor, Fecha_factura,
                   PlazoDeEntregaCod, Total_Neto_factura, pdfComprobante
            FROM facturas
            $sqlWhere
            ORDER BY $order_by $order_dir";

    $stmt = $pdo->prepare($sql);
    foreach ($params as $k => $v) {
        $stmt->bindValue($k, $v);
    }
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Total para footer
    $countSql = "SELECT COUNT(*) FROM facturas $sqlWhere";
    $countStmt = $pdo->prepare($countSql);
    foreach ($params as $k => $v) {
        $countStmt->bindValue($k, $v);
    }
    $countStmt->execute();
    $total = (int)$countStmt->fetchColumn();

    // Log de éxito con cantidad
    $puntero = fopen("./errores.log", "a");
    fwrite($puntero, "ok salidaJsonFactura rows=" . count($rows) . " total=" . $total);
    fwrite($puntero, " | " . date("Y-m-d H:i"));
    fwrite($puntero, "\n");
    fclose($puntero);

    echo json_encode(["total" => $total, "rows" => $rows], JSON_UNESCAPED_UNICODE);

} catch (PDOException $error) {
    // Log de error según apunte
    $puntero = fopen("./errores.log", "a");
    fwrite($puntero, "Error en salidaJsonFactura: " . $error->getMessage());
    fwrite($puntero, " | " . date("Y-m-d H:i"));
    fwrite($puntero, "\n");
    fclose($puntero);

    echo json_encode(["error" => "Error al obtener los datos de facturas."]);
}
