document.addEventListener("DOMContentLoaded", () => {
    const btnGuardar = document.getElementById("btnGuardar");
    const btnModificar = document.getElementById("btnModificar");
    const btnEliminar = document.getElementById("btnEliminar");
    const tabla = document.querySelector("#tablaAlturas tbody");
    cargarEmbalajes();
    const idEmbalaje = document.getElementById("id_embalaje");
    const altura = document.getElementById("altura");
    const cajas = document.getElementById("cajas");
    let idSeleccionado = null;

    // Cargar select de embalajes
    function cargarEmbalajes() {
    fetch("../controllers/listar_embalaje.php")
        .then(response => response.json())
        .then(data => {
            const select = document.getElementById("id_embalaje");
            select.innerHTML = '<option value="">Seleccione un embalaje</option>';
            data.forEach(emb => {
                const option = document.createElement("option");
                option.value = emb.id; // AsegÃºrate de usar el ID real
                option.textContent = `${emb.id} - ${emb.embalaje}`;
                select.appendChild(option);
            });
        })
        .catch(error => console.error("Error cargando embalajes:", error));
}

function limpiarFormulario() {
    document.getElementById("id_embalaje").value = "";
    document.getElementById("altura").value = "";
    document.getElementById("cajas").value = "";
    idSeleccionado = null; // Resetear la variable global si estÃ¡s usando una
}


    // FunciÃ³n para cargar tabla
    function cargarTabla() {
        fetch("../controllers/listar_altura_pallet.php")
            .then(res => res.json())
            .then(data => {
                tabla.innerHTML = data.map(row => `
                    <tr data-id="${row.id}">
                        <td>${row.id}</td>
                        <td>${row.embalaje}</td>
                        <td>${row.altura}</td>
                        <td>${row.cajas}</td>
                    </tr>
                `).join("");

                document.querySelectorAll("#tablaAlturas tbody tr").forEach(tr => {
                    tr.addEventListener("click", () => {
                        document.querySelectorAll("#tablaAlturas tbody tr").forEach(row => row.classList.remove("table-primary"));
                        tr.classList.add("table-primary");
                        const celdas = tr.children;
                        idSeleccionado = tr.dataset.id;
                        idEmbalaje.value = data.find(d => d.id == idSeleccionado).id_embalaje;
                        altura.value = celdas[2].textContent;
                        cajas.value = celdas[3].textContent;
                    });
                });
            });
    }

    cargarTabla();

    // Guardar
    btnGuardar.addEventListener("click", () => {
    const id = idSeleccionado || null;
    const id_embalaje = idEmbalaje.value;
    const alturaValor = altura.value;
    const cajasValor = cajas.value;

    // Validar que los valores no sean vacÃ­os si es necesario
    console.log("Datos a enviar:", { id, id_embalaje, alturaValor, cajasValor });

    fetch("../controllers/guardar_altura_pallet.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            id: id,
            id_embalaje: id_embalaje,
            altura: alturaValor,
            cajas: cajasValor
        })
    })
    .then(response => response.json())
    .then(data => {
        alert(data.mensaje);
        cargarTabla();
        limpiarFormulario();
    })
    .catch(error => console.error("Error al guardar:", error));
});

    // Modificar
    btnModificar.addEventListener("click", () => {
        if (!idSeleccionado) return alert("Seleccione una fila para modificar.");

        const datos = new FormData();
        datos.append("id", idSeleccionado);
        datos.append("id_embalaje", idEmbalaje.value);
        datos.append("altura", altura.value);
        datos.append("cajas", cajas.value);

        fetch("../altura_pallet_modificar.php", {
            method: "POST",
            body: datos
        })
            .then(res => res.text())
            .then(() => {
                cargarTabla();
                document.getElementById("formAltura").reset();
                idSeleccionado = null;
            });
    });

    // Eliminar
    btnEliminar.addEventListener("click", () => {
        if (!idSeleccionado) return alert("Seleccione una fila para eliminar.");

        const datos = new FormData();
        datos.append("id", idSeleccionado);

        fetch("../altura_pallet_eliminar.php", {
            method: "POST",
            body: datos
        })
            .then(res => res.text())
            .then(() => {
                cargarTabla();
                document.getElementById("formAltura").reset();
                idSeleccionado = null;
            });
    });
});

