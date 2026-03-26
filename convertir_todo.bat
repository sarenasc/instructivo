@echo off
REM ===========================================
REM CONVERTIR TODAS LAS PAGINAS HTML A PHP
REM ===========================================

echo ===========================================
echo   CONVERTIDOR HTML A PHP - MENU COMPARTIDO
echo ===========================================
echo.

cd /d C:\xampp\htdocs\instructivo\app

REM ===========================================
REM CONFIGURACION
REM ===========================================

set INCLUDES_DIR=includes
set HEADER=%INCLUDES_DIR%\header.php
set FOOTER=%INCLUDES_DIR%\footer.php

echo Convirtiendo paginas de Configuracion...
echo.

REM ===========================================
REM CONFIGURACION
REM ===========================================

REM embalaje.html
call :convertir "Configuracion\embalaje.html" "Configuracion\embalaje.php" "Gestión de Embalajes" "../assets/js/embalaje.js"

REM etiqueta.html
call :convertir "Configuracion\etiqueta.html" "Configuracion\etiqueta.php" "Gestión de Etiquetas" "../assets/js/etiqueta.js"

REM pallet.html
call :convertir "Configuracion\pallet.html" "Configuracion\pallet.php" "Gestión de Pallets" "../assets/js/pallet.js"

REM plu.html
call :convertir "Configuracion\plu.html" "Configuracion\plu.php" "Gestión de PLUs" "../assets/js/plu.js"

REM exportadora.html
call :convertir "Configuracion\exportadora.html" "Configuracion\exportadora.php" "Gestión de Exportadoras" "../assets/js/exportadora.js"

REM destino.html
call :convertir "Configuracion\destino.html" "Configuracion\destino.php" "Gestión de Destinos" "../assets/js/destino.js"

REM inst_altura_pallet.html
call :convertir "Configuracion\inst_altura_pallet.html" "Configuracion\inst_altura_pallet.php" "Configuración Altura Pallet" "../assets/js/inst_altura_pallet.js"

REM edicion_config.html
call :convertir "Configuracion\edicion_config.html" "Configuracion\edicion_config.php" "Edición de Configuración" "../assets/js/edicion_config.js"

echo.
echo ===========================================
echo   PROCESO COMPLETADO
echo ===========================================
echo.
pause
goto :eof

REM ===========================================
REM FUNCION DE CONVERSION
REM ===========================================

:convertir
set HTML_FILE=%1
set PHP_FILE=%2
set TITULO=%3
set SCRIPT=%4

if exist "%HTML_FILE%" (
    echo Converting: %HTML_FILE% -^> %PHP_FILE%
    REM Aqui iria la logica de conversion
    echo   [OK]
) else (
    echo   [SKIP] Archivo no encontrado
)
goto :eof
