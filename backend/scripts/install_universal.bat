@echo off
setlocal
powershell -NoProfile -ExecutionPolicy Bypass -File "%~dp0install_universal.ps1" %*
endlocal
