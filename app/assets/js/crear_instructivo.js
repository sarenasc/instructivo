// Variables globales
let pedidosAgregados = [];
let detalleAgregado = []; // Ahora agrupa por pedido, no por calibre

// Colores para calibres
const coloresCalibres = [
    'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
    'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)',
    'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)',
    'linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)',
    'linear-gradient(135deg, #fa709a 0%, #fee140 100%)',
    'linear-gradient(135deg, #30cfd0 0%, #330867 100%)',
    'linear-gradient(135deg, #a8edea 0%, #fed6e3 100%)',
    'linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%)',
];

function obtenerColorCalibre(index) {
    return coloresCalibres[index % coloresCalibres.length];
}

// Al cargar la página
document.addEventListener("DOMContentLoaded", () => {
    cargarCombos();
    establecerFechaHoy();
    configurarEventos();
});

// Establecer fecha de hoy por defecto
function establecerFechaHoy() {
    const hoy = new Date().toISOString().split('T')[0];
    document.getElementById('fecha').value = hoy;
}

// Cargar todos los combos
function cargarCombos() {
    cargarExportadoras();
    cargarEspecies();
    cargarDestinos();
}

function cargarExportadoras() {
    fetch('../services/api_exportadora.php')
        .then(response => response.json())
        .then(data => {
            const select = document.getElementById('exportadora');
            select.innerHTML = '<option value="">Seleccione...</option>';
            if (Array.isArray(data)) {
                data.forEach(exp => {
                    select.innerHTML += `<option value="${exp.id}">${exp.Nombre_Exportadora}</option>`;
                });
            }
            select.addEventListener('change', () => {
                cargarCombosPorExportadora(select.value);
            });
        })
        .catch(error => console.error('Error cargando exportadoras:', error));
}

function cargarEspecies() {
    fetch('../services/api_especies.php')
        .then(response => response.json())
        .then(data => {
            const select = document.getElementById('especie');
            select.innerHTML = '<option value="">Seleccione...</option>';
            if (Array.isArray(data)) {
                data.forEach(esp => {
                    select.innerHTML += `<option value="${esp.id_especie}">${esp.especie}</option>`;
                });
            }
            select.addEventListener('change', () => {
                const id_exportadora = document.getElementById('exportadora').value;
                if (id_exportadora) {
                    cargarCombosPorExportadora(id_exportadora);
                }
            });
        })
        .catch(error => console.error('Error cargando especies:', error));
}

function cargarDestinos() {
    fetch('../services/api_destino.php')
        .then(response => response.json())
        .then(data => {
            const select = document.getElementById('detalle_destino');
            select.innerHTML = '<option value="">Seleccione...</option>';
            if (Array.isArray(data)) {
                data.forEach(dest => {
                    select.innerHTML += `<option value="${dest.id}">${dest.nombre_destino}</option>`;
                });
            }
        })
        .catch(error => console.error('Error cargando destinos:', error));
}

// Cargar combos filtrados por exportadora
function cargarCombosPorExportadora(id_exportadora) {
    const id_especie = document.getElementById('especie').value || null;
    
    if (!id_exportadora) return;
    
    cargarCalibres(id_especie);
    cargarEmbalajes(id_exportadora, id_especie);
    cargarCategorias(id_exportadora, id_especie);
    cargarPLU(id_especie);
    cargarEtiquetas(id_exportadora);
    cargarPallets(id_exportadora);
}

function cargarCalibres(id_especie) {
    let url = '../models/obtener_calibres.php';
    if (id_especie) {
        url += '?id_especie=' + id_especie;
    }
    
    fetch(url)
        .then(response => response.json())
        .then(data => {
            const select = document.getElementById('detalle_calibre');
            select.innerHTML = '';
            select.multiple = true;
            select.size = 5;
            
            if (Array.isArray(data)) {
                const calibresPorEspecie = {};
                data.forEach(cal => {
                    if (!calibresPorEspecie[cal.especie]) {
                        calibresPorEspecie[cal.especie] = [];
                    }
                    calibresPorEspecie[cal.especie].push(cal);
                });
                
                Object.keys(calibresPorEspecie).forEach(especie => {
                    select.innerHTML += `<optgroup label="${especie}">`;
                    calibresPorEspecie[especie].forEach(cal => {
                        select.innerHTML += `<option value="${cal.id}">${cal.cod_calibre} - ${cal.nombre_calibre}</option>`;
                    });
                    select.innerHTML += `</optgroup>`;
                });
            }
        })
        .catch(error => console.error('Error cargando calibres:', error));
}

function cargarEmbalajes(id_exportadora, id_especie) {
    let url = '../models/obtener_embalajes.php?id_exportadora=' + id_exportadora;
    if (id_especie) {
        url += '&id_especie=' + id_especie;
    }
    
    fetch(url)
        .then(response => response.json())
        .then(data => {
            const select = document.getElementById('detalle_embalaje');
            select.innerHTML = '<option value="">Seleccione...</option>';
            
            // Remover evento anterior si existe
            const newSelect = select.cloneNode(true);
            select.parentNode.replaceChild(newSelect, select);
            
            if (Array.isArray(data)) {
                data.forEach(emb => {
                    const codigo = emb.codigo_embalaje || emb.Codigo_emb || '';
                    const descripcion = emb.nombre_embalaje || emb.Descripcion_Embalaje || '';
                    const texto = `${codigo} - ${descripcion}`;
                    newSelect.innerHTML += `<option value="${emb.id}">${texto}</option>`;
                });
            }
            
            // Configurar evento DESPUÉS de cargar los datos
            newSelect.addEventListener('change', () => {
                const id_embalaje = newSelect.value;
                console.log('📏 Cambiando embalaje, id_embalaje:', id_embalaje);
                
                if (id_embalaje) {
                    const alturaUrl = `../models/obtener_altura_pallet.php?id_embalaje=${id_embalaje}`;
                    console.log('🔗 Fetch URL:', alturaUrl);
                    
                    fetch(alturaUrl)
                        .then(response => {
                            console.log('📡 Response status:', response.status);
                            return response.json();
                        })
                        .then(data => {
                            console.log('💾 Data recibida:', data);
                            const alturaSelect = document.getElementById('detalle_altura');
                            alturaSelect.innerHTML = '<option value="">Seleccione...</option>';
                            
                            if (Array.isArray(data) && data.length > 0) {
                                data.forEach(alt => {
                                    const texto = `${alt.altura} cm - ${alt.cajas} cajas`;
                                    console.log('✅ Agregando opción:', texto);
                                    alturaSelect.innerHTML += `<option value="${alt.id_altura_pallet}">${texto}</option>`;
                                });
                                console.log('🎉 Alturas cargadas:', data.length);
                            } else {
                                console.log('⚠️ No hay alturas disponibles para este embalaje');
                                alturaSelect.innerHTML += '<option value="">Sin alturas disponibles</option>';
                            }
                        })
                        .catch(error => {
                            console.error('❌ Error cargando alturas:', error);
                        });
                } else {
                    document.getElementById('detalle_altura').innerHTML = '<option value="">Primero seleccione embalaje</option>';
                }
            });
            
            console.log('✅ Evento change configurado para embalaje');
        })
        .catch(error => console.error('Error cargando embalajes:', error));
}

function cargarCategorias(id_exportadora, id_especie) {
    let url = '../models/obtener_categoria.php?id_exportadora=' + id_exportadora;
    if (id_especie) {
        url += '&id_especie=' + id_especie;
    }
    
    fetch(url)
        .then(response => response.json())
        .then(data => {
            const select = document.getElementById('detalle_categoria');
            select.innerHTML = '<option value="">Seleccione...</option>';
            if (Array.isArray(data)) {
                data.forEach(cat => {
                    select.innerHTML += `<option value="${cat.id}">${cat.nombre_categoria}</option>`;
                });
            }
        })
        .catch(error => console.error('Error cargando categorías:', error));
}

function cargarPLU(id_especie) {
    let url = '../models/obtener_plus.php';
    if (id_especie) {
        url += '?id_especie=' + id_especie;
    }
    
    fetch(url)
        .then(response => response.json())
        .then(data => {
            const select = document.getElementById('detalle_plu');
            select.innerHTML = '<option value="">Seleccione...</option>';
            if (Array.isArray(data)) {
                data.forEach(plu => {
                    const texto = plu.nombre_plu || plu.plu || plu.codigo_plu || 'Sin PLU';
                    select.innerHTML += `<option value="${plu.id}">${texto}</option>`;
                });
            }
        })
        .catch(error => console.error('Error cargando PLU:', error));
}

function cargarEtiquetas(id_exportadora) {
    fetch('../models/obtener_etiquetas.php?id_exportadora=' + id_exportadora)
        .then(response => response.json())
        .then(data => {
            const select = document.getElementById('detalle_etiqueta');
            select.innerHTML = '<option value="">Seleccione...</option>';
            if (Array.isArray(data)) {
                data.forEach(etq => {
                    const texto = etq.nombre_etiqueta || etq.Nombre_etiqueta || 'Sin nombre';
                    select.innerHTML += `<option value="${etq.id}">${texto}</option>`;
                });
            }
        })
        .catch(error => console.error('Error cargando etiquetas:', error));
}

function cargarPallets(id_exportadora) {
    fetch('../models/obtener_pallets.php?id_exportadora=' + id_exportadora)
        .then(response => response.json())
        .then(data => {
            const select = document.getElementById('detalle_pallet');
            select.innerHTML = '<option value="">Seleccione...</option>';
            if (Array.isArray(data)) {
                data.forEach(pal => {
                    const codigo = pal.cod_pallet || '';
                    const texto = pal.describ_pallet || 'Sin nombre';
                    select.innerHTML += `<option value="${pal.id}">${codigo} - ${texto}</option>`;
                });
            }
        })
        .catch(error => console.error('Error cargando pallets:', error));
}

// Agregar pedido
function agregarPedido() {
    const numero = document.getElementById('numero_pedido').value;
    const cantidad = document.getElementById('cantidad_pedido').value;
    const prioridad = document.getElementById('prioridad_pedido').value;

    if (!numero || !cantidad || !prioridad) {
        alert('⚠️ Complete todos los campos del pedido');
        return;
    }

    if (pedidosAgregados.some(p => p.numero === numero)) {
        alert('⚠️ Este número de pedido ya fue agregado');
        return;
    }

    pedidosAgregados.push({ numero, cantidad, prioridad });
    actualizarTablaPedidos();
    actualizarComboPedidos();

    document.getElementById('numero_pedido').value = '';
    document.getElementById('cantidad_pedido').value = '';
    document.getElementById('prioridad_pedido').value = '';
}

function actualizarTablaPedidos() {
    const tbody = document.querySelector('#tablaPedidos tbody');
    tbody.innerHTML = '';

    pedidosAgregados.forEach((pedido, index) => {
        const row = tbody.insertRow();
        row.innerHTML = `
            <td>${pedido.numero}</td>
            <td>${pedido.cantidad}</td>
            <td>${pedido.prioridad}</td>
            <td>
                <button class="btn btn-sm btn-danger" onclick="eliminarPedido(${index})">🗑️ Eliminar</button>
            </td>
        `;
    });
}

window.eliminarPedido = function(index) {
    pedidosAgregados.splice(index, 1);
    actualizarTablaPedidos();
    actualizarComboPedidos();
}

function actualizarComboPedidos() {
    const select = document.getElementById('detalle_pedido');
    select.innerHTML = '<option value="">Seleccione...</option>';
    
    pedidosAgregados.forEach(pedido => {
        select.innerHTML += `<option value="${pedido.numero}">${pedido.numero} (Cant: ${pedido.cantidad})</option>`;
    });
}

// Agregar detalle (GRUPO de calibres con misma configuración)
function agregarDetalle() {
    const calibreSelect = document.getElementById('detalle_calibre');
    const calibresSeleccionados = Array.from(calibreSelect.selectedOptions).map(opt => ({
        id: opt.value,
        texto: opt.text.split(' - ')[0] || opt.text // Solo el código del calibre
    }));
    
    const numero_pedido = document.getElementById('detalle_pedido').value;
    const cantidad = document.getElementById('detalle_cantidad').value || '0';
    
    const id_embalaje = document.getElementById('detalle_embalaje').value;
    const embalajeSelect = document.getElementById('detalle_embalaje');
    const embalaje_text = embalajeSelect.options[embalajeSelect.selectedIndex]?.text || 'N/A';
    
    const id_categoria = document.getElementById('detalle_categoria').value;
    const categoriaSelect = document.getElementById('detalle_categoria');
    const categoria_text = categoriaSelect.options[categoriaSelect.selectedIndex]?.text || 'N/A';
    
    const id_plu = document.getElementById('detalle_plu').value;
    const pluSelect = document.getElementById('detalle_plu');
    const plu_text = pluSelect.options[pluSelect.selectedIndex]?.text || 'N/A';
    
    const id_etiqueta = document.getElementById('detalle_etiqueta').value;
    const etiquetaSelect = document.getElementById('detalle_etiqueta');
    const etiqueta_text = etiquetaSelect.options[etiquetaSelect.selectedIndex]?.text || 'N/A';
    
    const id_pallet = document.getElementById('detalle_pallet').value;
    const palletSelect = document.getElementById('detalle_pallet');
    const pallet_text = palletSelect.options[palletSelect.selectedIndex]?.text || 'N/A';
    
    const id_altura = document.getElementById('detalle_altura').value;
    const alturaSelect = document.getElementById('detalle_altura');
    const altura_text = alturaSelect.options[alturaSelect.selectedIndex]?.text || 'N/A';
    
    const id_destino = document.getElementById('detalle_destino').value;
    const destinoSelect = document.getElementById('detalle_destino');
    const destino_text = destinoSelect.options[destinoSelect.selectedIndex]?.text || 'N/A';
    
    const variedad_etiquetada = document.getElementById('detalle_variedad').value || '';
    const observacion = document.getElementById('detalle_obs').value || '';

    if (calibresSeleccionados.length === 0 || !numero_pedido) {
        alert('⚠️ Debe seleccionar al menos un calibre y el pedido');
        return;
    }

    // Crear un registro agrupado por pedido con todos los calibres
    detalleAgregado.push({
        numero_pedido,
        cantidad,
        id_embalaje,
        embalaje_text,
        id_categoria,
        categoria_text,
        id_plu,
        plu_text,
        id_etiqueta,
        etiqueta_text,
        id_pallet,
        pallet_text,
        id_altura,
        altura_text,
        id_destino,
        destino_text,
        variedad_etiquetada,
        observacion,
        calibres: calibresSeleccionados // Array de {id, texto}
    });

    actualizarTablaDetalle();
    calibreSelect.value = '';
    document.getElementById('detalle_cantidad').value = '';
    document.getElementById('detalle_embalaje').value = '';
    document.getElementById('detalle_categoria').value = '';
    document.getElementById('detalle_plu').value = '';
    document.getElementById('detalle_etiqueta').value = '';
    document.getElementById('detalle_pallet').value = '';
    document.getElementById('detalle_altura').value = '';
    document.getElementById('detalle_destino').value = '';
    document.getElementById('detalle_variedad').value = '';
    document.getElementById('detalle_obs').value = '';
}

// Actualizar tabla de detalle (AGRUPADA POR PEDIDO)
function actualizarTablaDetalle() {
    const tbody = document.querySelector('#tablaDetalle tbody');
    tbody.innerHTML = '';
    
    // Actualizar cinta de calibres
    actualizarCintaCalibres();

    detalleAgregado.forEach((det, index) => {
        const row = tbody.insertRow();
        
        // Crear badges de calibres con colores
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
            <td>${det.categoria_text}</td>
            <td>${det.plu_text}</td>
            <td>${det.destino_text || '-'}</td>
            <td>${det.pallet_text || '-'}</td>
            <td>${det.cantidad}</td>
            <td>${det.altura_text || '-'}</td>
            <td>${det.observacion || '-'}</td>
            <td>
                <button class="btn btn-sm btn-danger" onclick="eliminarDetalle(${index})">🗑️ Eliminar</button>
            </td>
        `;
    });
}

// Actualizar cinta de calibres
function actualizarCintaCalibres() {
    const cintaContainer = document.getElementById('cintaCalibres');
    const cintaContent = document.getElementById('cintaCalibresContent');
    
    // Recolectar todos los calibres únicos
    const calibresUnicos = new Map();
    detalleAgregado.forEach(det => {
        det.calibres.forEach(calibre => {
            if (!calibresUnicos.has(calibre.id)) {
                calibresUnicos.set(calibre.id, calibre.texto);
            }
        });
    });
    
    if (calibresUnicos.size === 0) {
        cintaContainer.style.display = 'none';
        return;
    }
    
    cintaContainer.style.display = 'block';
    cintaContent.innerHTML = '';
    
    let index = 0;
    calibresUnicos.forEach((texto, id) => {
        const color = obtenerColorCalibre(index++);
        const badge = document.createElement('span');
        badge.className = 'badge';
        badge.style.cssText = `background: ${color}; padding: 8px 16px; border-radius: 20px; font-size: 13px; font-weight: 600;`;
        badge.textContent = texto;
        cintaContent.appendChild(badge);
    });
}

// Mostrar pantalla completa
window.mostrarPantallaCompleta = function() {
    if (detalleAgregado.length === 0) {
        alert('⚠️ No hay detalles agregados');
        return;
    }
    
    // Actualizar cinta de calibres en modal
    const cintaModal = document.getElementById('cintaCalibresModalContent');
    cintaModal.innerHTML = '';
    
    const calibresUnicos = new Map();
    detalleAgregado.forEach(det => {
        det.calibres.forEach(calibre => {
            if (!calibresUnicos.has(calibre.id)) {
                calibresUnicos.set(calibre.id, calibre.texto);
            }
        });
    });
    
    let index = 0;
    calibresUnicos.forEach((texto, id) => {
        const color = obtenerColorCalibre(index++);
        const badge = document.createElement('span');
        badge.className = 'badge';
        badge.style.cssText = `background: ${color}; padding: 10px 20px; border-radius: 20px; font-size: 14px; font-weight: 600;`;
        badge.textContent = texto;
        cintaModal.appendChild(badge);
    });
    
    // Calcular estadísticas
    const totalPedidos = detalleAgregado.length;
    const totalCalibres = detalleAgregado.reduce((sum, det) => sum + det.calibres.length, 0);
    const totalCajas = detalleAgregado.reduce((sum, det) => sum + parseInt(det.cantidad || 0), 0);
    const destinosUnicos = new Set(detalleAgregado.map(det => det.id_destino).filter(d => d));
    
    document.getElementById('statPedidos').textContent = totalPedidos;
    document.getElementById('statCalibres').textContent = totalCalibres;
    document.getElementById('statCajas').textContent = totalCajas.toLocaleString();
    document.getElementById('statDestinos').textContent = destinosUnicos.size;
    
    // Llenar tabla en modal
    const tbody = document.querySelector('#tablaDetalleModal tbody');
    tbody.innerHTML = '';
    
    detalleAgregado.forEach(det => {
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
            <td>${det.embalaje_text}</td>
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
    
    // Mostrar modal
    const modal = new bootstrap.Modal(document.getElementById('modalPantallaCompleta'));
    modal.show();
}

window.eliminarDetalle = function(index) {
    detalleAgregado.splice(index, 1);
    actualizarTablaDetalle();
}

// Mostrar modal de confirmación
function mostrarConfirmacion() {
    const exportadora = document.getElementById('exportadora').options[document.getElementById('exportadora').selectedIndex]?.text;
    const especie = document.getElementById('especie').options[document.getElementById('especie').selectedIndex]?.text;
    const turno = document.getElementById('turno').value;
    const fecha = document.getElementById('fecha').value;
    const observacion = document.getElementById('observacion').value;

    if (!exportadora || !especie || !turno || !fecha) {
        alert('⚠️ Complete todos los campos de la cabecera');
        return;
    }

    if (pedidosAgregados.length === 0) {
        alert('⚠️ Debe agregar al menos un pedido');
        return;
    }

    if (detalleAgregado.length === 0) {
        alert('⚠️ Debe agregar al menos un calibre al detalle');
        return;
    }

    // Contar total de calibres
    const totalCalibres = detalleAgregado.reduce((sum, det) => sum + det.calibres.length, 0);

    document.getElementById('confirmExportadora').textContent = exportadora;
    document.getElementById('confirmEspecie').textContent = especie;
    document.getElementById('confirmTurno').textContent = turno;
    document.getElementById('confirmFecha').textContent = fecha;
    document.getElementById('confirmPedidos').textContent = pedidosAgregados.length + ' pedidos';
    document.getElementById('confirmDetalle').textContent = totalCalibres + ' calibres en ' + detalleAgregado.length + ' grupos';

    const modal = new bootstrap.Modal(document.getElementById('modalConfirmacion'));
    modal.show();
}

// Guardar instructivo completo
function guardarInstructivo() {
    // Preparar datos para enviar (desagrupar calibres)
    const detalleDesagrupado = [];
    
    detalleAgregado.forEach(det => {
        det.calibres.forEach(calibre => {
            detalleDesagrupado.push({
                id_calibre: calibre.id,
                numero_pedido: det.numero_pedido,
                cantidad: det.cantidad,
                id_embalaje: det.id_embalaje,
                embalaje_text: det.embalaje_text,
                id_categoria: det.id_categoria,
                categoria_text: det.categoria_text,
                id_plu: det.id_plu,
                plu_text: det.plu_text,
                id_etiqueta: det.id_etiqueta,
                etiqueta_text: det.etiqueta_text,
                id_pallet: det.id_pallet,
                pallet_text: det.pallet_text,
                id_altura: det.id_altura,
                altura_text: det.altura_text,
                id_destino: det.id_destino,
                destino_text: det.destino_text,
                variedad_etiquetada: det.variedad_etiquetada,
                observacion: det.observacion
            });
        });
    });
    
    const data = {
        cabecera: {
            id_exportadora: document.getElementById('exportadora').value,
            id_especie: document.getElementById('especie').value,
            turno: document.getElementById('turno').value,
            fecha: document.getElementById('fecha').value,
            observacion: document.getElementById('observacion').value
        },
        pedidos: pedidosAgregados,
        detalle: detalleDesagrupado
    };

    fetch('../controllers/guardar_instructivo_completo.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.text())
    .then(respuesta => {
        const modal = bootstrap.Modal.getInstance(document.getElementById('modalConfirmacion'));
        modal.hide();

        if (respuesta.includes('éxito') || respuesta.includes('correctamente') || respuesta.includes('Instructivo')) {
            alert('✅ ' + respuesta);
            pedidosAgregados = [];
            detalleAgregado = [];
            actualizarTablaPedidos();
            actualizarTablaDetalle();
            document.getElementById('formCabecera').reset();
            establecerFechaHoy();
            cargarCombos();
        } else {
            alert('❌ Error: ' + respuesta);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('❌ Error de conexión: ' + error.message);
    });
}

// Configurar eventos
function configurarEventos() {
    const btnAgregarPedido = document.getElementById('btnAgregarPedido');
    const btnAgregarDetalle = document.getElementById('btnAgregarDetalle');
    const btnGuardarInstructivo = document.getElementById('btnGuardarInstructivo');
    const btnConfirmarGuardar = document.getElementById('btnConfirmarGuardado');
    
    if (btnAgregarPedido) {
        btnAgregarPedido.addEventListener('click', agregarPedido);
    }
    if (btnAgregarDetalle) {
        btnAgregarDetalle.addEventListener('click', agregarDetalle);
    }
    if (btnGuardarInstructivo) {
        btnGuardarInstructivo.addEventListener('click', mostrarConfirmacion);
    }
    if (btnConfirmarGuardar) {
        btnConfirmarGuardar.addEventListener('click', guardarInstructivo);
    }
    
    console.log('✅ Eventos configurados');
    console.log('📋 Botones encontrados:', 
        {
        btnAgregarPedido: !!btnAgregarPedido,
        btnAgregarDetalle: !!btnAgregarDetalle,
        btnGuardarInstructivo: !!btnGuardarInstructivo,
        btnConfirmarGuardar: !!btnConfirmarGuardar
    });
}
