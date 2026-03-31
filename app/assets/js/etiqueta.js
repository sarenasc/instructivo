let todasLasEtiquetas = [];
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
        const id = document.getElementById("id_etiqueta").value;
        if (id) eliminarEtiqueta(id);
        else alert("Seleccione un registro para eliminar.");
    });

    document.getElementById("btnLimpiar").addEventListener("click", function () {
        limpiarFormulario();
    });

    document.getElementById("buscadorEtiqueta").addEventListener("input", () => {
        paginaActual = 1;
        renderTabla();
    });

    document.getElementById("porPaginaEtiqueta").addEventListener("change", () => {
        paginaActual = 1;
        renderTabla();
    });
});

function cargarExportadora() {
    fetch("../models/obtener_exportadoras.php")
        .then(response => response.json())
        .then(data => {
            let select = document.getElementById("exportadora");
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
    fetch("../models/obtener_etiquetas.php")
        .then(response => response.json())
        .then(data => {
            todasLasEtiquetas = data;
            paginaActual = 1;
            renderTabla();
        })
        .catch(error => {
            console.error("Error cargando tabla:", error);
            document.querySelector("#tablaEtiqueta tbody").innerHTML =
                '<tr><td colspan="5" class="text-center text-danger">Error cargando datos</td></tr>';
        });
}

function renderTabla() {
    const busqueda = document.getElementById("buscadorEtiqueta").value.toLowerCase().trim();
    const porPagina = parseInt(document.getElementById("porPaginaEtiqueta").value);
    const tbody = document.querySelector("#tablaEtiqueta tbody");

    const filtrados = todasLasEtiquetas.filter(item =>
        String(item.id).includes(busqueda) ||
        (item.codigo_etiqueta || "").toLowerCase().includes(busqueda) ||
        (item.nombre_etiqueta || "").toLowerCase().includes(busqueda) ||
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
                <td>${item.codigo_etiqueta}</td>
                <td>${item.nombre_etiqueta}</td>
                <td>${item.nombre_exportadora || 'N/A'}</td>
                <td>
                    <button class="btn btn-sm btn-warning" onclick="cargarEtiqueta(${item.id}, '${item.codigo_etiqueta}', '${item.nombre_etiqueta}', '${item.id_exportadora || ''}')">Editar</button>
                    <button class="btn btn-sm btn-danger" onclick="eliminarEtiqueta(${item.id})">Eliminar</button>
                </td>
            `;
        });
    }

    const desde = filtrados.length ? inicio + 1 : 0;
    const hasta = Math.min(inicio + porPagina, filtrados.length);
    document.getElementById("infoEtiqueta").textContent =
        `Mostrando ${desde}–${hasta} de ${filtrados.length} registros${busqueda ? " (filtrados)" : ""}`;

    const nav = document.getElementById("paginacionEtiqueta");
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
    let formData = new FormData(document.getElementById("formEtiqueta"));
    formData.append("accion", accion);

    fetch("../controllers/procesar_etiqueta.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        alert(data);
        if (data.includes("correctamente")) {
            limpiarFormulario();
            cargarTabla();
        }
    })
    .catch(error => console.error("Error en el proceso:", error));
}

function cargarEtiqueta(id, codigo, nombre, id_exportadora) {
    document.getElementById("id_etiqueta").value = id;
    document.getElementById("codigo_etiqueta").value = codigo;
    document.getElementById("nombre_etiqueta").value = nombre;
    document.getElementById("exportadora").value = id_exportadora;
}

function eliminarEtiqueta(id) {
    if (confirm("¿Está seguro de eliminar esta etiqueta?")) {
        let formData = new FormData();
        formData.append("accion", "eliminar");
        formData.append("id_etiqueta", id);

        fetch("../controllers/procesar_etiqueta.php", {
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
    document.getElementById("formEtiqueta").reset();
    document.getElementById("id_etiqueta").value = "";
}
