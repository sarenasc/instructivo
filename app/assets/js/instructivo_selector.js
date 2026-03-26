$(document).ready(function () {
  // Cargar instructivos al iniciar
  $.getJSON('../obtener_instructivo.php', function (response) {   
    if (response.success) {
      $('#selectInstructivo').empty().append('<option value="">Seleccione un instructivo</option>');
      response.data.forEach(function (item) {
        const fechaFormateada = item.fecha_formateada || 'Sin fecha';
        
        $('#selectInstructivo').append(
          $('<option>', {
            value: item.id_instructivo,
            text: `ID  ${item.id_instructivo} - ${fechaFormateada}  - ${item.Nombre_Exportadora}  - ${item.especie}`
          })
        );
      });
    } else {
      console.error('Error al obtener instructivos:', response.message);
    }
  });

  // Cuando se selecciona un instructivo, cargar versiones correspondientes
  $('#selectInstructivo').on('change', function () {
    var idInstructivo = $(this).val();
   
    $('#selectVersion').empty().append('<option value="">Seleccione una versión</option>');

    if (idInstructivo) {
      $.getJSON('../obtener_version.php', { id_instructivo: idInstructivo }, function (response) {
        if (response.success) {
         // Usamos un Set para evitar versiones duplicadas
const versionesUnicas = new Set();

response.data.forEach(function (item) {
  versionesUnicas.add(item.version);
});

versionesUnicas.forEach(function (version) {
  $('#selectVersion').append(
    $('<option>', {
      value: version,
      text: 'Versión ' + version
    })
  );
});

        } else {
          console.error('Error al obtener versiones:', response.message);
        }
      });
    }
  });

  // Cuando se elige una versión, cargar cabecera y detalle en modal (si tienes esa lógica)
  $('#selectVersion').on('change', function () {
  const idInstructivo = $('#selectInstructivo').val();
  const version = $(this).val();
 
  if (idInstructivo && version) {
    mostrarModalVersion(idInstructivo, version);
  }
});
});

