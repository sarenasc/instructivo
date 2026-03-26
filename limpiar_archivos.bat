@echo off
REM ===========================================
REM LIMPIEZA DE ARCHIVOS DUPLICADOS / BACKUPS
REM ===========================================
REM Este script elimina archivos de backup y temporales
REM que no deberían estar en el servidor de producción

echo ===========================================
echo   LIMPIEZA DE ARCHIVOS DUPLICADOS
echo ===========================================
echo.
echo Este script eliminara:
echo - Archivos *BCK*.php
echo - Archivos .bak, .tmp
echo - Archivos zip y csv temporales
echo.
echo ¿Continuar? (S/N)
set /p confirm=

if /i not "%confirm%"=="S" (
    echo.
    echo Operacion cancelada.
    pause
    exit /b
)

echo.
echo Eliminando archivos...
echo.

cd /d C:\xampp\htdocs\instructivo

REM Eliminar backups de PHP
del /Q *BCK.php 2>nul
del /Q *BCK1.php 2>nul
del /Q *BCK2.php 2>nul
del /Q *.bak 2>nul
del /Q *.tmp 2>nul

REM Eliminar archivos temporales
del /Q instructivo.zip 2>nul
del /Q datos_paginados.csv 2>nul
del /Q procesos_estiba_completa.xlsx 2>nul

echo.
echo ===========================================
echo   LIMPIEZA COMPLETADA
echo ===========================================
echo.
echo Archivos eliminados:
echo - *BCK*.php
echo - *.bak
echo - *.tmp
echo - instructivo.zip
echo - datos_paginados.csv
echo - procesos_estiba_completa.xlsx
echo.
echo NOTA: Los archivos vendor/, node_modules/ NO se eliminan
echo       porque son dependencias necesarias.
echo.
pause
