document.addEventListener('DOMContentLoaded', function() {
  const contenedorTabla = document.getElementById('contenedor-tabla');
  const btnCargar = document.getElementById('btn-cargar');
  const btnVaciar = document.getElementById('btn-vaciar');
  const btnModal = document.getElementById('btn-modal');
  const botonCerrar = document.getElementById("cerrar_modal");
  const contenedor = document.getElementById("contenedor");
  const fondoModal = document.getElementById("fondo_modal");
  const selectPlazo = document.getElementById('PlazoDeEntregaCod');
  const formularioAlta = document.getElementById('formAlta');

  const inputNroFactura = document.getElementById('NroFacturaFiltro') || null;
  const inputCodProveedor = document.getElementById('CodProveedorFiltro') || null;
  const inputDomicilio = document.getElementById('DomicilioProveedorFiltro') || null;
  const inputFecha = document.getElementById('FechaFacturaFiltro') || null;
  const inputCodPlazoFiltro = document.getElementById('CodPlazoFiltro') || null;

  let orderBy = 'NroFactura';
  let orderDir = 'ASC';

  function cargarPlazos() {
    fetch('salidaJsonPlazos.php')
      .then(resp => resp.json())
      .then(data => {
        alert('ALERTA: JSON de plazosentrega:\n' + JSON.stringify(data));
        if (selectPlazo && data.plazos) {
          selectPlazo.innerHTML = '<option value="">-- Seleccione plazo de entrega --</option>';
          data.plazos.forEach(p => {
            const opt = document.createElement('option');
            opt.value = p.cod;
            opt.textContent = p.nroDias + ' días';
            selectPlazo.appendChild(opt);
          });
        }

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

  cargarPlazos();

  function construirQuery() {
    const params = new URLSearchParams();

    if (inputNroFactura && inputNroFactura.value.trim() !== '') params.append('NroFactura', inputNroFactura.value.trim());
    if (inputCodProveedor && inputCodProveedor.value.trim() !== '') params.append('CodProveedor', inputCodProveedor.value.trim());
    if (inputDomicilio && inputDomicilio.value.trim() !== '') params.append('DomicilioProveedor', inputDomicilio.value.trim());
    if (inputFecha && inputFecha.value.trim() !== '') params.append('FechaFactura', inputFecha.value.trim());
    if (inputCodPlazoFiltro && inputCodPlazoFiltro.value.trim() !== '') params.append('CodPlazoEntrega', inputCodPlazoFiltro.value.trim());

    params.append('order_by', orderBy);
    params.append('order_dir', orderDir);

    params.append('limit', 100);
    params.append('offset', 0);

    return params.toString();
  }

  function mostrarEsperandoRespuesta() {
    contenedorTabla.innerHTML = '';
    const tabla = document.createElement('table');
    tabla.className = 'tabla-datos';

    const thead = document.createElement('thead');
    const trHead = document.createElement('tr');

    ['NroFactura','CodProveedor','DomicilioProveedor','FechaFactura','CodPlazosEntrega','TotalNetoFactura','PdfComprobante']
      .forEach(col => {
        const th = document.createElement('th');
        th.textContent = col;
        th.style.cursor = 'pointer';
        th.dataset.col = col;
        
        th.addEventListener('click', () => {
          if (orderBy === col) orderDir = (orderDir === 'ASC') ? 'DESC' : 'ASC';
          else { orderBy = col; orderDir = 'ASC'; }
          
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

  function renderizarTabla(rows, total) {
    contenedorTabla.innerHTML = '';
    const tabla = document.createElement('table');
    tabla.className = 'tabla-datos';

   
    const thead = document.createElement('thead');
    const trHead = document.createElement('tr');
    
    const columnas = ['NroFactura','CodProveedor','DomicilioProveedor','FechaFactura','CodPlazosEntrega','TotalNetoFactura','PdfComprobante'];
    columnas.forEach(col => {
      const th = document.createElement('th');
      th.textContent = col;
      th.style.cursor = 'pointer';
      th.dataset.col = col;
      
      th.addEventListener('click', () => {
        if (orderBy === col) orderDir = (orderDir === 'ASC') ? 'DESC' : 'ASC';
        else { orderBy = col; orderDir = 'ASC'; }
        cargarDatos();
      });
      trHead.appendChild(th);
    });
    thead.appendChild(trHead);
    tabla.appendChild(thead);

    
    const tbody = document.createElement('tbody');
    rows.forEach(item => {
      const tr = document.createElement('tr');
      columnas.forEach(c => {
        const td = document.createElement('td');
        
        td.textContent = item[c] !== null && item[c] !== undefined ? item[c] : '';
        tr.appendChild(td);
      });
      tbody.appendChild(tr);
    });
    tabla.appendChild(tbody);

   
    const tfoot = document.createElement('tfoot');
    const trFoot = document.createElement('tr');
    const tdFoot = document.createElement('td');
    tdFoot.colSpan = columnas.length;
    tdFoot.textContent = `Registros: ${total} | Tabla: facturas | Usuario: Zabala Luis`;
    trFoot.appendChild(tdFoot);
    tfoot.appendChild(trFoot);
    tabla.appendChild(tfoot);

    contenedorTabla.appendChild(tabla);
  }

 
  function cargarDatos() {
    const query = construirQuery();
    const url = 'salidaJsonFactura.php?' + query;

    alert('ALERTA PRE-AJAX:\n' + url);

    
    mostrarEsperandoRespuesta();

    fetch(url)
      .then(resp => resp.json())
      .then(data => {
        alert('ALERTA POST-RESPUESTA:\n' + JSON.stringify(data));

        if (data.error) {
          alert('Error del servidor: ' + data.error);
          renderizarTabla([], 0);
          return;
        }
        renderizarTabla(data.rows || [], data.total || 0);
      })
      .catch(err => {
        console.error(err);
        alert('Error en fetch: ' + err.message);
        renderizarTabla([], 0);
      });
  }

 
  function vaciarDatos() {
    contenedorTabla.innerHTML = '';
  }


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


  if (formularioAlta) {
    const contenidoModal = document.getElementById('contenido_modal');
    const contenidoOriginal = contenidoModal.innerHTML;
    
    formularioAlta.addEventListener('submit', function(e) {
      e.preventDefault();

 
      const formData = new FormData(formularioAlta);
      const datos = {};
      for (let [key, value] of formData.entries()) {
        datos[key] = value;
      }

 
      const htmlRespuesta = `
        <div class="encabezadoFormulario">
          <h2>Datos enviados correctamente</h2>
        </div>
        <div class="principalFormulario">
          <div class="respuesta-container">
            <table class="respuesta-tabla">
              <tr>
                <th>Campo</th>
                <th>Valor</th>
              </tr>
              <tr>
                <td>Número de Factura:</td>
                <td>${datos.NroFactura || ''}</td>
              </tr>
              <tr>
                <td>Código Proveedor:</td>
                <td>${datos.CodProveedor || ''}</td>
              </tr>
              <tr>
                <td>Domicilio Proveedor:</td>
                <td>${datos.DomicilioProveedor || ''}</td>
              </tr>
              <tr>
                <td>Fecha Factura:</td>
                <td>${datos.FechaFactura || ''}</td>
              </tr>
              <tr>
                <td>Plazo de Entrega:</td>
                <td>${datos.PlazoDeEntregaCod || ''}</td>
              </tr>
              <tr>
                <td>Total Neto Factura:</td>
                <td>${datos.TotalNetoFactura || ''}</td>
              </tr>
            </table>
          </div>
        </div>
        <div class="pieFormulario">
          <button type="button" id="btnVolverFormulario" class="btn-volver">VOLVER AL FORMULARIO</button>
        </div>
      `;

      contenidoModal.innerHTML = htmlRespuesta;

      document.getElementById('btnVolverFormulario').addEventListener('click', function() {
        contenidoModal.innerHTML = contenidoOriginal;
        
        const nuevoForm = document.getElementById('formAlta');
        if (nuevoForm) {
          nuevoForm.addEventListener('submit', arguments.callee);
        }

        cargarPlazos();
      });
    });
  }
});
