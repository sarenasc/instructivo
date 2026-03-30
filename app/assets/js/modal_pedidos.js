let pedidosAgregados = [];

function mostrarModalVersion(idInstructivo, version) {
   // Cargar cabecera
  $.getJSON('../models/obtener_cabecera_por_version.php', { id_instructivo: idInstructivo, version }, function (resp) {
    
      
      $('#readonlyVersion').val(version);
      $('#readonlyExportadora').val(resp.exportadora);
      $('#readonlyEspecie').val(resp.especie);
      $('#readonlyTurno').val(resp.turno);
      $('#readonlyObservacion').val(resp.observacion);
    
  });

 // Cargar nÃºmero de pedidos desde inst_detalle_instructivo
$.getJSON('../models/obtener_pedidos_existentes.php', { id_instructivo: idInstructivo, version }, function (resp) {
  if (resp.success && resp.data) {
    $('#selectNumeroPedido').empty().append('<option value="">Seleccione...</option>');

    const pedidosUnicos = new Set();

    resp.data.forEach(pedido => {
      pedidosUnicos.add(pedido.numero);
    });

    pedidosUnicos.forEach(numero => {
      $('#selectNumeroPedido').append(
        $('<option>', {
          value: numero,
          text: numero
        })
      );
    });
  }
});


  // Cargar pedidos existentes
/*$.getJSON('../models/obtener_pedidos_posibles.php', { id_instructivo: idInstructivo, version }, function (resp) {
  if (resp.success && resp.data.length > 0) {
    pedidosAgregados = [...resp.data]; // Guardar en variable global
    resp.data.forEach(pedido => {
      $('#tablaPedidos tbody').append(`
        <tr>
          <td>${pedido.numero_pedido}</td>
          <td><button class="btn btn-sm btn-danger btnEliminarPedido">Eliminar</button></td>
        </tr>
      `);
    });
  }
});*/


// Evento: Agregar pedido a la tabla
$('#btnAgregarPedido').on('click', function () {
  const numero = $('#selectNumeroPedido').val();
  const cantidad = parseInt($('#inputCantidad').val());
  const prioridad = parseInt($('#inputPrioridad').val());

  if (!numero || !cantidad || cantidad <= 0 || !prioridad || prioridad <= 0) return alert("Complete los campos correctamente");

  pedidosAgregados.push({ numero, cantidad, prioridad });

  $('#tablaPedidos tbody').append(`
    <tr>
      <td>${numero}</td>
      <td>${cantidad}</td>
      <td>${prioridad}</td>
      <td><button class="btn btn-sm btn-danger btnEliminarPedido">Eliminar</button></td>
    </tr>
  `);

  $('#inputCantidad').val('');
});

// Evento: Eliminar pedido
$(document).on('click', '.btnEliminarPedido', function () {
  const row = $(this).closest('tr');
  const numero = row.find('td:first').text();
  pedidosAgregados = pedidosAgregados.filter(p => p.numero !== numero);
  row.remove();
});

// Guardar en la base
$('#btnGuardarPedidos').on('click', function () {
  const idInstructivo = $('#selectInstructivo').val();
  const version = $('#selectVersion').val();

  if (pedidosAgregados.length === 0) return alert("No hay pedidos para guardar.");

  $.post('../controllers/guardar_pedidos.php', {
    id_instructivo: idInstructivo,
    version,
    pedidos: JSON.stringify(pedidosAgregados)
  }, function (resp) {
    alert("Pedidos guardados correctamente");
    $('#modalEditarVersion').modal('hide');
  });
});

  $('#modalEditarVersion').modal('show');
}



