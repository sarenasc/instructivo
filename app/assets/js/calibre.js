document.addEventListener("DOMContentLoaded", function () {
    cargarEspecies();

    document.getElementById("btnGuardar").addEventListener("click", function () {
        enviarFormulario("guardar");
    });

    document.getElementById("btnModificar").addEventListener("click", function () {
        enviarFormulario("modificar");
    });

    document.getElementById("btnEliminar").addEventListener("click", function () {
        enviarFormulario("eliminar");
    });
});

function cargarEspecies() {
    fetch("../api_especies.php")
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

function enviarFormulario(accion) {
    let formData = new FormData(document.getElementById("formCalibre"));
    formData.append("accion", accion);

    fetch("../procesar_calibre.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.text())
    .then(data => alert(data))
    .catch(error => console.error("Error en el proceso:", error));
}
