document.addEventListener("DOMContentLoaded", () => {
  const exportadoraSelect = document.getElementById("exportadora");
  const instructivoSelect = document.getElementById("instructivo");
  const versionSelect = document.getElementById("version");
  const modal = new bootstrap.Modal(document.getElementById("modalDetalle"));
  const detalleDiv = document.getElementById("detalleInstructivo");
  const fechaInput = document.getElementById("nueva_fecha");

  let instructivosGlobal = [];

  // 1. Cargar exportadoras
  fetch("../services/api_exportadora.php")
    .then(res => res.json())
    .then(data => {
      data.forEach(exp => {
        exportadoraSelect.innerHTML += `<option value="${exp.id}">${exp.Nombre_Exportadora}</option>`;
      });
    });

  // 2. Al seleccionar exportadora, cargar instructivos
  exportadoraSelect.addEventListener("change", () => {
    instructivoSelect.disabled = false;
    instructivoSelect.innerHTML = '<option value="">Seleccione un instructivo</option>';
    versionSelect.innerHTML = '<option value="">Seleccione una versiÃ³n</option>';
    versionSelect.disabled = true;

    fetch("../models/obtener_instructivo.php")
      .then(res => res.json())
      .then(json => {
        if (json.success) {
          instructivosGlobal = json.data;
          const filtrados = instructivosGlobal.filter(i => i.id === parseInt(exportadoraSelect.value));
          filtrados.forEach(ins => {

            const fecha = new Date(ins.fecha.date);
          const dia = fecha.getDate().toString().padStart(2, '0');
          const mes = (fecha.getMonth() + 1).toString().padStart(2, '0');
          const anio = fecha.getFullYear();
          const fechaFormateada = `${dia}/${mes}/${anio}`;

            instructivoSelect.innerHTML += `<option value="${ins.id_instructivo}">${ins.id_instructivo} - ${fechaFormateada} - ${ins.especie}</option>`;
          });
        }
      });
  });

  // 3. Al seleccionar instructivo, cargar versiones
  instructivoSelect.addEventListener("change", () => {
    versionSelect.disabled = false;
    versionSelect.innerHTML = '<option value="">Seleccione una versiÃ³n</option>';

    fetch(`../models/obtener_version.php?id_instructivo=${instructivoSelect.value}`)
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      // Extraer solo los nÃºmeros de versiÃ³n
      const versiones = data.data.map(item => item.version);

      // Usar Set para eliminar duplicados y convertirlo de nuevo en array
      const versionesUnicas = [...new Set(versiones)];

      // Limpiar y llenar el select de versiones
      const selectVersion = document.getElementById('version');
      selectVersion.innerHTML = '<option value="">Seleccione versiÃ³n</option>';

      versionesUnicas.forEach(version => {
        const option = document.createElement('option');
        option.value = version;
        option.textContent = `VersiÃ³n ${version}`;
        selectVersion.appendChild(option);
      });
    } else {
      alert("No se pudieron obtener las versiones.");
    }
  })
  .catch(error => {
    console.error("Error al obtener versiones:", error);
  });

  });

  // 4. Al seleccionar versiÃ³n, mostrar modal con detalle
  versionSelect.addEventListener("change", () => {
  fetch(`../models/obtener_detalle_por_version.php?id_instructivo=${instructivoSelect.value}&version=${versionSelect.value}`)
    .then(res => res.json())
    .then(json => {
      if (json.success) {
        const agrupado = {};

        json.data.forEach(d => {
          const key = `${d.numero_pedido}|${d.embalaje}|${d.plu}|${d.Nombre_etiqueta}|${d.categoria}|${d.turno}`;
          if (!agrupado[key]) {
            agrupado[key] = {
              ...d,
              calibres: []
            };
          }
          agrupado[key].calibres.push(d.calibre);
        });

        detalleDiv.innerHTML = Object.values(agrupado).map(d => `
          <div class="mb-2 p-2 border rounded bg-white">
            <strong>Pedido:</strong> ${d.numero_pedido} =>
            <strong>Embalaje:</strong> ${d.embalaje} =>
            <strong>Calibres:</strong> ${d.calibres.join(", ")} =>
            <strong>PLU:</strong> ${d.plu} =>
            <strong>Etiqueta:</strong> ${d.Nombre_etiqueta} =>
            <strong>Categoria:</strong> ${d.categoria} <==>
            <strong> TURNO:</strong> ${d.turno}
          </div>
        `).join("");

        modal.show();
      } else {
        alert("Error al obtener detalle del instructivo.");
      }
    });
});


  // 5. Enviar copia del instructivo
  document.getElementById("formDetalle").addEventListener("submit", e => {
    e.preventDefault();
    const nuevaFecha = fechaInput.value;

    if (!nuevaFecha) {
      alert("Por favor, ingresa una nueva fecha.");
      return; 
    }

    const datos = {
  id_instructivo: document.getElementById('instructivo').value,
  version: document.getElementById('version').value,
  fecha: document.getElementById('nueva_fecha').value, 
  turno:document.getElementById('turno').value
};

fetch('../copiar_instructivo.php', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json'
  },
  body: JSON.stringify(datos)
})
.then(response => response.json())
.then(data => {
  if (data.success) {
    alert("Instructivo copiado exitosamente. Nuevo ID: " + data.nuevo_id);
    
Â Â Â  // Cerrar el modal (si usas Bootstrap 5)
Â Â Â  const modalElement = document.getElementById('modalDetalle');
Â Â Â  const modalInstance = bootstrap.Modal.getInstance(modalElement);
Â Â Â  modalInstance.hide();

  } else {
    alert("Error: " + data.message);
    console.error(data.detalle || '');
  }
})
.catch(error => {
  console.error("Error al copiar instructivo:", error);
});

  });

});

