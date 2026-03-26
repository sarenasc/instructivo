document.addEventListener("DOMContentLoaded", function () {
    cargarEmbalajes();
    cargarTabla();

    document.getElementById("btnGuardar").addEventListener("click", () => procesar("guardar"));
    document.getElementById("btnModificar").addEventListener("click", () => procesar("modificar"));
    document.getElementById("btnEliminar").addEventListener("click", eliminar);
    document.getElementById("btnLimpiar").addEventListener("click", limpiar);
});

function cargarEmbalajes() {
    fetch("../obtener_embalajes.php")
        .then(r => r.json())
        .then(data => {
            const select = document.getElementById("id_embalaje");
            select.innerHTML = '<option value="">Seleccione un embalaje</option>';
            data.forEach(item => {
                const option = document.createElement("option");
                option.value = item.id_embalaje;
                option.textContent = item.nombre_embalaje || item.codigo_embalaje;
                select.appendChild(option);
            });
        })
        .catch(e => console.error("Error cargando embalajes:", e));
}

function cargarTabla() {
    fetch("../obtener_altura_pallet.php")
        .then(r => r.json())
        .then(data => {
            const tbody = document.querySelector("#tablaAlturas tbody");
            tbody.innerHTML = "";
            
            if (!data.length) {
                tbody.innerHTML = '<tr><td colspan="4" class="text-center">No hay registros</td></tr>';
                return;
            }
            
            data.forEach(item => {
                const row = tbody.insertRow();
                row.innerHTML = `
                    <td>${item.id_altura_pallet}</td>
                    <td>${item.nombre_embalaje || 'N/A'}</td>
                    <td>${item.altura}</td>
                    <td>${item.cajas}</td>
                `;
            });
        })
        .catch(e => {
            console.error("Error cargando tabla:", e);
            const tbody = document.querySelector("#tablaAlturas tbody");
            tbody.innerHTML = '<tr><td colspan="4" class="text-center text-danger">Error cargando datos</td></tr>';
        });
}

function procesar(accion) {
    const fd = new FormData(document.getElementById("formAltura"));
    fd.append("accion", accion);
    
    fetch("../procesar_altura_pallet.php", {method: "POST", body: fd})
        .then(r => r.text())
        .then(d => {
            alert(d);
            if (d.includes("éxito") || d.includes("correctamente") || d.includes("Eliminado")) {
                limpiar();
                cargarTabla();
                cargarEmbalajes();
            }
        })
        .catch(e => console.error("Error:", e));
}

function eliminar() {
    const id = document.getElementById("id_altura_pallet").value;
    if (!id || !confirm("¿Eliminar este registro?")) return;
    
    const fd = new FormData();
    fd.append("accion", "eliminar");
    fd.append("id_altura_pallet", id);
    
    fetch("../procesar_altura_pallet.php", {method: "POST", body: fd})
        .then(r => r.text())
        .then(d => {
            alert(d);
            cargarTabla();
            cargarEmbalajes();
        })
        .catch(e => console.error("Error:", e));
}

function limpiar() {
    document.getElementById("formAltura").reset();
    document.getElementById("id_altura_pallet").value = "";
}
