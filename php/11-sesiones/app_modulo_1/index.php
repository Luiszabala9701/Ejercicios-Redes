<?php
// Protección de sesión
session_start();
include __DIR__ . '/../manejoSesion.inc';

// Obtener datos del usuario
$nombreUsuario = $_SESSION['usuario_apellido'] . ' ' . $_SESSION['usuario_nombres'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>ABM Facturas</title>
  <link rel="stylesheet" href="estilos.css" />
</head>
<body>
  <header class="topbar">
    <h1>Facturas.</h1>
    <div class="controls">
      <label class="orden-label">Orden:</label>
      <select id="selectOrden" class="orden-select">
        <option value="">-- Seleccione --</option>
        <option value="NroFactura" selected>NroFactura</option>
        <option value="CodProveedor">CodProveedor</option>
        <option value="DomicilioProveedor">DomicilioProveedor</option>
        <option value="FechaFactura">FechaFactura</option>
        <option value="CodPlazosEntrega">CodPlazosEntrega</option>
        <option value="TotalNetoFactura">TotalNetoFactura</option>
      </select>

      <button id="btn-cargar">Cargar datos</button>
      <button id="btn-vaciar">Vaciar datos</button>
      <button id="btn-limpiar">Limpiar filtros</button>
      <button id="btn-alta">Alta registro</button>
      <button id="btn-cerrar-sesion">Cierra Sesión</button>
    </div>
  </header>

  <main class="main-board">
    <div class="table-wrap">
      <table id="tablaFacturas">
        <thead>
          <tr class="header-row">
            <th>NroFactura</th>
            <th>CodProveedor</th>
            <th>DomicilioProveedor</th>
            <th>FechaFactura</th>
            <th>CodPlazosEntrega</th>
            <th>TotalNetoFactura</th>
            <th>PDF</th>
            <th>Acciones</th>
          </tr>
          <tr class="filter-row">
            <th><input id="fNro" type="text" placeholder=""></th>
            <th><input id="fProv" type="text"></th>
            <th><input id="fDom" type="text"></th>
            <th><input id="fFecha" type="date"></th>
            <th><select id="fPlazo"><option value="">(todos)</option></select></th>
            <th><input id="fTotal" type="number" step="0.01" min="0"></th>
            <th></th>
            <th></th>
          </tr>
        </thead>
        <tbody id="cuerpoTabla">
          <!-- filas cargadas por JS -->
        </tbody>
      </table>
    </div>
  </main>

  <footer class="pie">
    <p>Registros: <span id="conteoRegistros">0</span> | Tabla: facturas | Usuario: <?php echo htmlspecialchars($nombreUsuario); ?></p>
  </footer>

  <!-- Modal Formulario (Alta/Modi: reutilizamos misma ventana) -->
  <div id="ventanaModalFormulario" class="modal">
    <div class="modal-contenido">
      <div class="modal-encabezado">
        <h2 id="tituloModalForm">Formulario</h2>
        <button id="cerrarModalForm" class="bt-cerrar">X</button>
      </div>
      <div class="modal-cuerpo">
        <form id="formArticulos" method="post" enctype="multipart/form-data">
          <ul>
            <li>
              <label>NroFactura:</label>
              <input id="entNroFactura" name="NroFactura" required />
            </li>
            <li>
              <label>CodProveedor:</label>
              <input id="entCodProveedor" name="CodProveedor" required />
            </li>
            <li>
              <label>Domicilio proveedor:</label>
              <input id="entDomicilioProveedor" name="DomicilioProveedor" required />
            </li>
            <li>
              <label>Fecha factura:</label>
              <input type="date" id="entFechaFactura" name="FechaFactura" required />
            </li>
            <li>
              <label>Plazo de entrega:</label>
              <select id="entPlazo" name="CodPlazosEntrega" required></select>
            </li>
            <li>
              <label>Total neto factura:</label>
              <input type="number" step="0.01" id="entTotal" name="TotalNetoFactura" required />
            </li>
            <li>
              <label>PDF comprobante (opcional):</label>
              <input type="file" id="entPdf" name="PdfComprobante" accept="application/pdf" />
            </li>
          </ul>

          <div class="modal-pie">
            <button type="button" id="btnEnviar" class="btn" disabled>Guardar</button>
            <button type="button" id="btnCancelar" class="btn">Cancelar</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal Respuesta del servidor -->
  <div id="modalResp" class="modal">
    <div class="modal-contenido">
      <div class="modal-encabezado">
        <h2>Respuesta del servidor</h2>
        <button id="closeResp" class="bt-cerrar">X</button>
      </div>
      <div id="respBody" class="modal-cuerpo"></div>
      <div class="modal-pie">
        <button id="btnRespCerrar" class="btn">Cerrar</button>
      </div>
    </div>
  </div>

  <!-- Modal PDF -->
  <div id="modalPdf" class="modal">
    <div class="modal-contenido">
      <div class="modal-encabezado">
        <h2>Documento PDF</h2>
        <button id="closePdf" class="bt-cerrar">X</button>
      </div>
      <div id="pdfBody" class="modal-cuerpo">
        <iframe id="pdfFrame"></iframe>
      </div>
      <div class="modal-pie">
        <button id="btnPdfCerrar" class="btn">Cerrar</button>
      </div>
    </div>
  </div>

  <script src="script.js"></script>
  <script>
    document.getElementById('btn-cerrar-sesion').addEventListener('click', function() {
      window.location.href = '../destruirSesion.php';
    });
  </script>
</body>
</html>
