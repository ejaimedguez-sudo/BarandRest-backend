Param(
    [string]$TaskName = "BarandRest-HealthCheck"
)

$ErrorActionPreference = 'Stop'

try {
    Unregister-ScheduledTask -TaskName $TaskName -Confirm:$false -ErrorAction Stop
    Write-Host "Tarea eliminada:" $TaskName
} catch {
    cmd /c "schtasks /Delete /TN \"$TaskName\" /F >nul 2>&1"
    Write-Host "No existe en módulo ScheduledTasks o ya estaba eliminada:" $TaskName
}

$startupTaskName = "$TaskName-Startup"
cmd /c "schtasks /Delete /TN \"$startupTaskName\" /F >nul 2>&1"
Write-Host "Tarea de inicio eliminada (si existía):" $startupTaskName
