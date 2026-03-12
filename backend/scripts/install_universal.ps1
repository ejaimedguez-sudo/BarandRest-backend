Param(
    [string]$ProjectDir = (Split-Path -Parent $PSScriptRoot),
    [int]$Port = 8000,
    [switch]$SkipSetup
)

$ErrorActionPreference = 'Stop'

Write-Host "==> Instalador universal Ordena Facil" -ForegroundColor Cyan
Write-Host "Proyecto: $ProjectDir"

if (-not (Test-Path $ProjectDir)) {
    throw "No existe el directorio del proyecto: $ProjectDir"
}

Set-Location $ProjectDir

if (-not $SkipSetup) {
    Write-Host "==> Ejecutando setup base" -ForegroundColor Cyan
    & "$PSScriptRoot\setup.ps1"
    if ($LASTEXITCODE -ne 0) {
        throw "Fallo setup.ps1"
    }
}

Write-Host "==> Creando accesos directos" -ForegroundColor Cyan
& "$PSScriptRoot\create_desktop_shortcut.ps1" -ProjectDir $ProjectDir -Port $Port -AppPath '/' -CreateStopShortcut -CreateStartMenuShortcuts
if ($LASTEXITCODE -ne 0) {
    throw "Fallo create_desktop_shortcut.ps1"
}

Write-Host "==> Finalizado" -ForegroundColor Green
Write-Host "Para instalar en tablets/moviles abre: http://127.0.0.1:$Port/install" -ForegroundColor Yellow
Write-Host "Para uso diario en este equipo: acceso directo 'Ordena Facil - Iniciar'" -ForegroundColor Yellow
