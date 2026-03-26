@echo off
REM ===========================================
REM GENERAR ARCHIVOS PHP PARA CONFIGURACIONES
REM ===========================================

cd /d C:\xampp\htdocs\instructivo\app

echo ===========================================
echo   GENERANDO ARCHIVOS PHP
echo ===========================================
echo.

REM Tablas a procesar
set TABLAS=embalaje etiqueta pallet plu exportadora destino inst_altura_pallet

for %%T in (%TABLAS%) do (
    echo Generando archivos para %%T...
    
    REM Crear obtener_%%T.php
    (
        echo ^<?php
        echo require_once^("../conexion.php"^);
        echo.
        echo header^('Content-Type: application/json'^);
        echo.
        echo $sql = "SELECT * FROM %%T ORDER BY 1";
        echo $stmt = sqlsrv_query^($conn, $sql^);
        echo.
        echo $resultados = [^];
        echo.
        echo if ^($stmt^) {
        echo     while ^($row = sqlsrv_fetch_array^($stmt, SQLSRV_FETCH_ASSOC^)^) {
        echo         $resultados[^] = $row;
        echo     }
        echo     echo json_encode^($resultados^);
        echo ^} else {
        echo     echo json_encode^([^]^);
        echo ^}
        echo ?^>
    ) > "obtener_%%T.php"
    
    echo   [OK] obtener_%%T.php
)

echo.
echo ===========================================
echo   COMPLETADO
echo ===========================================
pause
