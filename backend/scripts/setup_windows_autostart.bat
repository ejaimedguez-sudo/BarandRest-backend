@echo off
setlocal

powershell -NoProfile -ExecutionPolicy Bypass -File "%~dp0setup_windows_autostart.ps1" -RunNow %*
exit /b %ERRORLEVEL%
