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
      if (!data || data.length === 0) {
        console.error("No se encontraron exportadoras");
        return;
      }
      data.forEach(exp => {
        exportadoraSelect.innerHTML += `<option value="${exp.id}">${exp.Nombre_Exportadora}</option>`;
      });
    })
    .catch(err => {
      console.error("Error al cargar exportadoras:", err);
    });

  // 2. Al seleccionar exportadora, cargar instructivos
  exportadoraSelect.addEventListener("change", () => {
    const idExportadora = exportadoraSelect.value;
    
    if (!idExportadora) {
      instructivoSelect.disabled = true;
      instructivoSelect.innerHTML = '<option value="">Seleccione una exportadora primero</option>';
      return;
    }

    instructivoSelect.disabled = false;
    instructivoSelect.innerHTML = '<option value="">Cargando instructivos...</option>';
    versionSelect.innerHTML = '<option value="">Seleccione una versión</option>';
    versionSelect.disabled = true;

    // Cargar instructivos filtrados por exportadora
    fetch(`../models/obtener_instructivo.php?id_exportadora=${idExportadora}`)
      .then(res => res.json())
      .then(json => {
        if (json.success && json.data && json.data.length > 0) {
          instructivosGlobal = json.data;
          instructivoSelect.innerHTML = '<option value="">Seleccione un instructivo</option>';
          
          json.data.forEach(ins => {
            const fechaMostrar = ins.fecha_formateada || 'Sin fecha';
            const especie = ins.especie || '';
            instructivoSelect.innerHTML += `<option value="${ins.id_instructivo}">${ins.id_instructivo} - ${fechaMostrar} - ${especie}</option>`;
          });
        } else {
          instructivoSelect.innerHTML = '<option value="">No hay instructivos para esta exportadora</option>';
          console.warn("No se encontraron instructivos:", json);
        }
      })
      .catch(err => {
        console.error("Error al cargar instructivos:", err);
        instructivoSelect.innerHTML = '<option value="">Error al cargar</option>';
      });
  });

  // 3. Al seleccionar instructivo, cargar versiones
  instructivoSelect.addEventListener("change", () => {
    const idInstructivo = instructivoSelect.value;
    
    if (!idInstructivo) {
      versionSelect.disabled = true;
      versionSelect.innerHTML = '<option value="">Seleccione un instructivo primero</option>';
      return;
    }

    versionSelect.disabled = false;
    versionSelect.innerHTML = '<option value="">Cargando versiones...</option>';

    fetch(`../models/obtener_version.php?id_instructivo=${idInstructivo}`)
      .then(response => response.json())
      .then(data => {
        if (data.success && data.data && data.data.length > 0) {
          // Extraer solo los números de versión
          const versiones = data.data.map(item => item.version);

          // Usar Set para eliminar duplicados y convertirlo de nuevo en array
          const versionesUnicas = [...new Set(versiones)];

          // Limpiar y llenar el select de versiones
          versionSelect.innerHTML = '<option value="">Seleccione versión</option>';

          versionesUnicas.forEach(version => {
            const option = document.createElement('option');
            option.value = version;
            option.textContent = `Versión ${version}`;
            versionSelect.appendChild(option);
          });
        } else {
          versionSelect.innerHTML = '<option value="">No se encontraron versiones</option>';
          console.warn("No se encontraron versiones:", data);
        }
      })
      .catch(error => {
        console.error("Error al obtener versiones:", error);
        versionSelect.innerHTML = '<option value="">Error al cargar versiones</option>';
      });
  });

  // 4. Al seleccionar versión, mostrar modal con detalle
  versionSelect.addEventListener("change", () => {
    if (!versionSelect.value) {
      return;
    }

    fetch(`../models/obtener_detalle_por_version.php?id_instructivo=${instructivoSelect.value}&version=${versionSelect.value}`)
      .then(res => res.json())
      .then(json => {
        if (json.success && json.data && json.data.length > 0) {
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
              <strong>Categoria:</strong> ${d.categoria} ==>
              <strong>Turno:</strong> ${d.turno}
            </div>
          `).join("");

          modal.show();
        } else {
          alert("No se encontró el detalle del instructivo.");
          console.warn("Detalle no encontrado:", json);
        }
      })
      .catch(err => {
        console.error("Error al obtener detalle:", err);
        alert("Error al cargar el detalle del instructivo.");
      });
  });

  // 5. Enviar copia del instructivo
  document.getElementById("formDetalle").addEventListener("submit", e => {
    e.preventDefault();
    
    const idInstructivo = document.getElementById('instructivo').value;
    const version = document.getElementById('version').value;
    const nuevaFecha = document.getElementById('nueva_fecha').value;
    const turno = document.getElementById('turno').value;

    if (!idInstructivo || !version || !nuevaFecha) {
      alert("Por favor, complete todos los campos requeridos.");
      return;
    }

    const datos = {
      id_instructivo: parseInt(idInstructivo),
      version: parseInt(version),
      fecha: nuevaFecha,
      turno: turno
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
        
        // Cerrar el modal
        const modalElement = document.getElementById('modalDetalle');
        const modalInstance = bootstrap.Modal.getInstance(modalElement);
        if (modalInstance) {
          modalInstance.hide();
        }
        
        // Recargar página o limpiar formulario
        setTimeout(() => {
          location.reload();
        }, 1000);
      } else {
        alert("Error: " + data.message);
        console.error(data.detalle || '');
      }
    })
    .catch(error => {
      console.error("Error al copiar instructivo:", error);
      alert("Error de conexión al copiar el instructivo.");
    });
  });
});
