<?php
$titulo_pagina = 'Exportar Instructivo';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="card mb-4">
        <div class="card-body"><h5>Exportar Instructivo a Excel</h5>
            <div class="col-md-4">
              <label for="selectInstructivo" class="form-label">Selecciona Instructivo</label>
              <select id="selectInstructivo" name="selectInstructivo" class="form-select"></select>
            </div>
            <div class="row mb-3"></div>
                <div class="col-md-4">
                    <label for="selectVersion" class="form-label">Especie</label>
                    <select id="selectVersion" name="selectVersion" class="form-select"></select>
                </div>
            </div>
            
        </div>
        <button onclick="descargarExcel()">Descargar Excel</button>
    </div>

<?php
$scripts_extra = '<script src="../assets/js/exportar_instructivo.js"></script>';
require_once __DIR__ . '/../includes/footer.php';
?>

