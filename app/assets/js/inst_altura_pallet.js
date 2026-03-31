let todasLasAlturas = [];
let todosLosEmbalajes = [];
let paginaActual = 1;

document.addEventListener("DOMContentLoaded", function () {
    cargarEmbalajes();
    cargarTabla();

    document.getElementById("btnGuardar").addEventListener("click", () => procesar("guardar"));
    document.getElementById("btnModificar").addEventListener("click", () => procesar("modificar"));
    document.getElementById("btnEliminar").addEventListener("click", eliminar);
    document.getElementById("btnLimpiar").addEventListener("click", limpiar);

    document.getElementById("buscadorAlturas").addEventListener("input", () => {
        paginaActual = 1;
        renderTabla();
    });

    document.getElementById("porPaginaAlturas").addEventListener("change", () => {
        paginaActual = 1;
        renderTabla();
    });

    document.getElementById("buscadorEmbalaje").addEventListener("input", function () {
        filtrarSelectEmbalaje(this.value);
    });
});

function cargarEmbalajes() {
    fetch("../models/obtener_embalajes.php")
        .then(r => r.json())
        .then(data => {
            todosLosEmbalajes = data;
            renderSelectEmbalaje(data);
        })
        .catch(e => console.error("Error cargando embalajes:", e));
}

function renderSelectEmbalaje(data) {
    const select = document.getElementById("id_embalaje");
    const valorActual = select.value;
    select.innerHTML = '<option value="">Seleccione un embalaje</option>';
    data.forEach(item => {
        const option = document.createElement("option");
        option.value = item.id;
        option.textContent = `${item.codigo_embalaje} - ${item.nombre_embalaje} (${item.especie || 'S/E'})`;
        select.appendChild(option);
    });
    if (valorActual) select.value = valorActual;
}

function filtrarSelectEmbalaje(busqueda) {
    const texto = busqueda.toLowerCase();
    const filtrados = todosLosEmbalajes.filter(item =>
        (item.codigo_embalaje || "").toLowerCase().includes(texto) ||
        (item.nombre_embalaje || "").toLowerCase().includes(texto) ||
        (item.especie || "").toLowerCase().includes(texto)
    );
    renderSelectEmbalaje(filtrados);
}

function cargarTabla() {
    fetch("../models/obtener_altura_pallet.php")
        .then(r => r.json())
        .then(data => {
            todasLasAlturas = data;
            paginaActual = 1;
            renderTabla();
        })
        .catch(e => {
            console.error("Error cargando tabla:", e);
            document.querySelector("#tablaAlturas tbody").innerHTML =
                '<tr><td colspan="6" class="text-center text-danger">Error cargando datos</td></tr>';
        });
}

function renderTabla() {
    const busqueda = document.getElementById("buscadorAlturas").value.toLowerCase().trim();
    const porPagina = parseInt(document.getElementById("porPaginaAlturas").value);
    const tbody = document.querySelector("#tablaAlturas tbody");

    const filtrados = todasLasAlturas.filter(item =>
        String(item.id_altura_pallet).includes(busqueda) ||
        (item.nombre_embalaje || "").toLowerCase().includes(busqueda) ||
        (item.codigo_embalaje || "").toLowerCase().includes(busqueda) ||
        String(item.altura).includes(busqueda) ||
        String(item.cajas).includes(busqueda)
    );

    const totalPaginas = Math.max(1, Math.ceil(filtrados.length / porPagina));
    if (paginaActual > totalPaginas) paginaActual = totalPaginas;

    const inicio = (paginaActual - 1) * porPagina;
    const pagina = filtrados.slice(inicio, inicio + porPagina);

    tbody.innerHTML = "";
    if (!pagina.length) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center">No hay registros</td></tr>';
    } else {
        pagina.forEach(item => {
            const row = tbody.insertRow();
            row.innerHTML = `
                <td>${item.id_altura_pallet}</td>
                <td>${item.codigo_embalaje ? `${item.codigo_embalaje} - ${item.nombre_embalaje}` : 'N/A'}</td>
                <td>${item.especie || 'N/A'}</td>
                <td>${item.altura}</td>
                <td>${item.cajas}</td>
                <td>
                    <button class="btn btn-sm btn-warning" onclick="cargarAltura(${item.id_altura_pallet}, ${item.id_embalaje}, ${item.altura}, ${item.cajas})">Editar</button>
                    <button class="btn btn-sm btn-danger" onclick="eliminarAltura(${item.id_altura_pallet})">Eliminar</button>
                </td>
            `;
        });
    }

    const desde = filtrados.length ? inicio + 1 : 0;
    const hasta = Math.min(inicio + porPagina, filtrados.length);
    document.getElementById("infoAlturas").textContent =
        `Mostrando ${desde}–${hasta} de ${filtrados.length} registros${busqueda ? " (filtrados)" : ""}`;

    const nav = document.getElementById("paginacionAlturas");
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

function cargarAltura(id, id_embalaje, altura, cajas) {
    document.getElementById("id_altura_pallet").value = id;
    document.getElementById("id_embalaje").value = id_embalaje;
    document.getElementById("altura").value = altura;
    document.getElementById("cajas").value = cajas;
}

function eliminarAltura(id) {
    if (!confirm("¿Eliminar este registro?")) return;

    const fd = new FormData();
    fd.append("accion", "eliminar");
    fd.append("id_altura_pallet", id);

    fetch("../controllers/procesar_altura_pallet.php", { method: "POST", body: fd })
        .then(r => r.text())
        .then(d => {
            alert(d);
            cargarTabla();
        })
        .catch(e => console.error("Error:", e));
}

function procesar(accion) {
    const fd = new FormData(document.getElementById("formAltura"));
    fd.append("accion", accion);

    fetch("../controllers/procesar_altura_pallet.php", { method: "POST", body: fd })
        .then(r => r.text())
        .then(d => {
            alert(d);
            if (d.includes("correctamente")) {
                limpiar();
                cargarTabla();
            }
        })
        .catch(e => console.error("Error:", e));
}

function eliminar() {
    const id = document.getElementById("id_altura_pallet").value;
    if (!id) { alert("Seleccione un registro para eliminar."); return; }
    eliminarAltura(id);
}

function limpiar() {
    document.getElementById("formAltura").reset();
    document.getElementById("id_altura_pallet").value = "";
}
