let todosLosCalibes = [];
let paginaActual = 1;

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
        const id = document.getElementById("id_calibre").value;
        if (id) eliminarCalibre(id);
        else alert("Seleccione un registro para eliminar.");
    });

    document.getElementById("btnLimpiar").addEventListener("click", function () {
        limpiarFormulario();
    });

    document.getElementById("buscadorCalibre").addEventListener("input", () => {
        paginaActual = 1;
        renderTabla();
    });

    document.getElementById("porPaginaCalibre").addEventListener("change", () => {
        paginaActual = 1;
        renderTabla();
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

function cargarTabla() {
    fetch("../models/obtener_calibres.php")
        .then(response => response.json())
        .then(data => {
            todosLosCalibes = data;
            paginaActual = 1;
            renderTabla();
        })
        .catch(error => {
            console.error("Error cargando tabla:", error);
            document.querySelector("#tablaCalibres tbody").innerHTML =
                '<tr><td colspan="6" class="text-center text-danger">Error cargando datos</td></tr>';
        });
}

function renderTabla() {
    const busqueda = document.getElementById("buscadorCalibre").value.toLowerCase().trim();
    const porPagina = parseInt(document.getElementById("porPaginaCalibre").value);
    const tbody = document.querySelector("#tablaCalibres tbody");

    const filtrados = todosLosCalibes.filter(item =>
        String(item.id).includes(busqueda) ||
        (item.cod_calibre || "").toLowerCase().includes(busqueda) ||
        (item.nombre_calibre || "").toLowerCase().includes(busqueda) ||
        (item.especie || "").toLowerCase().includes(busqueda)
    );

    const totalPaginas = Math.max(1, Math.ceil(filtrados.length / porPagina));
    if (paginaActual > totalPaginas) paginaActual = totalPaginas;

    const inicio = (paginaActual - 1) * porPagina;
    const pagina = filtrados.slice(inicio, inicio + porPagina);

    tbody.innerHTML = "";
    if (!pagina.length) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center">No hay registros</td></tr>';
    } else {
        pagina.forEach(calibre => {
            const row = tbody.insertRow();
            row.innerHTML = `
                <td>${calibre.id}</td>
                <td>${calibre.cod_calibre}</td>
                <td>${calibre.nombre_calibre}</td>
                <td>${calibre.orden ?? 'N/A'}</td>
                <td>${calibre.especie || 'N/A'}</td>
                <td>
                    <button class="btn btn-sm btn-warning" onclick="cargarCalibre(${calibre.id}, '${calibre.cod_calibre}', '${calibre.nombre_calibre}', ${calibre.orden ?? 'null'}, '${calibre.id_especie || ''}')">Editar</button>
                    <button class="btn btn-sm btn-danger" onclick="eliminarCalibre(${calibre.id})">Eliminar</button>
                </td>
            `;
        });
    }

    const desde = filtrados.length ? inicio + 1 : 0;
    const hasta = Math.min(inicio + porPagina, filtrados.length);
    document.getElementById("infoCalibre").textContent =
        `Mostrando ${desde}–${hasta} de ${filtrados.length} registros${busqueda ? " (filtrados)" : ""}`;

    const nav = document.getElementById("paginacionCalibre");
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
    let formData = new FormData(document.getElementById("formCalibre"));
    formData.append("accion", accion);

    fetch("../controllers/procesar_calibre.php", {
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

function cargarCalibre(id, codigo, nombre, orden, id_especie) {
    document.getElementById("id_calibre").value = id;
    document.getElementById("codigo_calibre").value = codigo;
    document.getElementById("nombre_calibre").value = nombre;
    document.getElementById("orden").value = orden ?? '';
    document.getElementById("especie").value = id_especie;
}

function eliminarCalibre(id) {
    if (confirm("¿Está seguro de eliminar este calibre?")) {
        let formData = new FormData();
        formData.append("accion", "eliminar");
        formData.append("id_calibre", id);

        fetch("../controllers/procesar_calibre.php", {
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
    document.getElementById("formCalibre").reset();
    document.getElementById("id_calibre").value = "";
}
