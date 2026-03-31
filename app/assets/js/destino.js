let todosLosDestinos = [];
let paginaActual = 1;

document.addEventListener("DOMContentLoaded", function () {
    cargarTabla();

    document.getElementById("btnGuardar").addEventListener("click", function () {
        enviarFormulario("guardar");
    });

    document.getElementById("btnModificar").addEventListener("click", function () {
        enviarFormulario("modificar");
    });

    document.getElementById("btnEliminar").addEventListener("click", function () {
        const id = document.getElementById("id_destino").value;
        if (id) eliminarDestino(id);
        else alert("Seleccione un registro para eliminar.");
    });

    document.getElementById("btnLimpiar").addEventListener("click", function () {
        limpiarFormulario();
    });

    document.getElementById("buscadorDestino").addEventListener("input", () => {
        paginaActual = 1;
        renderTabla();
    });

    document.getElementById("porPaginaDestino").addEventListener("change", () => {
        paginaActual = 1;
        renderTabla();
    });
});

function cargarTabla() {
    fetch("../models/obtener_destinos.php")
        .then(response => response.json())
        .then(data => {
            todosLosDestinos = data;
            paginaActual = 1;
            renderTabla();
        })
        .catch(error => {
            console.error("Error cargando tabla:", error);
            document.querySelector("#tablaDestino tbody").innerHTML =
                '<tr><td colspan="4" class="text-center text-danger">Error cargando datos</td></tr>';
        });
}

function renderTabla() {
    const busqueda = document.getElementById("buscadorDestino").value.toLowerCase().trim();
    const porPagina = parseInt(document.getElementById("porPaginaDestino").value);
    const tbody = document.querySelector("#tablaDestino tbody");

    const filtrados = todosLosDestinos.filter(item =>
        String(item.id).includes(busqueda) ||
        (item.codigo_destino || "").toLowerCase().includes(busqueda) ||
        (item.nombre_destino || "").toLowerCase().includes(busqueda)
    );

    const totalPaginas = Math.max(1, Math.ceil(filtrados.length / porPagina));
    if (paginaActual > totalPaginas) paginaActual = totalPaginas;

    const inicio = (paginaActual - 1) * porPagina;
    const pagina = filtrados.slice(inicio, inicio + porPagina);

    tbody.innerHTML = "";
    if (!pagina.length) {
        tbody.innerHTML = '<tr><td colspan="4" class="text-center">No hay registros</td></tr>';
    } else {
        pagina.forEach(item => {
            const row = tbody.insertRow();
            row.innerHTML = `
                <td>${item.id}</td>
                <td>${item.codigo_destino}</td>
                <td>${item.nombre_destino}</td>
                <td>
                    <button class="btn btn-sm btn-warning" onclick="cargarDestino(${item.id}, '${item.codigo_destino}', '${item.nombre_destino}')">Editar</button>
                    <button class="btn btn-sm btn-danger" onclick="eliminarDestino(${item.id})">Eliminar</button>
                </td>
            `;
        });
    }

    const desde = filtrados.length ? inicio + 1 : 0;
    const hasta = Math.min(inicio + porPagina, filtrados.length);
    document.getElementById("infoDestino").textContent =
        `Mostrando ${desde}–${hasta} de ${filtrados.length} registros${busqueda ? " (filtrados)" : ""}`;

    const nav = document.getElementById("paginacionDestino");
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
    let formData = new FormData(document.getElementById("formDestino"));
    formData.append("accion", accion);

    fetch("../controllers/procesar_destino.php", {
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

function cargarDestino(id, codigo, nombre) {
    document.getElementById("id_destino").value = id;
    document.getElementById("codigo_destino").value = codigo;
    document.getElementById("nombre_destino").value = nombre;
}

function eliminarDestino(id) {
    if (confirm("¿Está seguro de eliminar este destino?")) {
        let formData = new FormData();
        formData.append("accion", "eliminar");
        formData.append("id_destino", id);

        fetch("../controllers/procesar_destino.php", {
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
    document.getElementById("formDestino").reset();
    document.getElementById("id_destino").value = "";
}
