let todosLosRegistros = [];

document.addEventListener("DOMContentLoaded", function () {
    cargarEspecies();
    cargarTabla();

    document.getElementById("id_especie").addEventListener("change", function () {
        const id_especie = this.value;
        limpiarSelect("id_exportadora", "Seleccione especie primero");
        limpiarSelect("id_categoria",   "Seleccione especie y exportadora");
        limpiarSelect("calibres",       "Seleccione especie primero");
        if (!id_especie) return;
        cargarExportadoras(id_especie);
        cargarCalibres(id_especie);
    });

    document.getElementById("id_exportadora").addEventListener("change", function () {
        const id_especie     = document.getElementById("id_especie").value;
        const id_exportadora = this.value;
        limpiarSelect("id_categoria", "Seleccione...");
        if (!id_especie || !id_exportadora) return;
        cargarCategorias(id_especie, id_exportadora);
    });

    document.getElementById("btnGuardar").addEventListener("click",   () => enviar("guardar"));
    document.getElementById("btnModificar").addEventListener("click", () => enviar("modificar"));
    document.getElementById("btnEliminar").addEventListener("click",  () => {
        const id = document.getElementById("id_agrupacion").value;
        if (id) eliminar(id);
        else alert("Seleccione un registro para eliminar.");
    });
    document.getElementById("btnLimpiar").addEventListener("click", limpiar);
    document.getElementById("buscador").addEventListener("input", renderTabla);
});

// ── Carga de combos ───────────────────────────────────────────────────────────

function cargarEspecies() {
    fetch("../services/api_especies.php")
        .then(r => r.json())
        .then(data => {
            const sel = document.getElementById("id_especie");
            sel.innerHTML = '<option value="">Seleccione...</option>';
            data.forEach(e => sel.innerHTML += `<option value="${e.id_especie}">${e.especie}</option>`);
        });
}

function cargarExportadoras(id_especie) {
    fetch(`../services/api_exportadoras.php?id_especie=${id_especie}`)
        .then(r => r.json())
        .then(data => {
            const sel = document.getElementById("id_exportadora");
            sel.innerHTML = '<option value="">Seleccione exportadora...</option>';
            data.forEach(ex => sel.innerHTML +=
                `<option value="${ex.id}">${ex.nombre_exportadora}</option>`);
        });
}

function cargarCategorias(id_especie, id_exportadora) {
    fetch(`../models/obtener_categoria.php?id_especie=${id_especie}&id_exportadora=${id_exportadora}`)
        .then(r => r.json())
        .then(data => {
            const sel = document.getElementById("id_categoria");
            sel.innerHTML = '<option value="">Seleccione categoría...</option>';
            data.forEach(c => sel.innerHTML +=
                `<option value="${c.id}">${c.nombre_categoria}</option>`);
        });
}

function cargarCalibres(id_especie) {
    fetch(`../models/obtener_calibres.php?id_especie=${id_especie}`)
        .then(r => r.json())
        .then(data => {
            const sel = document.getElementById("calibres");
            sel.innerHTML = "";
            data.forEach(c => sel.innerHTML +=
                `<option value="${c.id}">${c.cod_calibre} - ${c.nombre_calibre}</option>`);
        });
}

// ── Tabla ─────────────────────────────────────────────────────────────────────

function cargarTabla() {
    fetch("../models/obtener_agrupacion_calibre.php")
        .then(r => r.json())
        .then(data => { todosLosRegistros = data; renderTabla(); })
        .catch(() => {
            document.querySelector("#tablaAgrupaciones tbody").innerHTML =
                '<tr><td colspan="7" class="text-center text-danger">Error cargando datos</td></tr>';
        });
}

function renderTabla() {
    const busqueda = document.getElementById("buscador").value.toLowerCase().trim();
    const tbody    = document.querySelector("#tablaAgrupaciones tbody");

    const filtrados = todosLosRegistros.filter(r =>
        (r.nombre_especie      || "").toLowerCase().includes(busqueda) ||
        (r.nombre_exportadora  || "").toLowerCase().includes(busqueda) ||
        (r.nombre_categoria    || "").toLowerCase().includes(busqueda) ||
        (r.nombre_grupo        || "").toLowerCase().includes(busqueda) ||
        (r.calibres_lista      || "").toLowerCase().includes(busqueda)
    );

    tbody.innerHTML = "";
    if (!filtrados.length) {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center">No hay registros</td></tr>';
        return;
    }
    filtrados.forEach(r => {
        const tr = tbody.insertRow();
        tr.innerHTML = `
            <td>${r.id}</td>
            <td>${r.nombre_especie}</td>
            <td>${r.nombre_exportadora}</td>
            <td>${r.nombre_categoria}</td>
            <td><strong>${r.nombre_grupo}</strong></td>
            <td><small>${r.calibres_lista || '—'}</small></td>
            <td>
                <button class="btn btn-sm btn-warning"
                    onclick="cargarRegistro(${r.id}, ${r.id_especie}, ${r.id_exportadora}, ${r.id_categoria}, '${r.nombre_grupo}')">
                    Editar
                </button>
                <button class="btn btn-sm btn-danger" onclick="eliminar(${r.id})">Eliminar</button>
            </td>`;
    });
}

// ── CRUD ──────────────────────────────────────────────────────────────────────

function enviar(accion) {
    const id             = document.getElementById("id_agrupacion").value;
    const id_especie     = document.getElementById("id_especie").value;
    const id_exportadora = document.getElementById("id_exportadora").value;
    const id_categoria   = document.getElementById("id_categoria").value;
    const nombre_grupo   = document.getElementById("nombre_grupo").value.trim();
    const calibresOpts   = [...document.getElementById("calibres").selectedOptions];

    if (!id_especie || !id_exportadora || !id_categoria || !nombre_grupo) {
        alert("Especie, exportadora, categoría y nombre de grupo son obligatorios."); return;
    }
    if (calibresOpts.length === 0) {
        alert("Seleccione al menos un calibre."); return;
    }
    if (accion === "modificar" && !id) {
        alert("Seleccione un registro para modificar."); return;
    }

    const fd = new FormData();
    fd.append("accion",       accion);
    fd.append("id",           id);
    fd.append("id_especie",   id_especie);
    fd.append("id_exportadora", id_exportadora);
    fd.append("id_categoria", id_categoria);
    fd.append("nombre_grupo", nombre_grupo);
    calibresOpts.forEach(opt => fd.append("calibres[]", opt.value));

    fetch("../controllers/procesar_agrupacion_calibre.php", { method: "POST", body: fd })
        .then(r => r.text())
        .then(msg => {
            alert(msg);
            if (msg.includes("correctamente")) { limpiar(); cargarTabla(); }
        });
}

function eliminar(id) {
    if (!confirm("¿Eliminar esta agrupación y sus calibres asociados?")) return;
    const fd = new FormData();
    fd.append("accion", "eliminar");
    fd.append("id", id);
    fetch("../controllers/procesar_agrupacion_calibre.php", { method: "POST", body: fd })
        .then(r => r.text())
        .then(msg => { alert(msg); cargarTabla(); });
}

async function cargarRegistro(id, id_especie, id_exportadora, id_categoria, nombre_grupo) {
    document.getElementById("id_agrupacion").value  = id;
    document.getElementById("id_especie").value     = id_especie;

    // Cargar exportadoras y categorías en cascada antes de setear los valores
    await cargarExportadorasAsync(id_especie);
    document.getElementById("id_exportadora").value = id_exportadora;

    await cargarCategoriasAsync(id_especie, id_exportadora);
    document.getElementById("id_categoria").value   = id_categoria;

    await cargarCalibresAsync(id_especie);
    document.getElementById("nombre_grupo").value   = nombre_grupo;

    // Marcar calibres seleccionados de este grupo
    const resp = await fetch(`../models/obtener_calibres_por_agrupacion.php?id=${id}`);
    const calibresDelGrupo = await resp.json();
    const idsSeleccionados = new Set(calibresDelGrupo.map(c => String(c.id_calibre)));
    const sel = document.getElementById("calibres");
    [...sel.options].forEach(opt => { opt.selected = idsSeleccionados.has(opt.value); });
}

// Versiones async de las cargas para usar con await
function cargarExportadorasAsync(id_especie) {
    return fetch(`../services/api_exportadoras.php?id_especie=${id_especie}`)
        .then(r => r.json())
        .then(data => {
            const sel = document.getElementById("id_exportadora");
            sel.innerHTML = '<option value="">Seleccione exportadora...</option>';
            data.forEach(ex => sel.innerHTML +=
                `<option value="${ex.id}">${ex.nombre_exportadora}</option>`);
        });
}

function cargarCategoriasAsync(id_especie, id_exportadora) {
    return fetch(`../models/obtener_categoria.php?id_especie=${id_especie}&id_exportadora=${id_exportadora}`)
        .then(r => r.json())
        .then(data => {
            const sel = document.getElementById("id_categoria");
            sel.innerHTML = '<option value="">Seleccione categoría...</option>';
            data.forEach(c => sel.innerHTML +=
                `<option value="${c.id}">${c.nombre_categoria}</option>`);
        });
}

function cargarCalibresAsync(id_especie) {
    return fetch(`../models/obtener_calibres.php?id_especie=${id_especie}`)
        .then(r => r.json())
        .then(data => {
            const sel = document.getElementById("calibres");
            sel.innerHTML = "";
            data.forEach(c => sel.innerHTML +=
                `<option value="${c.id}">${c.cod_calibre} - ${c.nombre_calibre}</option>`);
        });
}

// ── Helpers ───────────────────────────────────────────────────────────────────

function limpiarSelect(id, placeholder) {
    document.getElementById(id).innerHTML = `<option value="">${placeholder}</option>`;
}

function limpiar() {
    document.getElementById("id_agrupacion").value = "";
    document.getElementById("formAgrupacion").reset();
    limpiarSelect("id_exportadora", "Seleccione especie primero");
    limpiarSelect("id_categoria",   "Seleccione especie y exportadora");
    limpiarSelect("calibres",       "Seleccione especie primero");
}
