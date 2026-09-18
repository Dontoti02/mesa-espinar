@echo off
chcp 65001 > nul
title Compartir Mesa de Partes con Cliente (Túnel Cloudflare)
echo ===================================================================
echo     COMPARTIR MESA DE PARTES CON TU CLIENTE (TÚNEL PÚBLICO)
echo ===================================================================
echo.
if not exist "%~dp0scratch\cloudflared.exe" (
    echo [INFO] Descargando componente seguro Cloudflare Tunnel...
    if not exist "%~dp0scratch" mkdir "%~dp0scratch"
    curl.exe -s -L -o "%~dp0scratch\cloudflared.exe" https://github.com/cloudflare/cloudflared/releases/latest/download/cloudflared-windows-amd64.exe
)
echo Conectando con Cloudflare Tunnel...
echo Espera unos segundos hasta ver la linea con 'https://...trycloudflare.com'
echo.
echo Presiona Ctrl+C cuando quieras detener el acceso a tu cliente.
echo ===================================================================
echo.
"%~dp0scratch\cloudflared.exe" tunnel --url http://localhost:80
pause
