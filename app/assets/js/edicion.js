document.addEventListener('DOMContentLoaded', () => {
  // Cambio de tabla en el select
  document.getElementById('tablaConfig').addEventListener('change', function () {
    const tabla = this.value;
    if (tabla) {
      cargarTabla(tabla);
    } else {
      document.getElementById('tablaResultados').innerHTML = '';
    }
  });

  // Envío del formulario del modal
  const form = document.getElementById('formEdicion');
  form.addEventListener('submit', function (e) {
    e.preventDefault();

    const tabla = this.dataset.tabla;
    const inputs = this.querySelectorAll('input[name]');
    const datos = {};

    inputs.forEach(input => {
      datos[input.name] = input.value.trim();
    });

    fetch(`../modificar_${tabla}.php`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(datos)
    })
      .then(res => res.json())
      .then(resp => {
        if (resp.success) {
           alert('Modificado correctamente');
            bootstrap.Modal.getInstance(document.getElementById('modalEdicion')).hide();
          actualizarFilaEnDOM(resp.datos); 
        } else {
          alert('Error al modificar.');
        }
      })
      .catch(err => {
        console.error(err);
        alert('Error de red');
      });
  });
});

function cargarTabla(tabla) {
  fetch(`../api_${tabla}.php`)
    .then(res => res.json())
    .then(data => {
      if (Array.isArray(data)) {
        renderTabla(data, tabla);
      } else {
        document.getElementById('tablaResultados').innerHTML =
          '<div class="alert alert-warning">No se pudo cargar la tabla. Verifica la API.</div>';
        console.error("Estructura inesperada en respuesta:", data);
      }
    })
    .catch(error => {
      console.error("Error al conectar con la API:", error);
      document.getElementById('tablaResultados').innerHTML =
        '<div class="alert alert-danger">Error al conectar con la API.</div>';
    });
}

function renderTabla(datos, tabla) {
  if (datos.length === 0) {
    document.getElementById('tablaResultados').innerHTML = '<p>No hay registros.</p>';
    return;
  }

  const columnas = Object.keys(datos[0]);
  let html = '<table class="table table-striped">';
  html += '<thead><tr>';
  columnas.forEach(col => {
    html += `<th>${col}</th>`;
  });
  html += '<th>Acciones</th></tr></thead><tbody>';

  datos.forEach(registro => {
    html += '<tr>';
    columnas.forEach(col => {
      html += `<td>${registro[col]}</td>`;
    });
    html += `<td>
              <button class="btn btn-sm btn-success" onclick="modificarRegistro('${tabla}', ${registro.id}, this)">Modificar</button>
              <button class="btn btn-sm btn-danger" onclick="eliminarRegistro('${tabla}', ${registro.id})">Eliminar</button>
            </td>`;
    html += '</tr>';
  });

  html += '</tbody></table>';
  document.getElementById('tablaResultados').innerHTML = html;
}

function modificarRegistro(tabla, id, boton) {
  const fila = boton.closest('tr');
  const celdas = fila.querySelectorAll('td');
  const headers = Array.from(document.querySelectorAll('#tablaResultados th')).map(th => th.textContent);
  const datos = {};

  headers.forEach((header, i) => {
    if (header === 'Acciones') return;
    datos[header] = celdas[i].textContent.trim();
  });

  mostrarModalEdicion(tabla, datos);
}

function mostrarModalEdicion(tabla, datos) {
  const modal = document.getElementById('modalEdicion');
  const form = modal.querySelector('form');
  const container = modal.querySelector('.modal-body');

  container.innerHTML = '';
  for (const campo in datos) {
    container.innerHTML += `
      <div class="mb-3">
        <label class="form-label">${campo}</label>
        <input type="text" class="form-control" name="${campo}" value="${datos[campo]}" ${campo === 'id' ? 'readonly' : ''}>
      </div>
    `;
  }

  form.dataset.tabla = tabla;

  const modalInstance = new bootstrap.Modal(modal);
  modalInstance.show();
}

function eliminarRegistro(tabla, id) {
  if (!confirm('¿Seguro que deseas eliminar este registro?')) return;

  fetch(`eliminar_${tabla}.php`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ id })
  })
    .then(res => res.json())
    .then(resp => {
      if (resp.success) {
        alert('Registro eliminado correctamente.');
        cargarTabla(tabla);
      } else {
        alert('Error al eliminar.');
      }
    })
    .catch(err => {
      console.error(err);
      alert('Error de red al eliminar.');
    });
}

function actualizarFilaEnDOM(datosActualizados) {
  const filas = document.querySelectorAll('#tablaResultados tbody tr');
  filas.forEach(fila => {
    const celdaId = fila.querySelector('td'); // asumimos que la primera columna es ID
    if (celdaId && celdaId.textContent.trim() == datosActualizados.id) {
      const celdas = fila.querySelectorAll('td');
      const columnas = Object.keys(datosActualizados);
      
      columnas.forEach((col, i) => {
        if (celdas[i]) celdas[i].textContent = datosActualizados[col];
      });

      // Agrega la clase para resaltar
      fila.classList.add('table-success');

      // Quitar la clase luego de 1.5 segundos
      setTimeout(() => {
        fila.classList.remove('table-success');
      }, 1500);
    }
  });
}

