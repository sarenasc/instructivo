document.addEventListener("DOMContentLoaded", () => {
  const exportadoraSelect = document.getElementById("exportadora");
  const instructivoSelect = document.getElementById("instructivo");
  const versionSelect     = document.getElementById("version");
  const modal = new bootstrap.Modal(document.getElementById("modalDetalle"));

  // Array de grupos: cada grupo = una fila de la tabla con múltiples calibres
  let grupos = [];

  // ── 1. Cargar exportadoras ─────────────────────────────────────────────────
  fetch("../services/api_exportadora.php")
    .then(res => res.json())
    .then(data => {
      if (!data || data.length === 0) return;
      data.forEach(exp => {
        exportadoraSelect.innerHTML += `<option value="${exp.id}">${exp.Nombre_Exportadora}</option>`;
      });
    })
    .catch(err => console.error("Error al cargar exportadoras:", err));

  // ── 2. Exportadora → instructivos ──────────────────────────────────────────
  exportadoraSelect.addEventListener("change", () => {
    const idExportadora = exportadoraSelect.value;
    if (!idExportadora) {
      instructivoSelect.disabled = true;
      instructivoSelect.innerHTML = '<option value="">Seleccione una exportadora primero</option>';
      return;
    }
    instructivoSelect.disabled = false;
    instructivoSelect.innerHTML = '<option value="">Cargando instructivos...</option>';
    versionSelect.innerHTML    = '<option value="">Seleccione una versión</option>';
    versionSelect.disabled     = true;

    fetch(`../models/obtener_instructivo.php?id_exportadora=${idExportadora}`)
      .then(res => res.json())
      .then(json => {
        if (json.success && json.data && json.data.length > 0) {
          instructivoSelect.innerHTML = '<option value="">Seleccione un instructivo</option>';
          json.data.forEach(ins => {
            const fecha   = ins.fecha_formateada || 'Sin fecha';
            const especie = ins.especie || '';
            instructivoSelect.innerHTML += `<option value="${ins.id_instructivo}">${ins.id_instructivo} - ${fecha} - ${especie}</option>`;
          });
        } else {
          instructivoSelect.innerHTML = '<option value="">No hay instructivos para esta exportadora</option>';
        }
      })
      .catch(err => {
        console.error("Error al cargar instructivos:", err);
        instructivoSelect.innerHTML = '<option value="">Error al cargar</option>';
      });
  });

  // ── 3. Instructivo → versiones ─────────────────────────────────────────────
  instructivoSelect.addEventListener("change", () => {
    const idInstructivo = instructivoSelect.value;
    if (!idInstructivo) {
      versionSelect.disabled = true;
      versionSelect.innerHTML = '<option value="">Seleccione un instructivo primero</option>';
      return;
    }
    versionSelect.disabled = false;
    versionSelect.innerHTML = '<option value="">Cargando versiones...</option>';

    fetch(`../models/obtener_version.php?id_instructivo=${idInstructivo}`)
      .then(res => res.json())
      .then(data => {
        if (data.success && data.data && data.data.length > 0) {
          const unicas = [...new Set(data.data.map(i => i.version))];
          versionSelect.innerHTML = '<option value="">Seleccione versión</option>';
          unicas.forEach(v => {
            const opt = document.createElement('option');
            opt.value = v;
            opt.textContent = `Versión ${v}`;
            versionSelect.appendChild(opt);
          });
        } else {
          versionSelect.innerHTML = '<option value="">No se encontraron versiones</option>';
        }
      })
      .catch(err => {
        console.error("Error al obtener versiones:", err);
        versionSelect.innerHTML = '<option value="">Error al cargar versiones</option>';
      });
  });

  // ── 4. Versión → detalle agrupado ──────────────────────────────────────────
  versionSelect.addEventListener("change", () => {
    if (!versionSelect.value) return;

    fetch(`../models/obtener_detalle_por_version.php?id_instructivo=${instructivoSelect.value}&version=${versionSelect.value}`)
      .then(res => res.json())
      .then(json => {
        if (json.success && json.data && json.data.length > 0) {
          grupos = agrupar(json.data);
          renderTabla();
          renderPedidos();
          modal.show();
        } else {
          alert("No se encontró el detalle del instructivo.");
        }
      })
      .catch(err => {
        console.error("Error al obtener detalle:", err);
        alert("Error al cargar el detalle del instructivo.");
      });
  });

  // ── Agrupar filas por configuración (calibres agrupados) ───────────────────
  function agrupar(rawData) {
    const mapa = new Map();
    rawData.forEach(d => {
      const key = [
        d.numero_pedido, d.id_embalaje, d.id_etiqueta,
        d.id_destino, d.id_plu, d.id_categoria,
        d.id_pallet, d.altura_pallet, d.cantidad_pedido,
        d.var_etiquetada, d.observacion
      ].join('|');

      if (!mapa.has(key)) {
        mapa.set(key, {
          numero_pedido:  d.numero_pedido,
          var_etiquetada: d.var_etiquetada  || null,
          id_embalaje:    d.id_embalaje,
          embalaje:       d.embalaje        || '',
          id_etiqueta:    d.id_etiqueta,
          Nombre_etiqueta:d.Nombre_etiqueta || '',
          id_destino:     d.id_destino,
          nombre_destino: d.nombre_destino  || '',
          id_plu:         d.id_plu,
          plu:            d.plu             || '',
          id_categoria:   d.id_categoria,
          categoria:      d.categoria       || '',
          id_pallet:      d.id_pallet,
          Descrip_pallet: d.Descrip_pallet  || '',
          altura_pallet:  d.altura_pallet,
          altura_label:   d.altura_label    || '',
          observacion:    d.observacion     || null,
          cantidad_pedido:d.cantidad_pedido || null,
          calibres: []
        });
      }
      mapa.get(key).calibres.push({
        id:    d.id_calibre,
        texto: d.cod_calibre ? `${d.cod_calibre} - ${d.calibre}` : d.calibre,
        activo: true
      });
    });
    return [...mapa.values()];
  }

  // ── Render tabla ───────────────────────────────────────────────────────────
  function renderTabla() {
    const tbody = document.getElementById("tbodyDetalle");
    tbody.innerHTML = "";

    grupos.forEach((g, gi) => {
      const tr = document.createElement("tr");

      // Construir el multiselect de calibres
      const selectId = `cal-select-${gi}`;
      const optionsHtml = g.calibres
        .map((c, ci) => `<option value="${ci}" ${c.activo ? 'selected' : ''}>${esc(c.texto)}</option>`)
        .join('');

      tr.innerHTML = `
        <td>
          <input type="text"
                 class="form-control form-control-sm input-pedido"
                 value="${esc(g.numero_pedido)}"
                 data-gi="${gi}"
                 style="min-width:90px">
        </td>
        <td>${esc(g.embalaje)}</td>
        <td>
          <select id="${selectId}"
                  class="form-select form-select-sm select-calibres"
                  multiple
                  size="${Math.min(g.calibres.length, 6)}"
                  data-gi="${gi}"
                  style="min-width:160px">
            ${optionsHtml}
          </select>
          <small class="text-muted d-block mt-1" style="font-size:0.7em">
            Ctrl+clic para deseleccionar
          </small>
        </td>
        <td>${esc(g.plu)}</td>
        <td>${esc(g.Nombre_etiqueta)}</td>
        <td>${esc(g.categoria)}</td>
        <td>${esc(g.altura_label)}</td>
        <td>${esc(g.nombre_destino)}</td>
        <td>
          <button type="button"
                  class="btn btn-sm btn-danger btn-eliminar-grupo"
                  data-gi="${gi}"
                  title="Eliminar fila">×</button>
        </td>`;
      tbody.appendChild(tr);
    });

    // Evento cambio de pedido
    tbody.querySelectorAll('.input-pedido').forEach(input => {
      input.addEventListener("change", e => {
        grupos[parseInt(e.target.dataset.gi)].numero_pedido = e.target.value.trim();
        renderPedidos();
      });
    });

    // Evento multiselect calibres → actualiza activo/inactivo
    tbody.querySelectorAll('.select-calibres').forEach(sel => {
      sel.addEventListener("change", e => {
        const gi = parseInt(e.target.dataset.gi);
        const seleccionados = new Set(
          [...e.target.selectedOptions].map(o => parseInt(o.value))
        );
        grupos[gi].calibres.forEach((c, ci) => {
          c.activo = seleccionados.has(ci);
        });
        actualizarContador();
      });
    });

    // Eliminar grupo completo
    tbody.querySelectorAll('.btn-eliminar-grupo').forEach(btn => {
      btn.addEventListener("click", e => {
        grupos.splice(parseInt(e.currentTarget.dataset.gi), 1);
        renderTabla();
        renderPedidos();
      });
    });

    actualizarContador();
  }

  function actualizarContador() {
    const totalFilas = grupos.reduce((sum, g) => sum + g.calibres.filter(c => c.activo).length, 0);
    document.getElementById("contadorFilas").textContent =
      `${grupos.length} grupo(s) · ${totalFilas} fila(s) a copiar`;
  }

  // ── Render panel de pedidos ────────────────────────────────────────────────
  function renderPedidos() {
    const pedidos = pedidosUnicos();

    document.getElementById("listaPedidos").innerHTML = pedidos.length
      ? pedidos.map(p => `<span class="badge bg-primary" style="font-size:.85em">Pedido ${esc(p)}</span>`).join(" ")
      : '<span class="text-muted small">Sin pedidos</span>';

    const selQuitar = document.getElementById("selectQuitarPedido");
    selQuitar.innerHTML = '<option value="">Seleccione...</option>';
    pedidos.forEach(p => { selQuitar.innerHTML += `<option value="${esc(p)}">${p}</option>`; });

    const selBase = document.getElementById("basarseEnPedido");
    selBase.innerHTML = '<option value="">Pedido base...</option>';
    pedidos.forEach(p => { selBase.innerHTML += `<option value="${esc(p)}">${p}</option>`; });
  }

  function pedidosUnicos() {
    return [...new Set(grupos.map(g => g.numero_pedido).filter(Boolean))];
  }

  // ── Quitar pedido ──────────────────────────────────────────────────────────
  document.getElementById("btnQuitarPedido").addEventListener("click", () => {
    const pedido = document.getElementById("selectQuitarPedido").value;
    if (!pedido) { alert("Seleccione un pedido para quitar."); return; }
    grupos = grupos.filter(g => g.numero_pedido !== pedido);
    renderTabla();
    renderPedidos();
  });

  // ── Agregar pedido (clona grupos de un pedido base) ────────────────────────
  document.getElementById("btnAgregarPedido").addEventListener("click", () => {
    const nuevoPedido = document.getElementById("nuevoPedidoNum").value.trim();
    const pedidoBase  = document.getElementById("basarseEnPedido").value;

    if (!nuevoPedido) { alert("Ingrese el número del nuevo pedido."); return; }
    if (!pedidoBase)  { alert("Seleccione el pedido en el que basarse."); return; }
    if (pedidosUnicos().includes(nuevoPedido)) {
      alert("Ese número de pedido ya existe en el detalle."); return;
    }

    const base = grupos.filter(g => g.numero_pedido === pedidoBase);
    base.forEach(g => {
      grupos.push({
        ...g,
        numero_pedido: nuevoPedido,
        calibres: g.calibres.map(c => ({ ...c }))
      });
    });

    document.getElementById("nuevoPedidoNum").value = "";
    renderTabla();
    renderPedidos();
  });

  // ── Enviar copia ───────────────────────────────────────────────────────────
  document.getElementById("formDetalle").addEventListener("submit", e => {
    e.preventDefault();

    const idInstructivo = instructivoSelect.value;
    const version       = versionSelect.value;
    const nuevaFecha    = document.getElementById('nueva_fecha').value;
    const turno         = document.getElementById('turno').value;

    if (!idInstructivo || !version || !nuevaFecha || !turno) {
      alert("Complete todos los campos requeridos.");
      return;
    }

    // Desagrupar: un registro por calibre activo
    const detalles = [];
    grupos.forEach(g => {
      g.calibres.filter(c => c.activo).forEach(c => {
        detalles.push({
          numero_pedido:   g.numero_pedido,
          var_etiquetada:  g.var_etiquetada,
          id_embalaje:     g.id_embalaje,
          id_etiqueta:     g.id_etiqueta,
          id_destino:      g.id_destino,
          id_plu:          g.id_plu,
          id_categoria:    g.id_categoria,
          id_pallet:       g.id_pallet,
          altura_pallet:   g.altura_pallet,
          observacion:     g.observacion,
          id_calibre:      c.id,
          cantidad_pedido: g.cantidad_pedido
        });
      });
    });

    if (detalles.length === 0) {
      alert("Debe tener al menos un calibre activo para copiar.");
      return;
    }

    fetch('../copiar_instructivo.php', {
      method:  'POST',
      headers: { 'Content-Type': 'application/json' },
      body:    JSON.stringify({
        id_instructivo: parseInt(idInstructivo),
        version:        parseInt(version),
        fecha:          nuevaFecha,
        turno:          turno,
        detalles:       detalles
      })
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        alert("Instructivo copiado exitosamente. Nuevo ID: " + data.nuevo_id);
        const inst = bootstrap.Modal.getInstance(document.getElementById('modalDetalle'));
        if (inst) inst.hide();
        setTimeout(() => location.reload(), 1000);
      } else {
        alert("Error: " + data.message);
        console.error(data.detalle || '');
      }
    })
    .catch(err => {
      console.error("Error al copiar instructivo:", err);
      alert("Error de conexión al copiar el instructivo.");
    });
  });

  // ── Utilidad: escapar HTML ─────────────────────────────────────────────────
  function esc(val) {
    if (val == null) return '';
    return String(val)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;');
  }
});
