let instructivos = [];

document.addEventListener("DOMContentLoaded", () => {
  // Cargar instructivos con exportadora y fecha
  fetch('../obtener_instructivos.php')
    .then(res => res.json())
    .then(data => {
      instructivos = data;
      const select = document.getElementById('selectInstructivo');
      data.forEach(item => {
        const option = document.createElement('option');
        option.value = item.id_instructivo;
        option.text = `${item.id_instructivo} - ${item.nombre_exportadora} - ${item.fecha} - ${item.especie}`;
        select.appendChild(option);
      });
      cargarVersiones(); // carga inicial
    });

  document.getElementById('selectInstructivo').addEventListener('change', cargarVersiones);
});

function cargarVersiones() {
  const id = document.getElementById('selectInstructivo').value;
  fetch(`../obtener_versiones.php?id_instructivo=${id}`)
    .then(res => res.json())
    .then(data => {
      const select = document.getElementById('selectVersion');
      select.innerHTML = '';
      data.forEach(v => {
        const option = document.createElement('option');
        option.value = v.version;
        option.text = `Versión ${v.version}`;
        select.appendChild(option);
      });
    });
}

function descargarExcel() {
  const id = document.getElementById('selectInstructivo').value;
  const version = document.getElementById('selectVersion').value;
  if (id && version) {
    window.location.href = `../exportar_excel_instructivo.php?id_instructivo=${id}&version=${version}`;
  } else {
    alert("Debe seleccionar un instructivo y versión.");
  }
}
