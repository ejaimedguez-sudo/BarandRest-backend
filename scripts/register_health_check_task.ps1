Param(
    [string]$TaskName = "BarandRest-HealthCheck",
    [int]$IntervalMinutes = 15,
    [switch]$RunAtStartup
)

$ErrorActionPreference = 'Stop'

if ($IntervalMinutes -lt 1) {
    throw "IntervalMinutes debe ser mayor o igual a 1"
}

$repoRoot = Resolve-Path (Join-Path $PSScriptRoot "..")
$scriptPath = Join-Path $repoRoot "scripts\health_check.ps1"
$logDir = Join-Path $repoRoot "backend\storage\logs"
$logPath = Join-Path $logDir "health_check_task.log"

if (-not (Test-Path $scriptPath)) {
    throw "No se encontró el script: $scriptPath"
}

if (-not (Test-Path $logDir)) {
    New-Item -ItemType Directory -Path $logDir -Force | Out-Null
}

$taskCommand = "powershell.exe -NoProfile -ExecutionPolicy Bypass -File `"$scriptPath`" -LogPath `"$logPath`""

cmd /c "schtasks /Delete /TN \"$TaskName\" /F >nul 2>&1"

& schtasks /Create /F /SC MINUTE /MO $IntervalMinutes /TN $TaskName /TR $taskCommand | Out-Null
if ($LASTEXITCODE -ne 0) {
    throw "No se pudo crear la tarea periódica '$TaskName'"
}

if ($RunAtStartup) {
    $startupTaskName = "$TaskName-Startup"
    cmd /c "schtasks /Delete /TN \"$startupTaskName\" /F >nul 2>&1"
    & schtasks /Create /F /SC ONSTART /TN $startupTaskName /TR $taskCommand | Out-Null
    if ($LASTEXITCODE -eq 0) {
        Write-Host "Tarea de inicio creada/actualizada:" $startupTaskName
    } else {
        Write-Host "No se pudo crear la tarea de inicio (posible falta de permisos):" $startupTaskName
    }
}

Write-Host "Tarea programada creada/actualizada:" $TaskName
Write-Host "Intervalo:" $IntervalMinutes "min"
Write-Host "Log:" $logPath
