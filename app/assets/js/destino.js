document.addEventListener("DOMContentLoaded", function () {


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


function enviarFormulario(accion) {
    let formData = new FormData(document.getElementById("formDestino"));
    formData.append("accion", accion);

    fetch("../procesar_destino.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.text())
    .then(data => alert(data))
    .catch(error => console.error("Error en el proceso:", error));
}
