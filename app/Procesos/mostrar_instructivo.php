<?php
$titulo_pagina = 'Instructivo DinÃ¡mico';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container mt-4">
    <h1>Instructivo DinÃ¡mico</h1>
    <h5>Selecciona una versiÃ³n</h5>

    <label>Exportadora:</label>
    <div class="row g-2">
        <div class="col-md-4">
            <select id="exportadoraSelect" class="form-select"></select>
        </div>

        <label>Instructivo:</label>
        <div class="col-md-4">
            <select id="instructivoSelect" class="form-select"></select>
        </div>

        <label>VersiÃ³n:</label>
        <div class="col-md-4">
            <select id="versionSelect" class="form-select"></select>
        </div>
    </div>
</div>

<script>
document.getElementById('versionSelect').addEventListener('change', () => {
    const id_instructivo = document.getElementById('instructivoSelect').value;
    const version = document.getElementById('versionSelect').value;

    // Redirigir a nueva pÃ¡gina con parÃ¡metros
    window.location.href = `detalle.html?id_instructivo=${id_instructivo}&version=${version}`;
});
</script>

<?php
$scripts_extra = '<script src="../assets/js/scripts.js"></script>';
require_once __DIR__ . '/../includes/footer.php';
?>

