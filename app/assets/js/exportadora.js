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
 cargarTabla();
function enviarFormulario(accion) {
    let formData = new FormData(document.getElementById("formExportadora"));
    formData.append("accion", accion);

    fetch("../procesar_exportadora.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.text())
    .then(data => alert(data))
    .catch(error => console.error("Error en la operación:", error));
}

 // Función para cargar tabla
    function cargarTabla() {
        fetch("../api_exportadora.php")
            .then(res => res.json())
            .then(data => {
                tabla.innerHTML = data.map(row => `
                    <tr data-id="${row.id}">
                        <td>${row.Nombre_Exportadora}</td>
                        <td>${row.cod_exportadora}</td>
                        
                    </tr>
                `).join("");

                document.querySelectorAll("#tablaAlturas tbody tr").forEach(tr => {
                    tr.addEventListener("click", () => {
                        document.querySelectorAll("#tablaAlturas tbody tr").forEach(row => row.classList.remove("table-primary"));
                        tr.classList.add("table-primary");
                        const celdas = tr.children;
                        //idSeleccionado = tr.dataset.id;
                       // idEmbalaje.value = data.find(d => d.id == idSeleccionado).id_embalaje;
                        Nombre_Exportadora.value = celdas[1].textContent;
                        cod_exportadora.value = celdas[2].textContent;
                    });
                });
            });
    }

   