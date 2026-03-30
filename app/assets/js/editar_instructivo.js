// Editar Instructivo - Crear Nueva Versión
// Sistema carga instructivo existente y permite crear versión 2, 3, 4, etc.

let instructivoActual = null;
let versionActual = 1;
let pedidosEdit = [];
let detalleEdit = [];
let idInstructivoSeleccionado = null;

// Colores para calibres
const coloresCalibres = [
    '#FF6B6B', '#4ECDC4', '#45B7D1', '#96CEB4',
    '#FFEAA7', '#DDA0DD', '#98D8C8', '#F7DC6F'
];

function obtenerColorCalibre(index) {
    return coloresCalibres[index % coloresCalibres.length];
}

// ===== CARGA INICIAL =====
document.addEventListener('DOMContentLoaded', function() {
    //console.log('🚀 Cargando página de editar instructivo...');
    cargarCombosIniciales();
});

async function cargarCombosIniciales() {
    try {
        // Exportadoras
        const respExp = await fetch('../services/api_exportadoras.php');
        const exportadoras = await respExp.json();
        const selectExp = document.getElementById('filtro_exportadora');
        const selectExpEdit = document.getElementById('edit_exportadora');
        exportadoras.forEach(exp => {
            const opt = document.createElement('option');
            opt.value = exp.id;
            opt.textContent = exp.nombre_exportadora;
            selectExp.appendChild(opt);
            const optEdit = opt.cloneNode(true);
            selectExpEdit.appendChild(optEdit);
        });
        
        // Especies
        const respEsp = await fetch('../services/api_especies.php');
        const especies = await respEsp.json();
        const selectEsp = document.getElementById('filtro_especie');
        const selectEspEdit = document.getElementById('edit_especie');
        especies.forEach(esp => {
            const opt = document.createElement('option');
            opt.value = esp.id_especie;
            opt.textContent = esp.especie;
            selectEsp.appendChild(opt);
            const optEdit = opt.cloneNode(true);
            selectEspEdit.appendChild(optEdit);
        });
        
        // Destinos
        const respDest = await fetch('../services/api_destino.php');
        const destinos = await respDest.json();
        const selectDest = document.getElementById('edit_destino');
        destinos.forEach(dest => {
            const opt = document.createElement('option');
            opt.value = dest.id;
            opt.textContent = dest.nombre_destino;
            selectDest.appendChild(opt);
        });
        
        // Listeners
        document.getElementById('edit_especie').addEventListener('change', cargarFiltrosPorEspecie);
        document.getElementById('edit_exportadora').addEventListener('change', cargarFiltrosPorExportadora);
        document.getElementById('edit_embalaje').addEventListener('change', cargarAlturaPallet);
        
    } catch (error) {
        console.error('❌ Error cargando combos:', error);
    }
}

// ===== BÚSQUEDA DE INSTRUCTIVOS =====
async function buscarInstructivos() {
    const exportadora = document.getElementById('filtro_exportadora').value;
    const especie = document.getElementById('filtro_especie').value;
    const desde = document.getElementById('filtro_desde').value;
    const hasta = document.getElementById('filtro_hasta').value;
    
    let url = '../models/obtener_instructivos.php?';
    if (exportadora) url += `id_exportadora=${exportadora}&`;
    if (especie) url += `id_especie=${especie}&`;
    if (desde) url += `desde=${desde}&`;
    if (hasta) url += `hasta=${hasta}&`;
    
    try {
        const resp = await fetch(url);
        const instructivos = await resp.json();
        
        if (instructivos.error) {
            alert('❌ ' + instructivos.error);
            return;
        }
        
        const tbody = document.getElementById('tabla_instructivos_body');
        tbody.innerHTML = '';
        
        if (instructivos.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" class="text-center">No se encontraron instructivos</td></tr>';
            document.getElementById('lista_instructivos').style.display = 'block';
            return;
        }
        
        // Obtener versiones por instructivo
        for (const inst of instructivos) {
            const row = tbody.insertRow();
            row.innerHTML = `
                <td><strong>${inst.id_instructivo}</strong></td>
                <td>${inst.nombre_exportadora}</td>
                <td>${inst.especie}</td>
                <td>${inst.fecha}</td>
                <td>${inst.turno || '-'}</td>
                <td><span class="badge bg-primary" id="badge_ver_${inst.id_instructivo}">Cargando...</span></td>
                <td>
                    <button class="btn btn-sm btn-primary" onclick="cargarInstructivo(${inst.id_instructivo})">
                        ✏️ Editar / Crear Versión
                    </button>
                </td>
            `;
            
            // Cargar versiones
            cargarVersionesBadge(inst.id_instructivo);
        }
        
        document.getElementById('lista_instructivos').style.display = 'block';
        document.getElementById('formulario_edicion').style.display = 'none';
        
    } catch (error) {
        console.error('❌ Error buscando instructivos:', error);
        alert('Error al buscar instructivos');
    }
}

async function cargarVersionesBadge(idInstructivo) {
    try {
        const resp = await fetch(`../models/obtener_versiones.php?id_instructivo=${idInstructivo}`);
        const versiones = await resp.json();
        const maxVersion = versiones.length > 0 ? Math.max(...versiones.map(v => v.version)) : 1;
        const badge = document.getElementById(`badge_ver_${idInstructivo}`);
        badge.textContent = `V${maxVersion}`;
        badge.className = 'badge bg-success version-badge';
    } catch (error) {
        console.error('Error cargando versiones:', error);
        document.getElementById(`badge_ver_${idInstructivo}`).textContent = '1';
    }
}

function limpiarFiltros() {
    document.getElementById('filtro_exportadora').value = '';
    document.getElementById('filtro_especie').value = '';
    document.getElementById('filtro_desde').value = '';
    document.getElementById('filtro_hasta').value = '';
}

// ===== CARGAR INSTRUCTIVO PARA EDICIÓN =====
async function cargarInstructivo(idInstructivo) {
    idInstructivoSeleccionado = idInstructivo;
    
    try {
        const resp = await fetch(`../models/obtener_instructivo_para_edicion.php?id_instructivo=${idInstructivo}`);
        const data = await resp.json();
        
        if (data.error) {
            alert('❌ ' + data.error);
            return;
        }
        
        instructivoActual = data;
        versionActual = data.version_actual;
        pedidosEdit = [...data.pedidos];
        detalleEdit = [...data.detalle];
        
        // Mostrar formulario
        document.getElementById('lista_instructivos').style.display = 'none';
        document.getElementById('formulario_edicion').style.display = 'block';
        
        // Llenar cabecera
        document.getElementById('edit_id_instructivo').textContent = idInstructivo;
        document.getElementById('edit_version').textContent = versionActual;
        document.getElementById('edit_version_actual').value = `Versión ${versionActual}`;
        document.getElementById('btn_nueva_version').textContent = versionActual + 1;
        
        document.getElementById('edit_exportadora').value = data.cabecera.id_exportadora;
        document.getElementById('edit_especie').value = data.cabecera.id_especie;
        document.getElementById('edit_fecha').value = data.cabecera.fecha;
        document.getElementById('edit_turno').value = data.cabecera.turno || 'Día';
        document.getElementById('edit_observacion').value = data.cabecera.observacion || '';
        
        // Cargar combos dependientes
        await cargarFiltrosPorEspecie();
        await cargarFiltrosPorExportadora();
        
        // Llenar pedidos
        renderPedidosEdit();
        actualizarSelectNumeroPedido();  // Inicializar select de número de pedido
        
        // Llenar detalle
        renderDetalleEdit();
        actualizarCintaCalibres();
        
        // Scroll al formulario
        document.getElementById('formulario_edicion').scrollIntoView({ behavior: 'smooth' });
        
    } catch (error) {
        console.error('❌ Error cargando instructivo:', error);
        alert('Error al cargar instructivo');
    }
}

// ===== CARGAR COMBOS DEPENDIENTES =====
async function cargarFiltrosPorEspecie() {
    const idEspecie = document.getElementById('edit_especie').value;
    const idExportadora = document.getElementById('edit_exportadora').value;
    
    if (!idEspecie) return;
    
    // Calibres
    const respCal = await fetch(`../models/obtener_calibres.php?id_especie=${idEspecie}`);
    const calibres = await respCal.json();
    const selectCal = document.getElementById('edit_calibres');
    selectCal.innerHTML = '';
    calibres.forEach(cal => {
        const opt = document.createElement('option');
        opt.value = cal.id;
        opt.textContent = cal.cod_calibre + ' - ' + cal.nombre_calibre;
        selectCal.appendChild(opt);
    });
    
    // Embalajes
    const respEmb = await fetch(`../models/obtener_embalajes.php?id_especie=${idEspecie}&id_exportadora=${idExportadora || ''}`);
    const embalajes = await respEmb.json();
    const selectEmb = document.getElementById('edit_embalaje');
    //console.log('📋 Embalaje cargados:', embalajes);
    selectEmb.innerHTML = '<option value="">Seleccione...</option>';
    
    embalajes.forEach(emb => {
        const opt = document.createElement('option');
        opt.value = emb.id;
        opt.textContent = emb.codigo_embalaje + ' - ' + emb.nombre_embalaje;
        opt.dataset.codigo = emb.codigo_embalaje || '';
        opt.dataset.nombre = emb.nombre_embalaje || '';
        selectEmb.appendChild(opt);
    });
    
    // PLU
    const respPlu = await fetch(`../models/obtener_plus.php?id_especie=${idEspecie}`);
    const plus = await respPlu.json();
    const selectPlu = document.getElementById('edit_plu');
    selectPlu.innerHTML = '<option value="">Seleccione...</option>';
    plus.forEach(plu => {
        const opt = document.createElement('option');
        opt.value = plu.id;
        opt.textContent = plu.codigo_plu + ' - ' + plu.nombre_plu;
        selectPlu.appendChild(opt);
    });
}

async function cargarFiltrosPorExportadora() {
    const idExportadora = document.getElementById('edit_exportadora').value;
    const idEspecie = document.getElementById('edit_especie').value;
    
    if (!idExportadora) return;
    
    // Categoría
    const respCat = await fetch(`../models/obtener_categoria.php?id_especie=${idEspecie || ''}&id_exportadora=${idExportadora || ''}`);
    const categorias = await respCat.json();
   
    const selectCat = document.getElementById('edit_categoria');
    selectCat.innerHTML = '<option value="">Seleccione...</option>';
    categorias.forEach(cat => {
        const opt = document.createElement('option');
        opt.value = cat.id;
        const texto = cat.nombre_categoria || cat.codigo_categoria || 'Sin nombre';
        opt.textContent = cat.codigo_categoria + ' - ' + texto;
        selectCat.appendChild(opt);
    });
    
    // Etiqueta
    const respEtq = await fetch(`../models/obtener_etiquetas.php?id_exportadora=${idExportadora}`);
    const etiquetas = await respEtq.json();
    const selectEtq = document.getElementById('edit_etiqueta');
    selectEtq.innerHTML = '<option value="">Seleccione...</option>';
    etiquetas.forEach(etq => {
        const opt = document.createElement('option');
        opt.value = etq.id;
        opt.textContent = etq.codigo_etiqueta + ' - ' + etq.nombre_etiqueta;
        selectEtq.appendChild(opt);
    });
    
    // Pallet
    const respPal = await fetch(`../models/obtener_pallets.php?id_exportadora=${idExportadora}`);
    const pallets = await respPal.json();
    const selectPal = document.getElementById('edit_pallet');
    selectPal.innerHTML = '<option value="">Seleccione...</option>';
    pallets.forEach(pal => {
        const opt = document.createElement('option');
        opt.value = pal.id;
        opt.textContent = pal.cod_pallet + ' - ' + pal.describ_pallet;
        selectPal.appendChild(opt);
    });
}

async function cargarAlturaPallet() {
    const idEmbalaje = document.getElementById('edit_embalaje').value;
    
    if (!idEmbalaje) {
        document.getElementById('edit_altura_pallet').innerHTML = '<option value="">Seleccione...</option>';
        return;
    }
    
    const resp = await fetch(`../models/obtener_altura_pallet.php?id_embalaje=${idEmbalaje}`);
    const alturas = await resp.json();
    const selectAlt = document.getElementById('edit_altura_pallet');
    selectAlt.innerHTML = '<option value="">Seleccione...</option>';
    alturas.forEach(alt => {
        const opt = document.createElement('option');
        opt.value = alt.id;
        opt.textContent = alt.altura + ' cm - ' + alt.cajas + ' cajas';
        selectAlt.appendChild(opt);
    });
}

async function cargarAlturaPalletEnModal(idEmbalajeSeleccionado, idAlturaSeleccionada) {
    if (!idEmbalajeSeleccionado) {
        document.getElementById('edit_detalle_altura').innerHTML = '<option value="">Seleccione...</option>';
        return;
    }
    
    const resp = await fetch(`../models/obtener_altura_pallet.php?id_embalaje=${idEmbalajeSeleccionado}`);
    const alturas = await resp.json();
    //console.log('📏 Alturas cargadas para embalaje', idEmbalajeSeleccionado, ':', alturas);
    
    const selectAlt = document.getElementById('edit_detalle_altura');
    selectAlt.innerHTML = '<option value="">Seleccione...</option>';
    alturas.forEach(alt => {
        const opt = document.createElement('option');
        opt.value = alt.id;
        opt.textContent = alt.altura + ' cm - ' + alt.cajas + ' cajas';
        selectAlt.appendChild(opt);
    });
    
    // Seleccionar la altura guardada (inmediatamente después de cargar las opciones)
    if (idAlturaSeleccionada) {
        selectAlt.value = idAlturaSeleccionada;
        //console.log('📏 Altura seleccionada:', idAlturaSeleccionada, 'Valor actual:', selectAlt.value);
    }
}

// ===== PEDIDOS =====
function agregarPedido() {
    const numero = document.getElementById('edit_numero_pedido').value;
    const cantidad = document.getElementById('edit_cantidad_pedido').value;
    const prioridad = document.getElementById('edit_prioridad_pedido').value;
    
    if (!numero || !cantidad) {
        alert('⚠️ Complete número y cantidad del pedido');
        return;
    }
    
    pedidosEdit.push({
        numero_pedido: numero,
        cantidad: parseInt(cantidad),
        prioridad: parseInt(prioridad)
    });
    
    document.getElementById('edit_numero_pedido').value = '';
    document.getElementById('edit_cantidad_pedido').value = '';
    
    renderPedidosEdit();
    actualizarSelectNumeroPedido();  // Actualizar selects cuando se agrega pedido
}

function renderPedidosEdit() {
    const tbody = document.querySelector('#tabla_pedidos_edit tbody');
    tbody.innerHTML = '';
    
    pedidosEdit.forEach((ped, index) => {
        const row = tbody.insertRow();
        row.innerHTML = `
            <td><strong>${ped.numero_pedido}</strong></td>
            <td>${ped.cantidad}</td>
            <td>${ped.prioridad}</td>
            <td>
                <button class="btn btn-sm btn-warning me-1" onclick="abrirModalEditarPedido(${index})">✏️ Editar</button>
                <button class="btn btn-sm btn-danger" onclick="eliminarPedido(${index})">🗑️ Eliminar</button>
            </td>
        `;
    });
}

function eliminarPedido(index) {
    pedidosEdit.splice(index, 1);
    renderPedidosEdit();
    actualizarSelectNumeroPedido();  // Actualizar selects cuando se elimina pedido
}

// ===== ACTUALIZAR SELECT NÚMERO DE PEDIDO =====
function actualizarSelectNumeroPedido() {
    // Actualizar select del formulario de agregar detalle
    const selectAgregar = document.getElementById('edit_numero_pedido_detalle');
    if (selectAgregar) {
        const valorActual = selectAgregar.value;
        selectAgregar.innerHTML = '<option value="">Seleccione...</option>';
        
        const pedidosUnicos = [...new Set(pedidosEdit.map(p => p.numero_pedido))];
        pedidosUnicos.sort();
        
        pedidosUnicos.forEach(numero => {
            const opt = document.createElement('option');
            opt.value = numero;
            opt.textContent = numero;
            selectAgregar.appendChild(opt);
        });
        
        // Mantener selección si todavía existe
        if (pedidosUnicos.includes(valorActual)) {
            selectAgregar.value = valorActual;
        }
    }
}

// ===== DETALLE =====
function agregarDetalle() {
    const calibresSelect = document.getElementById('edit_calibres');
    const calibresSeleccionados = Array.from(calibresSelect.selectedOptions).map(opt => ({
        id: parseInt(opt.value),
        texto: opt.textContent
    }));
    
    // Leer desde SELECT (no INPUT)
    const numeroPedido = document.getElementById('edit_numero_pedido_detalle').value;
    const cantidad = document.getElementById('edit_cantidad_detalle').value;
    
    if (calibresSeleccionados.length === 0) {
        alert('⚠️ Seleccione al menos un calibre');
        return;
    }
    
    if (!numeroPedido || !cantidad) {
        alert('⚠️ Complete número de pedido y cantidad');
        return;
    }
    
    const embalajeSelect = document.getElementById('edit_embalaje');
    const categoriaSelect = document.getElementById('edit_categoria');
    const pluSelect = document.getElementById('edit_plu');
    const etiquetaSelect = document.getElementById('edit_etiqueta');
    const palletSelect = document.getElementById('edit_pallet');
    const alturaSelect = document.getElementById('edit_altura_pallet');
    const destinoSelect = document.getElementById('edit_destino');
    
    detalleEdit.push({
        numero_pedido: numeroPedido,
        cantidad: parseInt(cantidad),
        calibres: calibresSeleccionados,
        id_embalaje: parseInt(embalajeSelect.value) || null,
        embalaje_codigo: embalajeSelect.options[embalajeSelect.selectedIndex]?.dataset.codigo || '',
        embalaje_nombre: embalajeSelect.options[embalajeSelect.selectedIndex]?.dataset.nombre || '',
        embalaje_text: embalajeSelect.options[embalajeSelect.selectedIndex]?.text || '',      
        id_categoria: parseInt(categoriaSelect.value) || null,
        categoria_text: categoriaSelect.options[categoriaSelect.selectedIndex]?.text || '',
        id_plu: parseInt(pluSelect.value) || null,
        plu_text: pluSelect.options[pluSelect.selectedIndex]?.text || '',
        id_etiqueta: parseInt(etiquetaSelect.value) || null,
        etiqueta_text: etiquetaSelect.options[etiquetaSelect.selectedIndex]?.text || '',
        id_pallet: parseInt(palletSelect.value) || null,
        pallet_text: palletSelect.options[palletSelect.selectedIndex]?.text || '',
        altura_pallet: parseInt(alturaSelect.value) || null,
        altura_text: alturaSelect.options[alturaSelect.selectedIndex]?.text || '',
        id_destino: parseInt(destinoSelect.value) || null,
        destino_text: destinoSelect.options[destinoSelect.selectedIndex]?.text || '',
        variedad_etiquetada: document.getElementById('edit_variedad_etiqueta').value,
        observacion: document.getElementById('edit_observacion_detalle').value
    });
    
    // Limpiar campos
    document.getElementById('edit_numero_pedido_detalle').value = '';
    document.getElementById('edit_cantidad_detalle').value = '';
    document.getElementById('edit_variedad_etiqueta').value = '';
    document.getElementById('edit_observacion_detalle').value = '';
    calibresSelect.selectedIndex = -1;
    
    renderDetalleEdit();
    actualizarCintaCalibres();
}

function renderDetalleEdit() {
    const tbody = document.querySelector('#tabla_detalle_edit tbody');
    tbody.innerHTML = '';
    
    detalleEdit.forEach((det, index) => {
        const row = tbody.insertRow();
        
        // Debug: Verificar categoría
        ////console.log(`📋 Detalle ${index}: categoria_text="${det.embalaje_codigo}", id_categoria=${det.id_categoria}`);
        
        let calibresHTML = '<div style="display: flex; gap: 4px; flex-wrap: wrap;">';
        det.calibres.forEach((calibre, i) => {
            const color = obtenerColorCalibre(i);
            calibresHTML += `<span class="badge" style="background: ${color}; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;">${calibre.texto}</span>`;
        });
        calibresHTML += '</div>';
        
        row.innerHTML = `
            <td><strong>${det.numero_pedido}</strong></td>
            <td>${det.variedad_etiquetada || '-'}</td>
            <td>${det.embalaje_text}</td>
            <td>${det.etiqueta_text}</td>
            <td>${calibresHTML}</td>
            <td>${det.categoria_text || 'Sin categoría'}</td>
            <td>${det.plu_text}</td>
            <td>${det.destino_text || '-'}</td>
            <td>${det.pallet_text || '-'}</td>
            <td>${det.cantidad}</td>
            <td>${det.altura_text || '-'}</td>
            <td>${det.observacion || '-'}</td>
            <td>
                <button class="btn btn-sm btn-warning me-1" onclick="abrirModalEditarDetalle(${index})">✏️</button>
                <button class="btn btn-sm btn-danger" onclick="eliminarDetalle(${index})">🗑️</button>
            </td>
        `;
    });
}

function eliminarDetalle(index) {
    detalleEdit.splice(index, 1);
    renderDetalleEdit();
    actualizarCintaCalibres();
}

function actualizarCintaCalibres() {
    const cinta = document.getElementById('cinta_calibres_edit');
    cinta.innerHTML = '';
    
    // Obtener todos los calibres únicos
    const calibresUnicos = new Map();
    detalleEdit.forEach(det => {
        det.calibres.forEach(cal => {
            if (!calibresUnicos.has(cal.id)) {
                calibresUnicos.set(cal.id, cal.texto);
            }
        });
    });
    
    if (calibresUnicos.size === 0) {
        cinta.style.display = 'none';
        return;
    }
    
    cinta.style.display = 'flex';
    let index = 0;
    calibresUnicos.forEach((texto, id) => {
        const color = obtenerColorCalibre(index++);
        const badge = document.createElement('span');
        badge.className = 'badge badge-calibre';
        badge.style.background = color;
        badge.textContent = texto;
        cinta.appendChild(badge);
    });
}

// ===== GUARDAR NUEVA VERSIÓN =====
async function guardarNuevaVersion() {
    // Validaciones
    if (!document.getElementById('edit_exportadora').value) {
        alert('⚠️ Seleccione exportadora');
        return;
    }
    if (!document.getElementById('edit_especie').value) {
        alert('⚠️ Seleccione especie');
        return;
    }
    if (pedidosEdit.length === 0) {
        alert('⚠️ Agregue al menos un pedido');
        return;
    }
    if (detalleEdit.length === 0) {
        alert('⚠️ Agregue al menos un detalle');
        return;
    }
    
    const nuevaVersion = versionActual + 1;
    
    if (!confirm(`¿Crear Versión ${nuevaVersion} del Instructivo ${idInstructivoSeleccionado}?`)) {
        return;
    }
    
    const datos = {
        id_instructivo: idInstructivoSeleccionado,
        version_anterior: versionActual,
        cabecera: {
            id_exportadora: parseInt(document.getElementById('edit_exportadora').value),
            id_especie: parseInt(document.getElementById('edit_especie').value),
            fecha: document.getElementById('edit_fecha').value,
            turno: document.getElementById('edit_turno').value,
            observacion: document.getElementById('edit_observacion').value
        },
        pedidos: pedidosEdit,
        detalle: detalleEdit
    };
    
    try {
        const resp = await fetch('../controllers/guardar_nueva_version.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(datos)
        });
        
        const resultado = await resp.json();
        
        if (resultado.error) {
            alert('❌ ' + resultado.error);
            return;
        }
        
        alert('✅ ' + resultado.message);
        
        // Resetear
        pedidosEdit = [];
        detalleEdit = [];
        document.getElementById('formulario_edicion').style.display = 'none';
        document.getElementById('lista_instructivos').style.display = 'block';
        
        // Recargar lista
        buscarInstructivos();
        
    } catch (error) {
        console.error('❌ Error guardando:', error);
        alert('Error al guardar la nueva versión');
    }
}

function cancelarEdicion() {
    if (confirm('¿Cancelar edición? Los cambios no guardados se perderán.')) {
        pedidosEdit = [];
        detalleEdit = [];
        document.getElementById('formulario_edicion').style.display = 'none';
        document.getElementById('lista_instructivos').style.display = 'block';
    }
}

// ===== EDITAR PEDIDO =====
function abrirModalEditarPedido(index) {
    const pedido = pedidosEdit[index];
    document.getElementById('edit_pedido_index').value = index;
    document.getElementById('edit_pedido_numero').value = pedido.numero_pedido;
    document.getElementById('edit_pedido_cantidad').value = pedido.cantidad;
    document.getElementById('edit_pedido_prioridad').value = pedido.prioridad;
    
    const modal = new bootstrap.Modal(document.getElementById('modalEditarPedido'));
    modal.show();
}

function guardarEdicionPedido() {
    const index = parseInt(document.getElementById('edit_pedido_index').value);
    const numero = document.getElementById('edit_pedido_numero').value;
    const cantidad = document.getElementById('edit_pedido_cantidad').value;
    const prioridad = document.getElementById('edit_pedido_prioridad').value;
    
    if (!numero || !cantidad || !prioridad) {
        alert('⚠️ Complete todos los campos');
        return;
    }
    
    pedidosEdit[index] = {
        numero_pedido: numero,
        cantidad: parseInt(cantidad),
        prioridad: parseInt(prioridad)
    };
    
    bootstrap.Modal.getInstance(document.getElementById('modalEditarPedido')).hide();
    renderPedidosEdit();
}

// ===== EDITAR DETALLE =====
async function abrirModalEditarDetalle(index) {
    const det = detalleEdit[index];
    
    //console.log('🔍 Editando detalle index', index, ':', det);
    //console.log('📦 id_embalaje:', det.id_embalaje);
    //console.log('📏 altura_pallet:', det.altura_pallet);
    //console.log('📋 id_categoria:', det.id_categoria, 'categoria_text:', det.categoria_text);
    //console.log('🏷️ id_etiqueta:', det.id_etiqueta, 'etiqueta_text:', det.etiqueta_text);
    //console.log('🏷️ id_plu:', det.id_plu, 'plu_text:', det.plu_text);
    //console.log('📦 id_pallet:', det.id_pallet, 'pallet_text:', det.pallet_text);
    
    document.getElementById('edit_detalle_index').value = index;
    document.getElementById('edit_detalle_cantidad').value = det.cantidad;
    document.getElementById('edit_detalle_variedad').value = det.variedad_etiquetada || '';
    document.getElementById('edit_detalle_observacion').value = det.observacion || '';
    
    // Cargar combos si están vacíos
    if (document.getElementById('edit_detalle_embalaje').options.length <= 1) {
        //console.log('🔄 Cargando combos...');
        await cargarCombosEdicionDetalle();
    }
    
    // Cargar números de pedido disponibles en el select
    await cargarPedidosEnSelect();
    
    // Seleccionar valores - PRIMERO cargar embalaje, luego alturas
    setTimeout(async () => {
        // Número de pedido
        const selectPedido = document.getElementById('edit_detalle_numero_pedido');
        const numeroPedidoActual = String(det.numero_pedido ?? '').trim();

        if (numeroPedidoActual) {
            const optionExistente = Array.from(selectPedido.options).find(
                opt => String(opt.value).trim() === numeroPedidoActual);
                //console.log('🔢 Número pedido seleccionado:', numeroPedidoActual, 'valor final:', selectPedido.value);

            if (optionExistente) {
                selectPedido.value = numeroPedidoActual;
                //console.log('🔢 Número pedido seleccionado:', numeroPedidoActual);
            } else {
                //console.log('⚠️ No se encontró opción para número de pedido:', numeroPedidoActual);
            }
        }
        
        // Embalaje
        if (det.id_embalaje) {
            const embalajeSelect = document.getElementById('edit_detalle_embalaje');
            embalajeSelect.value = det.id_embalaje;
            //console.log('📦 Embalaje seleccionado:', det.id_embalaje, 'Texto:', embalajeSelect.options[embalajeSelect.selectedIndex]?.text);
        }
        
        // Cargar alturas DESPUÉS de seleccionar embalaje
        if (det.id_embalaje) {
            //console.log('📏 Cargando alturas para embalaje', det.id_embalaje);
            await cargarAlturaPalletEnModal(det.id_embalaje, det.altura_pallet);
        }
        
        // Seleccionar los demás campos
        if (det.id_categoria) {
            const catSelect = document.getElementById('edit_detalle_categoria');
            catSelect.value = det.id_categoria;
            //console.log('📋 Categoría seleccionada:', det.id_categoria, 'Texto:', catSelect.options[catSelect.selectedIndex]?.text);
        }
        if (det.id_plu) {
            const pluSelect = document.getElementById('edit_detalle_plu');
            pluSelect.value = det.id_plu;
            //console.log('🏷️ PLU seleccionado:', det.id_plu, 'Texto:', pluSelect.options[pluSelect.selectedIndex]?.text);
        }
        if (det.id_etiqueta) {
            const etqSelect = document.getElementById('edit_detalle_etiqueta');
            etqSelect.value = det.id_etiqueta;
            //console.log('🏷️ Etiqueta seleccionada:', det.id_etiqueta, 'Texto:', etqSelect.options[etqSelect.selectedIndex]?.text);
        }
        if (det.id_pallet) {
            const palSelect = document.getElementById('edit_detalle_pallet');
            palSelect.value = det.id_pallet;
            //console.log('📦 Pallet seleccionado:', det.id_pallet, 'Texto:', palSelect.options[palSelect.selectedIndex]?.text);
        }
        if (det.id_destino) {
            const destSelect = document.getElementById('edit_detalle_destino');
            destSelect.value = det.id_destino;
            //console.log('📍 Destino seleccionado:', det.id_destino, 'Texto:', destSelect.options[destSelect.selectedIndex]?.text);
        }
        
        // Seleccionar calibres
        const calibresSelect = document.getElementById('edit_detalle_calibres');
        Array.from(calibresSelect.options).forEach(opt => {
            opt.selected = det.calibres.some(c => c.id == opt.value);
        });
        //console.log('🎯 Calibres seleccionados:', det.calibres);
    }, 150);
    
    const modal = new bootstrap.Modal(document.getElementById('modalEditarDetalle'));
    modal.show();
}

async function cargarPedidosEnSelect() {
    const selectPedido = document.getElementById('edit_detalle_numero_pedido');
    selectPedido.innerHTML = '<option value="">Seleccione...</option>';
    
    // Obtener pedidos únicos de la lista de pedidos
    const pedidosUnicos = [...new Set(pedidosEdit.map(p => p.numero_pedido))];
    pedidosUnicos.sort();
    
    pedidosUnicos.forEach(numero => {
        const opt = document.createElement('option');
        opt.value = String(numero).trim();
        opt.textContent = String(numero).trim();
        selectPedido.appendChild(opt);
    });
}

async function cargarCombosEdicionDetalle() {
    const idEspecie = document.getElementById('edit_especie').value;
    const idExportadora = document.getElementById('edit_exportadora').value;
    
    // Embalaje
    const respEmb = await fetch(`../models/obtener_embalajes.php?id_especie=${idEspecie || ''}&id_exportadora=${idExportadora || ''}`);
    const embalajes = await respEmb.json();
     
    const selectEmb = document.getElementById('edit_detalle_embalaje');
    selectEmb.innerHTML = '<option value="">Seleccione...</option>';
    embalajes.forEach(emb => {
        const opt = document.createElement('option');
        opt.value = emb.id;
        opt.textContent = emb.codigo_embalaje + ' - ' + emb.nombre_embalaje;
        opt.dataset.codigo = emb.codigo_embalaje || '';
        opt.dataset.nombre = emb.nombre_embalaje || '';
        selectEmb.appendChild(opt);
    });
    
    // Categoría
    const respCat = await fetch(`../models/obtener_categoria.php?id_especie=${idEspecie || ''}&id_exportadora=${idExportadora || ''}`);
    const categorias = await respCat.json();
    //console.log('📋 Categorías cargadas:', categorias);
    const selectCat = document.getElementById('edit_detalle_categoria');
    selectCat.innerHTML = '<option value="">Seleccione...</option>';
    categorias.forEach(cat => {
        const opt = document.createElement('option');
        opt.value = cat.id;
        // Usar nombre_categoria o cod_categoria como fallback
        const texto = cat.nombre_categoria || cat.codigo_categoria || 'Sin nombre';
        opt.textContent = cat.codigo_categoria + ' - ' + texto;
        selectCat.appendChild(opt);
    });
    
    // PLU
    const respPlu = await fetch(`../models/obtener_plus.php?id_especie=${idEspecie || ''}`);
    const plus = await respPlu.json();
    const selectPlu = document.getElementById('edit_detalle_plu');
    selectPlu.innerHTML = '<option value="">Seleccione...</option>';
    plus.forEach(plu => {
        const opt = document.createElement('option');
        opt.value = plu.id;
        opt.textContent = plu.codigo_plu + ' - ' + plu.nombre_plu;
        selectPlu.appendChild(opt);
    });
    
    // Etiqueta
    const respEtq = await fetch(`../models/obtener_etiquetas.php?id_exportadora=${idExportadora || ''}`);
    const etiquetas = await respEtq.json();
    const selectEtq = document.getElementById('edit_detalle_etiqueta');
    selectEtq.innerHTML = '<option value="">Seleccione...</option>';
    etiquetas.forEach(etq => {
        const opt = document.createElement('option');
        opt.value = etq.id;
        opt.textContent = etq.codigo_etiqueta + ' - ' + etq.nombre_etiqueta;
        selectEtq.appendChild(opt);
    });
    
    // Pallet
    const respPal = await fetch(`../models/obtener_pallets.php?id_exportadora=${idExportadora || ''}`);
    const pallets = await respPal.json();
    const selectPal = document.getElementById('edit_detalle_pallet');
    selectPal.innerHTML = '<option value="">Seleccione...</option>';
    pallets.forEach(pal => {
        const opt = document.createElement('option');
        opt.value = pal.id;
        opt.textContent = pal.cod_pallet + ' - ' + pal.describ_pallet;
        selectPal.appendChild(opt);
    });
    
    // Destino
    const respDest = await fetch(`../services/api_destino.php`);
    const destinos = await respDest.json();
    const selectDest = document.getElementById('edit_detalle_destino');
    selectDest.innerHTML = '<option value="">Seleccione...</option>';
    destinos.forEach(dest => {
        const opt = document.createElement('option');
        opt.value = dest.id;
        opt.textContent = dest.nombre_destino;
        selectDest.appendChild(opt);
    });
    
    // Calibres
    const respCal = await fetch(`../models/obtener_calibres.php?id_especie=${idEspecie || ''}`);
    const calibres = await respCal.json();
    const selectCal = document.getElementById('edit_detalle_calibres');
    selectCal.innerHTML = '';
    calibres.forEach(cal => {
        const opt = document.createElement('option');
        opt.value = cal.id;
        opt.textContent = cal.cod_calibre + ' - ' + cal.nombre_calibre;
        selectCal.appendChild(opt);
    });
}

function guardarEdicionDetalle() {
    const index = parseInt(document.getElementById('edit_detalle_index').value);
    const calibresSelect = document.getElementById('edit_detalle_calibres');
    const calibresSeleccionados = Array.from(calibresSelect.selectedOptions).map(opt => ({
        id: parseInt(opt.value),
        texto: opt.textContent
    }));
    
    const embalajeSelect = document.getElementById('edit_detalle_embalaje');
    const categoriaSelect = document.getElementById('edit_detalle_categoria');
    const pluSelect = document.getElementById('edit_detalle_plu');
    const etiquetaSelect = document.getElementById('edit_detalle_etiqueta');
    const palletSelect = document.getElementById('edit_detalle_pallet');
    const alturaSelect = document.getElementById('edit_detalle_altura');
    const destinoSelect = document.getElementById('edit_detalle_destino');
    const pedidoSelect = document.getElementById('edit_detalle_numero_pedido');
    
    detalleEdit[index] = {
        numero_pedido: pedidoSelect.value,
        cantidad: parseInt(document.getElementById('edit_detalle_cantidad').value),
        calibres: calibresSeleccionados,
        id_embalaje: parseInt(embalajeSelect.value) || null,
        embalaje_codigo: embalajeSelect.options[embalajeSelect.selectedIndex]?.dataset.codigo || '',
        embalaje_nombre: embalajeSelect.options[embalajeSelect.selectedIndex]?.dataset.nombre || '',
        embalaje_text: embalajeSelect.options[embalajeSelect.selectedIndex]?.text || '',
        id_categoria: parseInt(categoriaSelect.value) || null,
        categoria_text: categoriaSelect.options[categoriaSelect.selectedIndex]?.text || '',
        id_plu: parseInt(pluSelect.value) || null,
        plu_text: pluSelect.options[pluSelect.selectedIndex]?.text || '',
        id_etiqueta: parseInt(etiquetaSelect.value) || null,
        etiqueta_text: etiquetaSelect.options[etiquetaSelect.selectedIndex]?.text || '',
        id_pallet: parseInt(palletSelect.value) || null,
        pallet_text: palletSelect.options[palletSelect.selectedIndex]?.text || '',
        altura_pallet: parseInt(alturaSelect.value) || null,
        altura_text: alturaSelect.options[alturaSelect.selectedIndex]?.text || '',
        id_destino: parseInt(destinoSelect.value) || null,
        destino_text: destinoSelect.options[destinoSelect.selectedIndex]?.text || '',
        variedad_etiquetada: document.getElementById('edit_detalle_variedad').value,
        observacion: document.getElementById('edit_detalle_observacion').value
    };
    
    bootstrap.Modal.getInstance(document.getElementById('modalEditarDetalle')).hide();
    renderDetalleEdit();
    actualizarCintaCalibres();
}

// ===== MODAL PANTALLA COMPLETA =====
function abrirModalPantallaCompleta() {
    if (detalleEdit.length === 0) {
        alert('⚠️ No hay detalle para mostrar');
        return;
    }
    
    // Cinta de calibres
    const cintaContent = document.getElementById('cintaCalibresModalContentEdit');
    cintaContent.innerHTML = '';
    const calibresUnicos = new Map();
    detalleEdit.forEach(det => {
        det.calibres.forEach(cal => {
            if (!calibresUnicos.has(cal.id)) {
                calibresUnicos.set(cal.id, cal.texto);
            }
        });
    });
    
    let index = 0;
    calibresUnicos.forEach((texto) => {
        const color = obtenerColorCalibre(index++);
        const badge = document.createElement('span');
        badge.className = 'badge';
        badge.style.background = color;
        badge.style.padding = '8px 14px';
        badge.style.borderRadius = '20px';
        badge.style.fontSize = '13px';
        badge.style.fontWeight = '600';
        badge.textContent = texto;
        cintaContent.appendChild(badge);
    });
    
    // Estadísticas
    const pedidosUnicos = new Set(detalleEdit.map(d => d.numero_pedido));
    const todosCalibres = new Set();
    detalleEdit.forEach(d => d.calibres.forEach(c => todosCalibres.add(c.id)));
    const totalCajas = detalleEdit.reduce((sum, d) => sum + d.cantidad, 0);
    const destinosUnicos = new Set(detalleEdit.filter(d => d.id_destino).map(d => d.id_destino));
    
    document.getElementById('statPedidosEdit').textContent = pedidosUnicos.size;
    document.getElementById('statCalibresEdit').textContent = todosCalibres.size;
    document.getElementById('statCajasEdit').textContent = totalCajas;
    document.getElementById('statDestinosEdit').textContent = destinosUnicos.size;
    
    // Tabla
    const tbody = document.querySelector('#tablaDetalleModalEdit tbody');
    tbody.innerHTML = '';
    
    detalleEdit.forEach(det => {
        const row = tbody.insertRow();
        
        let calibresHTML = '<div style="display: flex; gap: 4px; flex-wrap: wrap;">';
        det.calibres.forEach((calibre, i) => {
            const color = obtenerColorCalibre(i);
            calibresHTML += `<span class="badge" style="background: ${color}; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;">${calibre.texto}</span>`;
        });
        calibresHTML += '</div>';
        
        row.innerHTML = `
            <td><strong>${det.numero_pedido}</strong></td>
            <td>${det.variedad_etiquetada || '-'}</td>
            <td>${det.embalaje_codigo ? `${det.embalaje_codigo} - ${det.embalaje_nombre}` : (det.embalaje_text || '-')}</td>
            <td>${det.etiqueta_text}</td>
            <td>${calibresHTML}</td>
            <td>${det.categoria_text}</td>
            <td>${det.plu_text}</td>
            <td>${det.destino_text || '-'}</td>
            <td>${det.pallet_text || '-'}</td>
            <td>${det.cantidad}</td>
            <td>${det.altura_text || '-'}</td>
            <td>${det.observacion || '-'}</td>
        `;
    });
    
    const modal = new bootstrap.Modal(document.getElementById('modalPantallaCompletaEdit'));
    modal.show();
}
