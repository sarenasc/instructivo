document.addEventListener("DOMContentLoaded", function () {
    cargarExportadora();

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

function cargarExportadora() {
    fetch("../api_exportadora.php")
        .then(response => response.json())
        .then(data => {
            let select = document.getElementById("exportadora");
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

function enviarFormulario(accion) {
    let formData = new FormData(document.getElementById("formEtiqueta"));
    formData.append("accion", accion);

    fetch("../procesar_etiquetas.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.text())
    .then(data => alert(data))
    .catch(error => console.error("Error en el proceso:", error));
}
