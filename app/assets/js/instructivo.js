cargarEspecies();
cargarExportadora()

function cargarCombo(tabla, selectId, idExportadora, especie) {

  

  const tablasConAmbos = ["embalaje", "categoria"];
  const tablasSoloExportadora = ["etiqueta", "pallet"];
  const SoloEspecie = ["plu", "calibre"];

  let url = `../services/api_instructivo_combobox.php?tabla=${tabla}`;


    if (SoloEspecie.includes(tabla)) {
    
    if (especie) url += `&especie=${especie}`;
    
  } 
  else if (tablasConAmbos.includes(tabla)) {
    if (idExportadora) url += `&id_exportadora=${idExportadora}`;
    if (especie) url += `&especie=${especie}`;
    
  }
 
  else if (tablasSoloExportadora.includes(tabla)) {
    if (idExportadora) url += `&id_exportadora=${idExportadora}`;
   
  } 
 

     fetch(url)
    .then(response => response.json())
    .then(data => {
      const select = document.getElementById(selectId);
      select.innerHTML = '<option value="">Seleccione...</option>';
      data.forEach(item => {
        const option = document.createElement("option");
        option.value = item.id;
        option.textContent = item.text;
        select.appendChild(option);
      });
    });
  }



// Llenar combos al cargar la pÃ¡gina
document.addEventListener("DOMContentLoaded", function () {

  const selectExportadora = document.getElementById("id_exportadora");
  const selectEspecie = document.getElementById("especie");
    
  let idExportadora = "";
  let especie = "";
  let InfoCargada = false;
 

  function CargaCombo(){

        if(idExportadora && especie && !InfoCargada){
          InfoCargada = true;
        //cargarCombo('exportadora', 'id_exportadora');
        //cargarCombo('especie', 'id_especie');
        cargarCombo('embalaje', 'id_embalaje', idExportadora, especie);
        cargarCombo('etiqueta', 'id_etiqueta',idExportadora);
        cargarCombo('calibre', 'id_calibre',"", especie);
        cargarCombo('categoria', 'id_categoria',idExportadora, especie);
        cargarCombo('plu', 'id_plu',"",especie);
        cargarCombo('destino', 'id_destino');
        cargarCombo('pallet', 'id_pallet',idExportadora);
       }
  }

  selectExportadora.addEventListener("change" , function() {

    idExportadora = this.value;
    InfoCargada = false;
    CargaCombo();
  });

  selectEspecie.addEventListener("change", function() {
    especie = this.value;
    InfoCargada = false;
    CargaCombo();
  });
});


function cargarEspecies() {
    fetch("../services/api_especies.php")
        .then(response => response.json())
        .then(data => {
            let select = document.getElementById("especie");
            select.innerHTML = '<option value="">Seleccione una especie</option>';
            data.forEach(especie => {
                let option = document.createElement("option");
                option.value = especie.id_especie;
                option.textContent = especie.especie;
                select.appendChild(option);
            });
        })
        .catch(error => console.error("Error cargando especies:", error));
}

function cargarExportadora() {
    fetch("../services/api_exportadora.php")
        .then(response => response.json())
        .then(data => {
            let select = document.getElementById("id_exportadora");
            select.innerHTML = '<option value="">Seleccione una exportadora</option>';
            data.forEach(exportadora => {
                let option = document.createElement("option");
                option.value = exportadora.id;
                option.textContent = exportadora.Nombre_Exportadora;
                select.appendChild(option);
            });
        })
        .catch(error => console.error("Error cargando exportadora:", error));
}



// Manejo del envÃ­o del formulario principal
const form = document.getElementById("formCabecera");
if (form) {
  form.addEventListener("submit", function (e) {
    e.preventDefault();
    const formData = new FormData(form);

    fetch("../controllers/procesar_instructivo.php", {
      method: "POST",
      body: formData
    })
    .then(response => response.json())
    .then(data => {
      const mensajeDiv = document.getElementById("mensaje");
//mensaje verde con letras blancas
if (data.success) {
  mensajeDiv.className = "alert alert-success text-white bg-success";
  mensajeDiv.textContent = "Instructivo guardado con Ã©xito. ID: " + data.id_instructivo;
  mensajeDiv.classList.remove("d-none");

  // Opcional: ocultar mensaje despuÃ©s de unos segundos
  setTimeout(() => {
    mensajeDiv.classList.add("d-none");
  }, 5000);

  mostrarFormularioDetalle(data.id_instructivo, data.version);
} else {
  mensajeDiv.className = "alert alert-danger text-white bg-danger";
  mensajeDiv.textContent = "Error al guardar instructivo: " + data.message;
  mensajeDiv.classList.remove("d-none");

  setTimeout(() => {
    mensajeDiv.classList.add("d-none");
  }, 5000);
}

      
    })
    .catch(error => {
      console.error("Error en la solicitud:", error);
    });
  });
}
function mostrarFormularioDetalle(idInstructivo, version) {
  document.getElementById("detalleCard").classList.remove("d-none");

  document.getElementById("id_cab_instructivo_detalle").value = idInstructivo;
  document.getElementById("version_detalle").value = version;

  
}


// altura pallet 

document.addEventListener('DOMContentLoaded', () => {
  const embalajeSelect = document.getElementById('id_embalaje');
  const alturaSelect = document.getElementById('selectAlturaPallet');
 

  embalajeSelect.addEventListener('change', () => {
    const embalajeId = embalajeSelect.value;

    alturaSelect.innerHTML = '<option value="">Cargando...</option>';
   

    if (!embalajeId) {
      alturaSelect.innerHTML = '<option value="">Seleccione Altura y Cajas</option>';
      return;
    }

    fetch(`../services/api_alturas_pallet.php?id_embalaje=${embalajeId}`)
      .then(res => res.json())
      .then(data => {
        alturaSelect.innerHTML = '<option value="">Seleccione Altura y Cajas</option>';

        data.forEach(item => {
          const option = document.createElement('option');
          option.value = item.id;
          option.textContent = `${item.altura} cm - ${item.cajas} cajas`;
          alturaSelect.appendChild(option);
        });
      })
      .catch(() => {
        alturaSelect.innerHTML = '<option value="">Error al cargar datos</option>';
      });
  });

  alturaSelect.addEventListener('change', () => {
    const selectedOption = alturaSelect.options[alturaSelect.selectedIndex];
    
  });
});
