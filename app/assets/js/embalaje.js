document.addEventListener("DOMContentLoaded", function () {
    cargarEtiquetas();
    cargarEspecies();
    cargarExportadoras();
    cargarTabla();

    document.getElementById("btnGuardar").addEventListener("click", () => procesar("guardar"));
    document.getElementById("btnModificar").addEventListener("click", () => procesar("modificar"));
    document.getElementById("btnEliminar").addEventListener("click", eliminar);
    document.getElementById("btnLimpiar").addEventListener("click", limpiar);
});

function cargarEtiquetas() {
    fetch("../models/obtener_etiquetas.php")
        .then(r => r.json())
        .then(data => {
            const select = document.getElementById("etiqueta");
            select.innerHTML = '<option value="">Seleccione una etiqueta</option>';
            data.forEach(item => {
                const option = document.createElement("option");
                option.value = item.id_etiqueta;
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
                option.textContent = item.Nombre_Exportadora;
                select.appendChild(option);
            });
        })
        .catch(e => console.error("Error cargando exportadoras:", e));
}

function cargarTabla() {
    fetch("../models/obtener_embalajes.php")
        .then(r => r.json())
        .then(data => {
            const tbody = document.querySelector("#tablaEmbalaje tbody");
            tbody.innerHTML = "";
            
            if (!data.length) {
                tbody.innerHTML = '<tr><td colspan="7" class="text-center">No hay registros</td></tr>';
                return;
            }
            
            data.forEach(item => {
                const row = tbody.insertRow();
                row.innerHTML = `
                    <td>${item.id_embalaje}</td>
                    <td>${item.codigo_embalaje}</td>
                    <td>${item.nombre_embalaje}</td>
                    <td>${item.peso_embalaje || 'N/A'}</td>
                    <td>${item.nombre_etiqueta || 'N/A'}</td>
                    <td>${item.especie || 'N/A'}</td>
                    <td>${item.Nombre_Exportadora || 'N/A'}</td>
                    <td>
                        <button class="btn btn-sm btn-warning" onclick="cargarEmbalaje(${item.id_embalaje})">âœï¸ Editar</button>
                        <button class="btn btn-sm btn-danger" onclick="eliminarEmbalaje(${item.id_embalaje})">ðŸ—‘ï¸ Eliminar</button>
                    </td>
                `;
            });
        })
        .catch(e => {
            console.error("Error cargando tabla:", e);
            const tbody = document.querySelector("#tablaEmbalaje tbody");
            tbody.innerHTML = '<tr><td colspan="7" class="text-center text-danger">Error cargando datos</td></tr>';
        });
}

function cargarEmbalaje(id) {
    fetch(`../models/obtener_embalaje_por_id.php?id=${id}`)
        .then(r => r.json())
        .then(data => {
            if (data) {
                document.getElementById("id_embalaje").value = data.id_embalaje;
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

