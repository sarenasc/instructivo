document.addEventListener("DOMContentLoaded", function () {
    cargarEspecies();
    cargarTabla();

    document.getElementById("btnGuardar").addEventListener("click", function () {
        enviarFormulario("guardar");
    });

    document.getElementById("btnModificar").addEventListener("click", function () {
        enviarFormulario("modificar");
    });

    document.getElementById("btnEliminar").addEventListener("click", function () {
        enviarFormulario("eliminar");
    });

    document.getElementById("btnLimpiar").addEventListener("click", function () {
        limpiarFormulario();
    });
});

function cargarEspecies() {
    fetch("../obtener_especies.php")
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

function cargarTabla() {
    fetch("../obtener_plus.php")
        .then(response => response.json())
        .then(data => {
            let tbody = document.querySelector("#tablaPlu tbody");
            tbody.innerHTML = "";
            
            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center">No hay registros</td></tr>';
                return;
            }
            
            data.forEach(item => {
                let row = tbody.insertRow();
                row.innerHTML = `
                    <td>${item.id_plu}</td>
                    <td>${item.codigo_plu}</td>
                    <td>${item.nombre_plu}</td>
                    <td>${item.especie || 'N/A'}</td>
                    <td>
                        <button class="btn btn-sm btn-warning" onclick="cargarPlu(${item.id_plu}, '${item.codigo_plu}', '${item.nombre_plu}', '${item.id_especie || ''}')">
                            ✏️ Editar
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="eliminarPlu(${item.id_plu})">
                            🗑️ Eliminar
                        </button>
                    </td>
                `;
            });
        })
        .catch(error => {
            console.error("Error cargando tabla:", error);
            let tbody = document.querySelector("#tablaPlu tbody");
            tbody.innerHTML = '<tr><td colspan="5" class="text-center text-danger">Error cargando datos</td></tr>';
        });
}

function enviarFormulario(accion) {
    let formData = new FormData(document.getElementById("formPlu"));
    formData.append("accion", accion);

    fetch("../procesar_plu.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        alert(data);
        if (data.includes("éxito") || data.includes("correctamente") || data.includes("Eliminado")) {
            limpiarFormulario();
            cargarTabla();
        }
    })
    .catch(error => console.error("Error en el proceso:", error));
}

function cargarPlu(id, codigo, nombre, id_especie) {
    document.getElementById("id_plu").value = id;
    document.getElementById("codigo_plu").value = codigo;
    document.getElementById("nombre_plu").value = nombre;
    document.getElementById("especie").value = id_especie;
}

function eliminarPlu(id) {
    if (confirm("¿Está seguro de eliminar este PLU?")) {
        let formData = new FormData();
        formData.append("accion", "eliminar");
        formData.append("id_plu", id);

        fetch("../procesar_plu.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            alert(data);
            cargarTabla();
        })
        .catch(error => console.error("Error:", error));
    }
}

function limpiarFormulario() {
    document.getElementById("formPlu").reset();
    document.getElementById("id_plu").value = "";
}
