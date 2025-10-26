// app.js - frontend para ABM (UI like the provided image)
const familiaUrl = 'salidaJsonFamilia.php';
const altaUrl = 'alta.php';
const modiUrl = 'modi.php';
const bajaUrl = 'baja.php';
const traePdfUrl = 'traeDoc.php';

let dataRows = []; // dataset local

// DOM
const cuerpo = document.getElementById('cuerpoTabla');
const fFamilia = document.getElementById('fFamilia');
const selectOrden = document.getElementById('selectOrden');

// modales
const modalForm = document.getElementById('modalForm');
const modalResp = document.getElementById('modalResp');
const modalPdf = document.getElementById('modalPdf');
const respBody = document.getElementById('respBody');

// form
const formABM = document.getElementById('formABM');
const btnEnviar = document.getElementById('btnEnviar');

// inicial
document.getElementById('btnCargar').addEventListener('click', cargarEjemplo);
document.getElementById('btnVaciar').addEventListener('click', vaciarDatos);
document.getElementById('btnLimpiar').addEventListener('click', limpiarFiltros);
document.getElementById('btnAlta').addEventListener('click', ()=>openForm('alta'));

// cerrar modales
document.getElementById('closeForm').addEventListener('click', ()=>closeModal(modalForm));
document.getElementById('btnCancelar').addEventListener('click', ()=>closeModal(modalForm));
document.getElementById('closeResp').addEventListener('click', ()=>closeModal(modalResp));
document.getElementById('btnRespCerrar').addEventListener('click', ()=>closeModal(modalResp));
document.getElementById('closePdf').addEventListener('click', ()=>closeModal(modalPdf));
document.getElementById('btnPdfCerrar').addEventListener('click', ()=>closeModal(modalPdf));

// enviar form (alta/modi)
btnEnviar.addEventListener('click', async (e)=>{
  e.preventDefault();
  const formData = new FormData(formABM);
  // decide URL: si hay NroFactura (hidden) -> modi, sino alta
  const nro = document.getElementById('formNro').value;
  const url = nro ? modiUrl : altaUrl;

  try {
    const resp = await fetch(url, {method:'POST', body: formData});
    const text = await resp.text();
    showResponse(text);
    closeModal(modalForm);
    // recargar lista local (en producción: recargar desde servidor)
    cargarEjemplo();
  } catch(err){
    showResponse('Error de red: '+err.message);
  }
});

// util modal
function openModal(el){el.setAttribute('aria-hidden','false')}
function closeModal(el){el.setAttribute('aria-hidden','true')}

function showResponse(html){
  respBody.innerHTML = html.replace(/\n/g, '<br>');
  openModal(modalResp);
}

// cargar familias para select
async function cargarFamilias(){
  try{
    const r = await fetch(familiaUrl);
    const arr = await r.json();
    // llenar select filtro y form
    fFamilia.innerHTML = '<option value="">(todos)</option>' + arr.map(f=>`<option value="${f.id}">${escapeHtml(f.descripcion)}</option>`).join('');
    document.getElementById('formFamilia').innerHTML = arr.map(f=>`<option value="${f.id}">${escapeHtml(f.descripcion)}</option>`).join('');
  }catch(e){console.warn('No se pudieron cargar familias',e)}
}

// muestra dataset en tabla
function renderTabla(){
  const filtroCod = document.getElementById('fCod').value.trim().toLowerCase();
  const filtroFamilia = document.getElementById('fFamilia').value;
  const filtroUm = document.getElementById('fUm').value.trim().toLowerCase();
  const filtroDesc = document.getElementById('fDesc').value.trim().toLowerCase();
  const filtroFecha = document.getElementById('fFecha').value;

  const orden = selectOrden.value;
  const rows = dataRows.filter(r=>{
    if(filtroCod && !r.codArt.toLowerCase().includes(filtroCod)) return false;
    if(filtroFamilia && r.familiaId != filtroFamilia) return false;
    if(filtroUm && !r.um.toLowerCase().includes(filtroUm)) return false;
    if(filtroDesc && !r.descrip.toLowerCase().includes(filtroDesc)) return false;
    if(filtroFecha && r.fechaAlta !== filtroFecha) return false;
    return true;
  }).sort((a,b)=> a[orden] > b[orden] ? 1 : -1);

  cuerpo.innerHTML = rows.map(row=>{
    return `<tr>
      <td>${escapeHtml(row.codArt)}</td>
      <td>${escapeHtml(row.familia)}</td>
      <td>${escapeHtml(row.um)}</td>
      <td>${escapeHtml(row.descrip)}</td>
      <td>${escapeHtml(row.fechaAlta)}</td>
      <td>${escapeHtml(String(row.saldo))}</td>
      <td><button class="btn-mini" data-nro="${escapeHtml(row.codArt)}" data-action="pdf">PDF</button></td>
      <td><button class="btn-mini" data-nro="${escapeHtml(row.codArt)}" data-action="modi">Modi</button></td>
      <td><button class="btn-mini" data-nro="${escapeHtml(row.codArt)}" data-action="baja">Borrar</button></td>
    </tr>`
  }).join('');

  // attach listeners for row buttons
  Array.from(document.querySelectorAll('#cuerpoTabla button')).forEach(btn=>{
    btn.addEventListener('click', onRowAction);
  });
}

function onRowAction(e){
  const action = e.currentTarget.dataset.action;
  const nro = e.currentTarget.dataset.nro;
  if(action==='pdf') abrirPdf(nro);
  if(action==='modi') openForm('modi', nro);
  if(action==='baja') confirmarBaja(nro);
}

function abrirPdf(nro){
  // carga en iframe desde traeDoc.php
  const frame = document.getElementById('pdfFrame');
  frame.src = traePdfUrl + '?NroFactura=' + encodeURIComponent(nro);
  openModal(modalPdf);
}

function confirmarBaja(nro){
  if(!confirm('¿Confirma eliminar '+nro+' ?')) return;
  // enviar POST a bajaUrl
  const fd = new FormData(); fd.append('NroFactura', nro);
  fetch(bajaUrl, {method:'POST', body:fd}).then(r=>r.text()).then(t=>{
    showResponse(t);
    cargarEjemplo();
  }).catch(e=>showResponse('Error: '+e.message));
}

// abrir formulario
async function openForm(mode, nro){
  // mode: 'alta' o 'modi'
  await cargarFamilias();
  document.getElementById('formABM').reset();
  document.getElementById('formNro').value = '';
  document.getElementById('formNro').value = '';
  document.getElementById('formCod').value = '';
  if(mode==='modi'){
    // buscar en dataset
    const row = dataRows.find(r=>r.codArt===nro);
    if(row){
      document.getElementById('modalTitle').textContent = 'Modificar '+nro;
      document.getElementById('formNro').value = row.codArt;
      document.getElementById('formCod').value = row.codArt;
      document.getElementById('formFamilia').value = row.familiaId || '';
      document.getElementById('formUm').value = row.um || '';
      document.getElementById('formDesc').value = row.descrip || '';
      document.getElementById('formFecha').value = row.fechaAlta || '';
      document.getElementById('formSaldo').value = row.saldo || '';
    }
  } else {
    document.getElementById('modalTitle').textContent = 'Alta registro';
  }
  openModal(modalForm);
}

// helpers
function escapeHtml(s){
  if(s==null) return '';
  return String(s).replace(/[&<>\"]/g, c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c]));
}

function cargarEjemplo(){
  // dataset de ejemplo (puedes reemplazar por fetch a tu endpoint list)
  dataRows = [
    {codArt:'ALM00', familia:'FRSEC', familiaId:1, um:'KG', descrip:'Almendras Peladas Orig Thaiti', fechaAlta:'2022-10-28', saldo:20011},
    {codArt:'ALM01', familia:'FRSEC', familiaId:1, um:'KG', descrip:'Almendra Non Pareil Origen Filipinas', fechaAlta:'2021-04-30', saldo:153},
    {codArt:'ALM02', familia:'FRSEC', familiaId:1, um:'KG', descrip:'Almendra partida', fechaAlta:'2023-06-05', saldo:2502},
    {codArt:'ATU00', familia:'CONPE', familiaId:2, um:'LT', descrip:'Atun. Orig Ecuador', fechaAlta:'2023-03-15', saldo:269},
    {codArt:'CHO01', familia:'CONVE', familiaId:3, um:'LT', descrip:'Choclo Orig. Ecuador', fechaAlta:'2020-02-02', saldo:87},
  ];
  renderTabla();
}

function vaciarDatos(){ dataRows = []; renderTabla(); }
function limpiarFiltros(){ document.getElementById('fCod').value=''; document.getElementById('fFamilia').value=''; document.getElementById('fUm').value=''; document.getElementById('fDesc').value=''; document.getElementById('fFecha').value=''; renderTabla(); }

// init
cargarFamilias(); cargarEjemplo();

// añadir filtros: re-render cuando cambian
['fCod','fFamilia','fUm','fDesc','fFecha','selectOrden'].forEach(id=>{
  const el = document.getElementById(id);
  if(!el) return;
  el.addEventListener('input', renderTabla);
  el.addEventListener('change', renderTabla);
});
