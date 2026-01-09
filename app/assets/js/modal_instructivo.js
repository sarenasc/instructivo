let listasSelect = {};

// Detectar campos clave
const detectarCampos = (lista) => {
  if (!lista || lista.length === 0) return ['id', 'nombre'];
  const keys = Object.keys(lista[0]);
  return [
    keys[0],
    keys.find(k => /nombre|desc/i.test(k)) || keys[1] || keys[0]
  ];
};

// Generar opciones de <select>
const generarOpciones = (lista, seleccionado = '') => {
  if (!Array.isArray(lista)) return '<option value="">(Sin datos)</option>';
  const [campoId, campoNombre] = detectarCampos(lista);

  return lista.map(op => {
    let texto = op[campoNombre];
    let title = texto;

    if (listasSelect.alturas.includes(op)) {
      texto = `${op.altura} - ${op.caja}`;
      title = texto;
    }

    if (listasSelect.embalajes.includes(op)) {
      texto = op.Codigo_emb || texto;
      title = op.nombre || texto;
    }

    const isSelected = Array.isArray(seleccionado)
      ? seleccionado.includes(op[campoId])
      : op[campoId] == seleccionado;

    return `
      <option value="${op[campoId]}" title="${title}" ${isSelected ? 'selected' : ''}>
        ${texto}
      </option>
    `;
  }).join('');
};

// Agrupar por campos comunes excepto calibre
function agruparPorCamposComunes(data) {
  const grupos = {};

  data.forEach(item => {
    const clave = JSON.stringify({
      numero_pedido: item.numero_pedido,
      id_embalaje: item.id_embalaje,
      id_etiqueta: item.id_etiqueta,
      cantidad_pedido: item.cantidad_pedido,
      id_categoria: item.id_categoria,
      id_plu: item.id_plu,
      id_destino: item.id_destino,
      altura_pallet: item.altura_pallet,
      id_pallet: item.id_pallet,
      var_etiquetada: item.var_etiquetada,
      observacion: item.observacion
    });

    if (!grupos[clave]) {
      grupos[clave] = {
        ...item,
        id_calibre: [item.id_calibre]
      };
    } else {
      grupos[clave].id_calibre.push(item.id_calibre);
    }
  });

  return Object.values(grupos);
}

// Mostrar modal
function mostrarModalVersion(idInstructivo, version) {
  $.getJSON('../obtener_cabecera_por_version.php', { id_instructivo: idInstructivo, version: version }, function (data) {
    $('#readonlyExportadora').val(data.exportadora);
    $('#readonlyEspecie').val(data.especie);
    $('#readonlyTurno').val(data.turno);
    $('#readonlyObservacion').val(data.observacion);
    $('#readonlyVersion').val(data.version);

    const idExportadora = data.exportadora_id;
    const idEspecie = data.especie_id;

    $.getJSON('../obtener_listas_detalle.php', {
      id_exportadora: idExportadora,
      id_especie: idEspecie
    }, function (listas) {
      if (listas.success && listas.data) {
        listasSelect = {
          embalajes: listas.data.embalajes || [],
          categorias: listas.data.categorias || [],
          etiqueta: listas.data.etiqueta || [],
          pallets: listas.data.pallets || [],
          calibres: listas.data.calibres || [],
          plus: listas.data.plus || [],
          destinos: listas.data.destinos || [],
          alturas: listas.data.altura || [],
        };
      } else {
        console.error("Error cargando listas de selects filtradas:", listas);
      }

      $('#tablaDetalleEditable tbody').empty();
      $.getJSON('../obtener_detalle_por_version.php', {
        id_instructivo: idInstructivo,
        version: version
      }, function (response) {
        if (response.success && Array.isArray(response.data)) {
          const filasAgrupadas = agruparPorCamposComunes(response.data);
          filasAgrupadas.forEach(item => {
            agregarFilaDetalle(item);
          });
        } else {
          alert("No se pudo cargar el detalle del instructivo.");
          console.error("Detalle no es un array:", response);
        }
      });
    });
  });

  const modal = new bootstrap.Modal(document.getElementById('modalEditarVersion'));
  modal.show();
}

// Agregar fila
function agregarFilaDetalle(data = {}) {
  const alturasFiltradas = listasSelect.alturas.filter(a => a.id_embalaje == data.id_embalaje);

  const fila = `
    <tr>
      <td><input type="text" class="form-control numero_pedido" value="${data.numero_pedido || ''}"/></td>
      <td><select class="form-control embalaje">${generarOpciones(listasSelect.embalajes, data.id_embalaje)}</select></td>
      <td><select class="form-control etiqueta">${generarOpciones(listasSelect.etiqueta, data.id_etiqueta)}</select></td>
      <td><select class="form-control calibre" multiple>${generarOpciones(listasSelect.calibres, data.id_calibre)}</select></td>
      <td><input type="number" class="form-control cantidad" value="${data.cantidad_pedido || ''}"/></td>
      <td><select class="form-control categoria">${generarOpciones(listasSelect.categorias, data.id_categoria)}</select></td>
      <td><select class="form-control plu">${generarOpciones(listasSelect.plus, data.id_plu)}</select></td>
      <td><select class="form-control destino">${generarOpciones(listasSelect.destinos, data.id_destino)}</select></td>
      <td><select class="form-control altura">${generarOpciones(alturasFiltradas, data.altura_pallet)}</select></td>
      <td><select class="form-control pallet">${generarOpciones(listasSelect.pallets, data.id_pallet)}</select></td>
      <td><input type="text" class="form-control var_etiquetada" value="${data.var_etiquetada || ''}"/></td>
      <td><input type="text" class="form-control observacion" value="${data.observacion || ''}"/></td>
      <td><button class="btn btn-danger btn-sm eliminar-fila">X</button></td>
    </tr>
  `;
  $('#tablaDetalleEditable tbody').append(fila);
}

// Botón agregar fila
$('#btnAgregarFila').on('click', function () {
  agregarFilaDetalle();
});

// Eliminar fila
$(document).on('click', '.eliminar-fila', function () {
  $(this).closest('tr').remove();
});

// Cambio de embalaje para actualizar alturas
$(document).on('change', '.embalaje', function () {
  const $fila = $(this).closest('tr');
  const idEmbalaje = $(this).val();
  const alturasRelacionadas = listasSelect.alturas.filter(a => a.id_embalaje == idEmbalaje);

  const opciones = alturasRelacionadas.length > 0
    ? alturasRelacionadas.map(a => `<option value="${a.id}">${a.altura} - ${a.caja}</option>`).join('')
    : '<option value="">(Sin altura)</option>';

  $fila.find('.altura').html(opciones);
});

// Guardar versión
$('#btnGuardarNuevaVersion').on('click', function () {
  const id = $('#selectInstructivo').val();
  const detalle = [];

  $('#tablaDetalleEditable tbody tr').each(function () {
    const $fila = $(this);
    const id_calibres = $fila.find('.calibre').val(); // Array de calibres seleccionados

    if (Array.isArray(id_calibres)) {
      id_calibres.forEach(id_calibre => {
        detalle.push({
          numero_pedido: $fila.find('.numero_pedido').val(),
          id_embalaje: $fila.find('.embalaje').val(),
          id_etiqueta: $fila.find('.etiqueta').val(),
          id_calibre: id_calibre,
          cantidad_pedido: $fila.find('.cantidad').val(),
          id_categoria: $fila.find('.categoria').val(),
          id_plu: $fila.find('.plu').val(),
          id_destino: $fila.find('.destino').val(),
          altura_pallet: $fila.find('.altura').val(),
          id_pallet: $fila.find('.pallet').val(),
          var_etiquetada: $fila.find('.var_etiquetada').val(),
          observacion: $fila.find('.observacion').val(),
        });
      });
    }
  });

  $.ajax({
    url: '../procesar_nueva_version.php',
    method: 'POST',
    contentType: 'application/json',
    data: JSON.stringify({ id_instructivo: id, detalle: detalle }),
    success: function () {
      alert('Nueva versión guardada con éxito');
      bootstrap.Modal.getInstance(document.getElementById('modalEditarVersion')).hide();
    },
    error: function () {
      alert('Error al guardar la nueva versión');
    }
  });
});

