Param(
    [string]$ProjectDir = (Split-Path -Parent $PSScriptRoot),
    [int]$Port = 8000,
    [switch]$SkipGitPull
)

$ErrorActionPreference = 'Stop'

Write-Host "==> Actualizador Ordena Facil" -ForegroundColor Cyan
Write-Host "Proyecto: $ProjectDir"

if (-not (Test-Path $ProjectDir)) {
    throw "No existe el directorio del proyecto: $ProjectDir"
}

Set-Location $ProjectDir

$repoRoot = Resolve-Path (Join-Path $ProjectDir '..')
if (-not $SkipGitPull -and (Test-Path (Join-Path $repoRoot '.git'))) {
    Write-Host "==> Sincronizando codigo mas reciente" -ForegroundColor Cyan
    git -C $repoRoot fetch --all
    git -C $repoRoot pull --ff-only
}

Write-Host "==> Reaplicando instalacion local" -ForegroundColor Cyan
& "$PSScriptRoot\install_universal.ps1" -ProjectDir $ProjectDir -Port $Port
if ($LASTEXITCODE -ne 0) {
    throw "Fallo install_universal.ps1"
}

Write-Host "Actualizacion completada." -ForegroundColor Green
