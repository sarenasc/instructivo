document.addEventListener('DOMContentLoaded', () => {
  const expSel = document.getElementById('exportadoraSelect');
  const insSel = document.getElementById('instructivoSelect');
  const verSel = document.getElementById('versionSelect');
    cargarTodosLosInstructivos();
  let instructivosGlobal = [];

function cargarTodosLosInstructivos() {
  fetch('../obtener_instructivo.php')
    .then(r => r.json())
    .then(data => {
      instructivosGlobal = data.data;

      //console.log("Todos los instructivos cargados:", instructivosGlobal);
    });
}


  fetch('../api_exportadora.php')
    .then(r => r.json())
    .then(lista => {
      expSel.innerHTML = lista.map(e => `<option value="${e.id}">${e.Nombre_Exportadora}</option>`).join('');
      return lista[0]?.id;
    })
    .then(id_exp => loadInstructivos(id_exp));

  expSel.addEventListener('change', () => loadInstructivos(expSel.value));
  insSel.addEventListener('change', () => loadVersiones(insSel.value));
  verSel.addEventListener('change', () => mostrarDetalle(insSel.value, verSel.value));

function loadInstructivos(id_exportadora) {
  const filtrados = instructivosGlobal.filter(i => i.id == id_exportadora);
  
  insSel.innerHTML = filtrados.map(i =>
    `<option value="${i.id_instructivo}">${i.id_instructivo}</option>`
  ).join('');

  if (filtrados.length) {
    loadVersiones(filtrados[0].id_instructivo);
  } else {
    console.log("No hay instructivos para esta exportadora.");
  }
}


function loadVersiones(id_instructivo) {
  console.log("Cargando versiones para instructivo: ", id_instructivo);
  fetch(`../obtener_versiones.php?id_instructivo=${id_instructivo}`)
    .then(r => r.json())
    .then(lista => {
      console.log("Lista de versiones:", lista);
      if (!Array.isArray(lista)) {
        console.error("La respuesta no es un array:", lista);
        return;
      }

      versionSelect.innerHTML = lista.map(v => 
        `<option value="${v.version}">Versión ${v.version}</option>`).join('');
    })
    .catch(err => console.error("Error al cargar versiones:", err));
}


  

const id_instructivo = instructivoSelect.value;
const version = versionSelect.value;

console.log("Parámetros enviados:", id_instructivo, version);

fetch(`../obtener_detalle_por_version.php?id_instructivo=${id_instructivo}&version=${version}`)
  .then(res => res.json())
  .then(json => {
    console.log("Respuesta JSON del detalle:", json);

    if (!json || json.length === 0) {
      console.warn("No hay detalle para mostrar.");
      return;
    }

    renderTablaAgrupada(json);  // Asegúrate de que esta función esté definida
  })
  .catch(err => console.error("Error al cargar detalle:", err));



  // renderTablaAgrupada como antes...

  
});
