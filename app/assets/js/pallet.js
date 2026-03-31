let todosLosPallets = [];
let paginaActual = 1;

document.addEventListener("DOMContentLoaded", function () {
    cargarExportadora();
    cargarTabla();

    document.getElementById("btnGuardar").addEventListener("click", function () {
        enviarFormulario("guardar");
    });

    document.getElementById("btnModificar").addEventListener("click", function () {
        enviarFormulario("modificar");
    });

    document.getElementById("btnEliminar").addEventListener("click", function () {
        const id = document.getElementById("id_pallet").value;
        if (id) eliminarPallet(id);
        else alert("Seleccione un registro para eliminar.");
    });

    document.getElementById("btnLimpiar").addEventListener("click", function () {
        limpiarFormulario();
    });

    document.getElementById("buscadorPallet").addEventListener("input", () => {
        paginaActual = 1;
        renderTabla();
    });

    document.getElementById("porPaginaPallet").addEventListener("change", () => {
        paginaActual = 1;
        renderTabla();
    });
});

function cargarExportadora() {
    fetch("../models/obtener_exportadoras.php")
        .then(response => response.json())
        .then(data => {
            let select = document.getElementById("id_exportadora");
            select.innerHTML = '<option value="">Seleccione una exportadora</option>';
            data.forEach(exportadora => {
                let option = document.createElement("option");
                option.value = exportadora.id;
                option.textContent = exportadora.nombre_exportadora;
                select.appendChild(option);
            });
        })
        .catch(error => console.error("Error cargando exportadoras:", error));
}

function cargarTabla() {
    fetch("../models/obtener_pallets.php")
        .then(response => response.json())
        .then(data => {
            todosLosPallets = data;
            paginaActual = 1;
            renderTabla();
        })
        .catch(error => {
            console.error("Error cargando tabla:", error);
            document.querySelector("#tablaPallet tbody").innerHTML =
                '<tr><td colspan="5" class="text-center text-danger">Error cargando datos</td></tr>';
        });
}

function renderTabla() {
    const busqueda = document.getElementById("buscadorPallet").value.toLowerCase().trim();
    const porPagina = parseInt(document.getElementById("porPaginaPallet").value);
    const tbody = document.querySelector("#tablaPallet tbody");

    const filtrados = todosLosPallets.filter(item =>
        String(item.id).includes(busqueda) ||
        (item.cod_pallet || "").toLowerCase().includes(busqueda) ||
        (item.descrip_pallet || "").toLowerCase().includes(busqueda) ||
        (item.nombre_exportadora || "").toLowerCase().includes(busqueda)
    );

    const totalPaginas = Math.max(1, Math.ceil(filtrados.length / porPagina));
    if (paginaActual > totalPaginas) paginaActual = totalPaginas;

    const inicio = (paginaActual - 1) * porPagina;
    const pagina = filtrados.slice(inicio, inicio + porPagina);

    tbody.innerHTML = "";
    if (!pagina.length) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center">No hay registros</td></tr>';
    } else {
        pagina.forEach(item => {
            const row = tbody.insertRow();
            row.innerHTML = `
                <td>${item.id}</td>
                <td>${item.cod_pallet}</td>
                <td>${item.descrip_pallet}</td>
                <td>${item.nombre_exportadora || 'N/A'}</td>
                <td>
                    <button class="btn btn-sm btn-warning" onclick="cargarPallet(${item.id}, '${item.cod_pallet}', '${item.descrip_pallet}', '${item.id_exportadora || ''}')">Editar</button>
                    <button class="btn btn-sm btn-danger" onclick="eliminarPallet(${item.id})">Eliminar</button>
                </td>
            `;
        });
    }

    const desde = filtrados.length ? inicio + 1 : 0;
    const hasta = Math.min(inicio + porPagina, filtrados.length);
    document.getElementById("infoPallet").textContent =
        `Mostrando ${desde}–${hasta} de ${filtrados.length} registros${busqueda ? " (filtrados)" : ""}`;

    const nav = document.getElementById("paginacionPallet");
    nav.innerHTML = "";

    const crearLi = (texto, pag, deshabilitado = false, activo = false) => {
        const li = document.createElement("li");
        li.className = `page-item${deshabilitado ? " disabled" : ""}${activo ? " active" : ""}`;
        const a = document.createElement("a");
        a.className = "page-link";
        a.href = "#";
        a.innerHTML = texto;
        if (!deshabilitado && !activo) {
            a.addEventListener("click", e => { e.preventDefault(); paginaActual = pag; renderTabla(); });
        }
        li.appendChild(a);
        return li;
    };

    nav.appendChild(crearLi("&laquo;", paginaActual - 1, paginaActual === 1));
    const rango = 2;
    for (let i = 1; i <= totalPaginas; i++) {
        if (i === 1 || i === totalPaginas || (i >= paginaActual - rango && i <= paginaActual + rango)) {
            nav.appendChild(crearLi(i, i, false, i === paginaActual));
        } else if (i === paginaActual - rango - 1 || i === paginaActual + rango + 1) {
            nav.appendChild(crearLi("…", i, true));
        }
    }
    nav.appendChild(crearLi("&raquo;", paginaActual + 1, paginaActual === totalPaginas));
}

function enviarFormulario(accion) {
    let formData = new FormData(document.getElementById("formPallet"));
    formData.append("accion", accion);

    fetch("../controllers/procesar_pallet.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        alert(data);
        if (data.includes("Ã©xito") || data.includes("correctamente") || data.includes("Eliminado")) {
            limpiarFormulario();
            cargarTabla();
        }
    })
    .catch(error => console.error("Error en el proceso:", error));
}

function cargarPallet(id, codigo, descripcion, id_exportadora) {
    document.getElementById("id_pallet").value = id;
    document.getElementById("cod_pallet").value = codigo;
    document.getElementById("descrip_pallet").value = descripcion;
    document.getElementById("id_exportadora").value = id_exportadora;
}

function eliminarPallet(id) {
    if (confirm("¿Esta seguro de eliminar este pallet?")) {
        let formData = new FormData();
        formData.append("accion", "eliminar");
        formData.append("id_pallet", id);

        fetch("../controllers/procesar_pallet.php", {
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
    document.getElementById("formPallet").reset();
    document.getElementById("id_pallet").value = "";
}

