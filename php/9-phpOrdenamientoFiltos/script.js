// script.js - actualización para consumir los endpoints PHP

document.addEventListener('DOMContentLoaded', function() {
  // elementos
  const contenedorTabla = document.getElementById('contenedor-tabla');
  const btnCargar = document.getElementById('btn-cargar');
  const btnVaciar = document.getElementById('btn-vaciar');
  const btnModal = document.getElementById('btn-modal');
  const botonCerrar = document.getElementById("cerrar_modal");
  const contenedor = document.getElementById("contenedor");
  const fondoModal = document.getElementById("fondo_modal");
  const selectPlazo = document.getElementById('PlazoDeEntregaCod');

  // filtros (suponiendo que agregaste inputs con estos ids en tu index.php o los crearás)
  const inputNroFactura = document.getElementById('NroFacturaFiltro') || null;
  const inputCodProveedor = document.getElementById('CodProveedorFiltro') || null;
  const inputDomicilio = document.getElementById('DomicilioProveedorFiltro') || null;
  const inputFecha = document.getElementById('FechaFacturaFiltro') || null;
  const inputCodPlazoFiltro = document.getElementById('CodPlazoFiltro') || null;

  // estado de ordenamiento
  let orderBy = 'NroFactura';
  let orderDir = 'ASC';

  // ---------------------------
  // Cargar plazos (alert post-carga)
  // ---------------------------
  function cargarPlazos() {
    fetch('salidaJsonPlazos.php')
      .then(resp => resp.json())
      .then(data => {
        alert('ALERTA: JSON de familias/plazos recibido:\n' + JSON.stringify(data));
        // poblar select
        if (selectPlazo && data.plazos) {
          selectPlazo.innerHTML = '<option value="">-- Seleccione plazo de entrega --</option>';
          data.plazos.forEach(p => {
            const opt = document.createElement('option');
            opt.value = p.cod;
            opt.textContent = p.nroDias + ' días';
            selectPlazo.appendChild(opt);
          });
        }

        // Si existe filter select aparte
        if (inputCodPlazoFiltro && data.plazos) {
          inputCodPlazoFiltro.innerHTML = '<option value="">-- --</option>';
          data.plazos.forEach(p => {
            const opt = document.createElement('option');
            opt.value = p.cod;
            opt.textContent = p.cod + ' - ' + p.nroDias + ' días';
            inputCodPlazoFiltro.appendChild(opt);
          });
        }
      })
      .catch(err => {
        console.error(err);
        alert('Error al cargar plazos');
      });
  }

  // Cargar plazos al inicio (requisito: alerta de carga)
  cargarPlazos();

  // ---------------------------
  // Construir querystring con filtros y orden
  // ---------------------------
  function construirQuery() {
    const params = new URLSearchParams();

    if (inputNroFactura && inputNroFactura.value.trim() !== '') params.append('NroFactura', inputNroFactura.value.trim());
    if (inputCodProveedor && inputCodProveedor.value.trim() !== '') params.append('CodProveedor', inputCodProveedor.value.trim());
    if (inputDomicilio && inputDomicilio.value.trim() !== '') params.append('DomicilioProveedor', inputDomicilio.value.trim());
    if (inputFecha && inputFecha.value.trim() !== '') params.append('FechaFactura', inputFecha.value.trim());
    if (inputCodPlazoFiltro && inputCodPlazoFiltro.value.trim() !== '') params.append('CodPlazoEntrega', inputCodPlazoFiltro.value.trim());

    params.append('order_by', orderBy);
    params.append('order_dir', orderDir);

    // pagination optional
    params.append('limit', 100);
    params.append('offset', 0);

    return params.toString();
  }

  // ---------------------------
  // Mostrar "Esperando respuesta" en tbody
  // ---------------------------
  function mostrarEsperandoRespuesta() {
    contenedorTabla.innerHTML = '';
    const tabla = document.createElement('table');
    tabla.className = 'tabla-datos';

    const thead = document.createElement('thead');
    const trHead = document.createElement('tr');
    ['NroFactura','CodProveedor','DomicilioProveedor','Fecha_factura','PlazoDeEntregaCod','Total_Neto_factura','pdfComprobante']
      .forEach(col => {
        const th = document.createElement('th');
        th.textContent = col;
        th.style.cursor = 'pointer';
        th.dataset.col = col;
        // manejo de click para ordenar
        th.addEventListener('click', () => {
          if (orderBy === col) orderDir = (orderDir === 'ASC') ? 'DESC' : 'ASC';
          else { orderBy = col; orderDir = 'ASC'; }
          // recargar con nuevo orden
          cargarDatos();
        });
        trHead.appendChild(th);
    });
    thead.appendChild(trHead);
    tabla.appendChild(thead);

    const tbody = document.createElement('tbody');
    const tr = document.createElement('tr');
    const td = document.createElement('td');
    td.colSpan = 7;
    td.textContent = 'Esperando respuesta...';
    tr.appendChild(td);
    tbody.appendChild(tr);
    tabla.appendChild(tbody);

    // tfoot con espacio para total
    const tfoot = document.createElement('tfoot');
    const trFoot = document.createElement('tr');
    const tdFoot = document.createElement('td');
    tdFoot.colSpan = 7;
    tdFoot.id = 'tabla-footer-info';
    tdFoot.textContent = 'Registros: 0';
    trFoot.appendChild(tdFoot);
    tfoot.appendChild(trFoot);
    tabla.appendChild(tfoot);

    contenedorTabla.appendChild(tabla);
  }

  // ---------------------------
  // Renderizar datos
  // ---------------------------
  function renderizarTabla(rows, total) {
    contenedorTabla.innerHTML = '';
    const tabla = document.createElement('table');
    tabla.className = 'tabla-datos';

    // header
    const thead = document.createElement('thead');
    const trHead = document.createElement('tr');
    const columnas = ['NroFactura','CodProveedor','DomicilioProveedor','Fecha_factura','PlazoDeEntregaCod','Total_Neto_factura','pdfComprobante'];
    columnas.forEach(col => {
      const th = document.createElement('th');
      th.textContent = col;
      th.style.cursor = 'pointer';
      th.dataset.col = col;
      // click para ordenar
      th.addEventListener('click', () => {
        if (orderBy === col) orderDir = (orderDir === 'ASC') ? 'DESC' : 'ASC';
        else { orderBy = col; orderDir = 'ASC'; }
        cargarDatos();
      });
      trHead.appendChild(th);
    });
    thead.appendChild(trHead);
    tabla.appendChild(thead);

    // tbody
    const tbody = document.createElement('tbody');
    rows.forEach(item => {
      const tr = document.createElement('tr');
      columnas.forEach(c => {
        const td = document.createElement('td');
        // mapear nombres si es necesario
        td.textContent = item[c] !== null && item[c] !== undefined ? item[c] : '';
        tr.appendChild(td);
      });
      tbody.appendChild(tr);
    });
    tabla.appendChild(tbody);

    // tfoot
    const tfoot = document.createElement('tfoot');
    const trFoot = document.createElement('tr');
    const tdFoot = document.createElement('td');
    tdFoot.colSpan = columnas.length;
    tdFoot.textContent = `Registros: ${total} | Tabla: facturas | Usuario: TuApellido TuNombre`;
    trFoot.appendChild(tdFoot);
    tfoot.appendChild(trFoot);
    tabla.appendChild(tfoot);

    contenedorTabla.appendChild(tabla);
  }

  // ---------------------------
  // Cargar datos desde servidor (con alert pre y post)
  // ---------------------------
  function cargarDatos() {
    const query = construirQuery();
    const url = 'salidaJsonFactura.php?' + query;

    // Alerta pre-Ajax con la URL (cadena de parámetros)
    alert('ALERTA PRE-AJAX:\n' + url);

    // Mostrar esperando
    mostrarEsperandoRespuesta();

    fetch(url)
      .then(resp => resp.json())
      .then(data => {
        // Alerta post-respuesta
        alert('ALERTA POST-RESPUESTA:\n' + JSON.stringify(data));

        if (data.error) {
          alert('Error del servidor: ' + data.error);
          renderizarTabla([], 0);
          return;
        }

        // data.rows y data.total
        renderizarTabla(data.rows || [], data.total || 0);
      })
      .catch(err => {
        console.error(err);
        alert('Error en fetch: ' + err.message);
        renderizarTabla([], 0);
      });
  }

  // Vaciar datos (elimina el tbody)
  function vaciarDatos() {
    contenedorTabla.innerHTML = '';
  }

  // ---------------------------
  // Eventos
  // ---------------------------
  btnCargar.addEventListener('click', cargarDatos);
  btnVaciar.addEventListener('click', vaciarDatos);

  btnModal.addEventListener("click", () => {
    fondoModal.style.display = "flex";
    contenedor.classList.add("desactivado");
  });

  botonCerrar.addEventListener("click", () => {
    fondoModal.style.display = "none";
    contenedor.classList.remove("desactivado");
  });

  // Si querés cargar datos al inicio, podés llamar:
  // cargarDatos();
});
