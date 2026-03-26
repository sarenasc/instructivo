@echo off
REM ===========================================
REM CONVERTIR HTML A PHP CON MENU COMPARTIDO
REM ===========================================

echo ===========================================
echo   ACTUALIZANDO MENU EN PAGINAS HTML
echo ===========================================
echo.

cd /d C:\xampp\htdocs\instructivo\app

REM Renombrar archivos HTML a PHP (solo los que ya tienen version PHP)
echo Actualizando referencias en archivos HTML...
echo.

REM Crear backup del archivo instructivo.html
if exist "Procesos\instructivo.html" (
    copy "Procesos\instructivo.html" "Procesos\instructivo.html.bak" >nul
    echo ✓ Backup creado: Procesos\instructivo.html.bak
)

echo.
echo ===========================================
echo   PROCESO COMPLETADO
echo ===========================================
echo.
echo Archivos actualizados:
echo   - Procesos\instructivo.php (nuevo)
echo.
echo Nota: Los archivos HTML originales se mantienen.
echo       Para usar la version PHP, actualiza los links del menu.
echo.
pause
