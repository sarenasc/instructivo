let todosLosEmbalajes = [];
let paginaActual = 1;

document.addEventListener("DOMContentLoaded", function () {
    cargarEtiquetas();
    cargarEspecies();
    cargarExportadoras();
    cargarTabla();

    document.getElementById("btnGuardar").addEventListener("click", () => procesar("guardar"));
    document.getElementById("btnModificar").addEventListener("click", () => procesar("modificar"));
    document.getElementById("btnEliminar").addEventListener("click", () => {
        const id = document.getElementById("id_embalaje").value;
        if (id) eliminarEmbalaje(id);
        else alert("Seleccione un registro para eliminar.");
    });
    document.getElementById("btnLimpiar").addEventListener("click", limpiar);

    document.getElementById("buscadorEmbalaje").addEventListener("input", () => {
        paginaActual = 1;
        renderTabla();
    });
    document.getElementById("porPaginaEmbalaje").addEventListener("change", () => {
        paginaActual = 1;
        renderTabla();
    });
});

function cargarEtiquetas() {
    fetch("../models/obtener_etiquetas.php")
        .then(r => r.json())
        .then(data => {
            const select = document.getElementById("etiqueta");
            select.innerHTML = '<option value="">Seleccione una etiqueta</option>';
            data.forEach(item => {
                const option = document.createElement("option");
                option.value = item.id;
                option.textContent = item.nombre_etiqueta;
                select.appendChild(option);
            });
        })
        .catch(e => console.error("Error cargando etiquetas:", e));
}

function cargarEspecies() {
    fetch("../services/api_especies.php")
        .then(r => r.json())
        .then(data => {
            const select = document.getElementById("especie");
            select.innerHTML = '<option value="">Seleccione una especie</option>';
            data.forEach(item => {
                const option = document.createElement("option");
                option.value = item.id_especie;
                option.textContent = item.especie;
                select.appendChild(option);
            });
        })
        .catch(e => console.error("Error cargando especies:", e));
}

function cargarExportadoras() {
    fetch("../models/obtener_exportadoras.php")
        .then(r => r.json())
        .then(data => {
            const select = document.getElementById("exportadora");
            select.innerHTML = '<option value="">Seleccione una exportadora</option>';
            data.forEach(item => {
                const option = document.createElement("option");
                option.value = item.id;
                option.textContent = item.nombre_exportadora;
                select.appendChild(option);
            });
        })
        .catch(e => console.error("Error cargando exportadoras:", e));
}

function cargarTabla() {
    fetch("../models/obtener_embalajes.php")
        .then(r => r.json())
        .then(data => {
            todosLosEmbalajes = data;
            paginaActual = 1;
            renderTabla();
        })
        .catch(e => {
            console.error("Error cargando tabla:", e);
            const tbody = document.querySelector("#tablaEmbalaje tbody");
            tbody.innerHTML = '<tr><td colspan="8" class="text-center text-danger">Error cargando datos</td></tr>';
        });
}

function renderTabla() {
    const busqueda = document.getElementById("buscadorEmbalaje").value.toLowerCase().trim();
    const porPagina = parseInt(document.getElementById("porPaginaEmbalaje").value);
    const tbody = document.querySelector("#tablaEmbalaje tbody");

    const filtrados = todosLosEmbalajes.filter(item => {
        return (
            String(item.id).includes(busqueda) ||
            (item.codigo_embalaje || "").toLowerCase().includes(busqueda) ||
            (item.nombre_embalaje || "").toLowerCase().includes(busqueda) ||
            (item.peso_embalaje || "").toString().toLowerCase().includes(busqueda) ||
            (item.nombre_etiqueta || "").toLowerCase().includes(busqueda) ||
            (item.especie || "").toLowerCase().includes(busqueda) ||
            (item.Nombre_Exportadora || "").toLowerCase().includes(busqueda)
        );
    });

    const totalPaginas = Math.max(1, Math.ceil(filtrados.length / porPagina));
    if (paginaActual > totalPaginas) paginaActual = totalPaginas;

    const inicio = (paginaActual - 1) * porPagina;
    const pagina = filtrados.slice(inicio, inicio + porPagina);

    tbody.innerHTML = "";
    if (!pagina.length) {
        tbody.innerHTML = '<tr><td colspan="8" class="text-center">No hay registros</td></tr>';
    } else {
        pagina.forEach(item => {
            const row = tbody.insertRow();
            row.innerHTML = `
                <td>${item.id}</td>
                <td>${item.codigo_embalaje}</td>
                <td>${item.nombre_embalaje}</td>
                <td>${item.peso_embalaje || 'N/A'}</td>
                <td>${item.nombre_etiqueta || 'N/A'}</td>
                <td>${item.especie || 'N/A'}</td>
                <td>${item.Nombre_Exportadora || 'N/A'}</td>
                <td>
                    <button class="btn btn-sm btn-warning" onclick="cargarEmbalaje(${item.id})">Editar</button>
                    <button class="btn btn-sm btn-danger" onclick="eliminarEmbalaje(${item.id})">Eliminar</button>
                </td>
            `;
        });
    }

    // Info
    const desde = filtrados.length ? inicio + 1 : 0;
    const hasta = Math.min(inicio + porPagina, filtrados.length);
    document.getElementById("infoEmbalaje").textContent =
        `Mostrando ${desde}–${hasta} de ${filtrados.length} registros${busqueda ? " (filtrados)" : ""}`;

    // Paginación
    const nav = document.getElementById("paginacionEmbalaje");
    nav.innerHTML = "";

    const crearLi = (texto, pagina, deshabilitado = false, activo = false) => {
        const li = document.createElement("li");
        li.className = `page-item${deshabilitado ? " disabled" : ""}${activo ? " active" : ""}`;
        const a = document.createElement("a");
        a.className = "page-link";
        a.href = "#";
        a.innerHTML = texto;
        if (!deshabilitado && !activo) {
            a.addEventListener("click", e => { e.preventDefault(); paginaActual = pagina; renderTabla(); });
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

function cargarEmbalaje(id) {
    fetch(`../models/obtener_embalaje_por_id.php?id=${id}`)
        .then(r => r.json())
        .then(data => {
            if (data) {
                document.getElementById("id_embalaje").value = data.id;
                document.getElementById("codigo_embalaje").value = data.codigo_embalaje;
                document.getElementById("nombre_embalaje").value = data.nombre_embalaje;
                document.getElementById("peso_embalaje").value = data.peso_embalaje || '';
                document.getElementById("etiqueta").value = data.id_etiqueta || '';
                document.getElementById("especie").value = data.id_especie || '';
                document.getElementById("exportadora").value = data.id_exportadora || '';
            }
        })
        .catch(e => console.error("Error cargando embalaje:", e));
}

function eliminarEmbalaje(id) {
    if (!confirm("Â¿Eliminar este embalaje?")) return;
    
    const fd = new FormData();
    fd.append("accion", "eliminar");
    fd.append("id_embalaje", id);
    
    fetch("../controllers/procesar_embalaje.php", {method: "POST", body: fd})
        .then(r => r.text())
        .then(d => {
            alert(d);
            cargarTabla();
        })
        .catch(e => console.error("Error:", e));
}

function procesar(accion) {
    const fd = new FormData(document.getElementById("formEmbalaje"));
    fd.append("accion", accion);
    
    fetch("../controllers/procesar_embalaje.php", {method: "POST", body: fd})
        .then(r => r.text())
        .then(d => {
            alert(d);
            if (d.includes("Ã©xito") || d.includes("correctamente") || d.includes("Eliminado")) {
                limpiar();
                cargarTabla();
                cargarEtiquetas();
                cargarEspecies();
                cargarExportadoras();
            }
        })
        .catch(e => console.error("Error:", e));
}

function limpiar() {
    document.getElementById("formEmbalaje").reset();
    document.getElementById("id_embalaje").value = "";
}

