const textoFactura = `{
  "Factura":[
  {
    "NroFactura": "F-0001",
    "CodProveedor": "P001",
    "DomicilioProveedor": "Av. Siempreviva 123",
    "Fecha_factura": "2025-09-01",
    "PlazoDeEntregaCod": "PE30",
    "Total_Neto_factura": 15000.00,
    "pdfComprobante": ""
  },
  {
    "NroFactura": "F-0002",
    "CodProveedor": "P002",
    "DomicilioProveedor": "Calle Falsa 456",
    "Fecha_factura": "2025-09-05",
    "PlazoDeEntregaCod": "PE15",
    "Total_Neto_factura": 8200.50,
    "pdfComprobante": ""
  },
  {
    "NroFactura": "F-0003",
    "CodProveedor": "P003",
    "DomicilioProveedor": "Rivadavia 789",
    "Fecha_factura": "2025-09-10",
    "PlazoDeEntregaCod": "PE60",
    "Total_Neto_factura": 23000.00,
    "pdfComprobante": ""
  },
  {
    "NroFactura": "F-0004",
    "CodProveedor": "P004",
    "DomicilioProveedor": "Pueyrredón 101",
    "Fecha_factura": "2025-09-11",
    "PlazoDeEntregaCod": "PE30",
    "Total_Neto_factura": 12500.75,
    "pdfComprobante": ""
  },
  {
    "NroFactura": "F-0005",
    "CodProveedor": "P005",
    "DomicilioProveedor": "Belgrano 202",
    "Fecha_factura": "2025-09-12",
    "PlazoDeEntregaCod": "PE15",
    "Total_Neto_factura": 5400.00,
    "pdfComprobante": ""
  },
  {
    "NroFactura": "F-0006",
    "CodProveedor": "P006",
    "DomicilioProveedor": "Hipólito Yrigoyen 55",
    "Fecha_factura": "2025-09-13",
    "PlazoDeEntregaCod": "PE60",
    "Total_Neto_factura": 19999.99,
    "pdfComprobante": ""
  },
  {
    "NroFactura": "F-0007",
    "CodProveedor": "P007",
    "DomicilioProveedor": "San Martín 303",
    "Fecha_factura": "2025-09-14",
    "PlazoDeEntregaCod": "PE30",
    "Total_Neto_factura": 7600.00,
    "pdfComprobante": ""
  },
  {
    "NroFactura": "F-0008",
    "CodProveedor": "P008",
    "DomicilioProveedor": "Moreno 404",
    "Fecha_factura": "2025-09-15",
    "PlazoDeEntregaCod": "PE15",
    "Total_Neto_factura": 3100.25,
    "pdfComprobante": ""
  },
  {
    "NroFactura": "F-0009",
    "CodProveedor": "P009",
    "DomicilioProveedor": "San Juan 505",
    "Fecha_factura": "2025-09-16",
    "PlazoDeEntregaCod": "PE60",
    "pdfComprobante": ""
  },
  {
    "NroFactura": "F-0010",
    "CodProveedor": "P010",
    "DomicilioProveedor": "Corrientes 606",
    "Fecha_factura": "2025-09-17",
    "PlazoDeEntregaCod": "PE30",
    "Total_Neto_factura": 10000.00,
    "pdfComprobante": ""
  }
]
}`;

const textoPlazos = '[{"cod":"PE15","nroDias":15},{"cod":"PE30","nroDias":30},{"cod":"PE60","nroDias":60}]';
let plazosEntrega = JSON.parse(textoPlazos);

const columnas = [
  "NroFact",
  "CodProv",
  "DomicilioProv",
  "Fecha factura",
  "Plazo de entrega",
  "Total Neto factura",
  "pdfComprobante"
];

const mapaColumnas = {
  "NroFact": "NroFactura",
  "CodProv": "CodProveedor",
  "DomicilioProv": "DomicilioProveedor",
  "Fecha factura": "Fecha_factura",
  "Plazo de entrega": "PlazoDeEntregaCod",
  "Total Neto factura": "Total_Neto_factura",
  "pdfComprobante": "pdfComprobante"
};

let datosFactura = [];

function obtenerNroDiasPorCodigo(cod) {
  let resultado = null;
  plazosEntrega.forEach(function(p) {
    if (p.cod === cod) resultado = p.nroDias;
  });
  return resultado;
}

function crearTablaDesdeDatos(arr, contenedorTabla) {
  contenedorTabla.innerHTML = ''; 
  const tabla = document.createElement('table');
  tabla.className = 'tabla-datos';

  const objTr = document.createElement('tr');
  columnas.forEach(function(col) {
    const td = document.createElement('td');
    td.innerText = col;
    td.className = 'celda-encabezado';
    objTr.appendChild(td);
  });
  tabla.appendChild(objTr);

  arr.forEach(function(item) {
    const fila = document.createElement('tr');

    columnas.forEach(function(col) {
      const td = document.createElement('td');
      const clave = mapaColumnas[col];

      if (clave === 'PlazoDeEntregaCod') {
        const codigo = item[clave];
        const dias = obtenerNroDiasPorCodigo(codigo);
        td.innerText = (dias !== null) ? String(dias) + ' días' : String(codigo || '');
      } else {
        const valor = item[clave];
        td.innerText = (valor !== undefined && valor !== null) ? String(valor) : '';
      }

      fila.appendChild(td);
    });

    tabla.appendChild(fila);
  });

  contenedorTabla.appendChild(tabla);
}

function cargarDatos(contenedorTabla) {
  if (datosFactura.length > 0) {
    alert('Los datos ya fueron cargados');
    return;
  }
  const obj = JSON.parse(textoFactura);
  datosFactura = obj.Factura;
  crearTablaDesdeDatos(datosFactura, contenedorTabla);
}

function vaciarDatos(contenedorTabla) {
  datosFactura = [];
  if (contenedorTabla) contenedorTabla.innerHTML = '';
}

function poblarSelectPlazos(selectPlazo) {
  selectPlazo.innerHTML = '';

  const opcionVacia = document.createElement('option');
  opcionVacia.value = '';
  opcionVacia.textContent = '-- Seleccione plazo de entrega --';
  selectPlazo.appendChild(opcionVacia);

  plazosEntrega.forEach(function(plazo) {
    const opcion = document.createElement('option');
    opcion.value = plazo.cod;
    opcion.innerHTML = String(plazo.nroDias) + ' días';
    selectPlazo.appendChild(opcion);
  });
}

document.addEventListener('DOMContentLoaded', function() {
  const contenedorTabla = document.getElementById('contenedor-tabla');
  const btnCargar = document.getElementById('btn-cargar');
  const btnVaciar = document.getElementById('btn-vaciar');
  const btnModal = document.getElementById('btn-modal');
  const botonCerrar = document.getElementById("cerrar_modal");
  const contenedor = document.getElementById("contenedor");
  const fondoModal = document.getElementById("fondo_modal");
  const selectPlazo = document.getElementById('PlazoDeEntregaCod');

  btnCargar.addEventListener('click', function() { cargarDatos(contenedorTabla); });
  btnVaciar.addEventListener('click', function() { vaciarDatos(contenedorTabla); });

  btnModal.addEventListener("click", () => {
    fondoModal.style.display = "flex";
    contenedor.classList.add("desactivado");
  });

  botonCerrar.addEventListener("click", () => {
    fondoModal.style.display = "none";
    contenedor.classList.remove("desactivado");
  });

  if (selectPlazo) poblarSelectPlazos(selectPlazo);
});
