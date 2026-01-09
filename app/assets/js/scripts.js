const URL = "http://192.168.19.4:3003";

// ✅ Cargar exportadoras
async function cargarExportadoras() {
    const res = await fetch(`${URL}/api/exportadoras`);
    const data = await res.json();
    const select = document.getElementById('exportadoraSelect');
    select.innerHTML = '<option value="">Seleccione</option>';
    data.forEach(exp => {
        select.innerHTML += `<option value="${exp.id}">${exp.Nombre_Exportadora}</option>`;
    });
}

// ✅ Al seleccionar exportadora → cargar instructivos
document.getElementById('exportadoraSelect').addEventListener('change', async (e) => {
    const idExp = e.target.value;
    const res = await fetch(`${URL}/api/instructivos/${idExp}`);
    const data = await res.json();
    const instructivoSelect = document.getElementById('instructivoSelect');
    instructivoSelect.innerHTML = '<option value="">Seleccione</option>';
    document.getElementById('versionSelect').innerHTML = '<option value="">Seleccione</option>';
    data.forEach(item => {
        instructivoSelect.innerHTML += `<option value="${item.id_instructivo}">${item.id_instructivo}</option>`;
    });
});

// ✅ Al seleccionar instructivo → cargar versiones
document.getElementById('instructivoSelect').addEventListener('change', async (e) => {
    const id = e.target.value;
    const res = await fetch(`${URL}/api/versiones/${id}`);
    const data = await res.json();
    const versionSelect = document.getElementById('versionSelect');
    versionSelect.innerHTML = '<option value="">Seleccione</option>';
    data.forEach(v => {
        versionSelect.innerHTML += `<option value="${v.version}">${v.version}</option>`;
    });
});

// ✅ Al seleccionar versión → mostrar detalle
document.getElementById('versionSelect').addEventListener('change', async () => {
    const id_instructivo = document.getElementById('instructivoSelect').value;
    const version = document.getElementById('versionSelect').value;
    const res = await fetch(`${URL}/api/detalle?id_instructivo=${id_instructivo}&version=${version}`);
    const data = await res.json();

    const contenedor = document.getElementById('tablaResultado');
    if (data.length === 0) {
        contenedor.innerHTML = '<p class="text-muted">No hay datos para mostrar.</p>';
        return;
    }

    // Crear encabezados de tabla a partir de las claves del primer objeto
    const headers = Object.keys(data[0]);
    let tablaHTML = `<table class="table table-bordered table-striped table-hover table-sm align-middle">
        <thead class="table-dark">
            <tr>${headers.map(h => `<th>${h}</th>`).join('')}</tr>
        </thead>
        <tbody>
            ${data.map(row => `
                <tr>
                    ${headers.map(h => `<td>${row[h] !== null ? row[h] : ''}</td>`).join('')}
                </tr>
            `).join('')}
        </tbody>
    </table>`;

    contenedor.innerHTML = tablaHTML;
});


// ✅ Ejecutar al cargar página
cargarExportadoras();
