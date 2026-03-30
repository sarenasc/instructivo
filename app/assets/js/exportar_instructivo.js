let instructivos = [];

document.addEventListener("DOMContentLoaded", () => {
  console.log('🚀 Cargando página de exportar instructivo...');
  
  // Cargar instructivos con exportadora y fecha
  fetch('../models/obtener_instructivos.php')
    .then(res => {
      console.log('📡 Response status:', res.status);
      return res.json();
    })
    .then(data => {
      console.log('💾 Instructivos recibidos:', data.length);
      instructivos = data;
      const select = document.getElementById('selectInstructivo');
      select.innerHTML = '<option value="">Seleccione...</option>';
      
      if (data.length === 0) {
        console.log('⚠️ No hay instructivos disponibles');
        select.innerHTML += '<option value="">Sin instructivos</option>';
        return;
      }
      
      data.forEach(item => {
        const option = document.createElement('option');
        option.value = item.id_instructivo;
        const fecha = item.fecha ? (typeof item.fecha === 'object' ? item.fecha.format('Y-m-d') : item.fecha) : 'N/A';
        option.text = `${item.id_instructivo} - ${item.nombre_exportadora} - ${fecha} - ${item.especie}`;
        select.appendChild(option);
      });
      
      cargarVersiones(); // carga inicial
    })
    .catch(error => {
      console.error('❌ Error cargando instructivos:', error);
      alert('Error al cargar instructivos: ' + error.message);
    });

  const selectInstructivo = document.getElementById('selectInstructivo');
  if (selectInstructivo) {
    selectInstructivo.addEventListener('change', () => {
      console.log('🔄 Cambio de instructivo, cargando versiones...');
      cargarVersiones();
    });
  }
});

function cargarVersiones() {
  const id = document.getElementById('selectInstructivo').value;
  console.log('📋 Cargando versiones para instructivo:', id);
  
  if (!id) {
    console.log('⚠️ No hay instructivo seleccionado');
    const select = document.getElementById('selectVersion');
    select.innerHTML = '<option value="">Primero seleccione instructivo</option>';
    return;
  }
  
  fetch(`../models/obtener_versiones.php?id_instructivo=${id}`)
    .then(res => {
      console.log('📡 Response status:', res.status);
      return res.json();
    })
    .then(data => {
      console.log('💾 Versiones recibidas:', data.length);
      const select = document.getElementById('selectVersion');
      select.innerHTML = '<option value="">Seleccione...</option>';
      
      if (data.length === 0) {
        select.innerHTML += '<option value="">Sin versiones</option>';
        return;
      }
      
      data.forEach(v => {
        const option = document.createElement('option');
        option.value = v.version;
        option.text = `Versión ${v.version}`;
        select.appendChild(option);
      });
    })
    .catch(error => {
      console.error('❌ Error cargando versiones:', error);
    });
}

function descargarExcel() {
  const id = document.getElementById('selectInstructivo').value;
  const version = document.getElementById('selectVersion').value;
  
  console.log('💾 Descargando Excel - ID:', id, 'Version:', version);
  
  if (id && version) {
    window.location.href = `../exportar_excel_instructivo.php?id_instructivo=${id}&version=${version}`;
  } else {
    alert("⚠️ Debe seleccionar un instructivo y versión.");
  }
}

