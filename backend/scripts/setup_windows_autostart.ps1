Param(
    [string]$ProjectDir = "C:\xampp\htdocs\apps\OrdenaFacil\backend",
    [string]$PhpExe = "php",
    [int]$Port = 8000,
    [switch]$RunNow
)

$ErrorActionPreference = 'Stop'

function Step($message) {
    Write-Host "`n==> $message" -ForegroundColor Cyan
}

function Register-Task($taskName, $command) {
    $fullTaskName = "OrdenaFacil\$taskName"
    & schtasks /Create /F /SC ONLOGON /TN $fullTaskName /TR $command | Out-Null
    if ($LASTEXITCODE -ne 0) {
        throw "No se pudo registrar tarea: $fullTaskName"
    }
    Write-Host "Registrada: $fullTaskName" -ForegroundColor Green
}

function Run-Task($taskName) {
    $fullTaskName = "OrdenaFacil\$taskName"
    & schtasks /Run /TN $fullTaskName | Out-Null
    if ($LASTEXITCODE -ne 0) {
        throw "No se pudo ejecutar tarea: $fullTaskName"
    }
    Write-Host "Ejecutada: $fullTaskName" -ForegroundColor Green
}

function Create-StartupLauncher($fileName, $command) {
    $startupDir = Join-Path $env:APPDATA 'Microsoft\Windows\Start Menu\Programs\Startup'
    if (-not (Test-Path $startupDir)) {
        New-Item -ItemType Directory -Path $startupDir | Out-Null
    }

    $launcherPath = Join-Path $startupDir $fileName
    $content = ("@echo off`r`nstart `"`" /min cmd /c `"{0}`"`r`n" -f $command)
    Set-Content -Path $launcherPath -Value $content -Encoding ASCII
    Write-Host "Startup creado: $launcherPath" -ForegroundColor Green
}

if (-not (Test-Path $ProjectDir)) {
    throw "No existe el directorio del proyecto: $ProjectDir"
}

Step "Configurando autoarranque Windows para Ordena Facil"
Write-Host "ProjectDir: $ProjectDir"
Write-Host "PHP: $PhpExe"

$serverCmd = "cmd.exe /c cd /d $ProjectDir && $PhpExe artisan serve --host=127.0.0.1 --port=$Port"
$workerCmd = "cmd.exe /c cd /d $ProjectDir && $PhpExe artisan queue:work --sleep=3 --tries=3 --timeout=0"
$schedulerCmd = "cmd.exe /c cd /d $ProjectDir && $PhpExe artisan schedule:work"

$tasksConfigured = $true
try {
    Register-Task -taskName "LaravelServer" -command $serverCmd
    Register-Task -taskName "LaravelQueueWorker" -command $workerCmd
    Register-Task -taskName "LaravelScheduler" -command $schedulerCmd

    if ($RunNow) {
        Step "Ejecutando tareas ahora"
        Run-Task -taskName "LaravelServer"
        Start-Sleep -Seconds 1
        Run-Task -taskName "LaravelQueueWorker"
        Start-Sleep -Seconds 1
        Run-Task -taskName "LaravelScheduler"
    }

    Step "Resumen tareas programadas"
    & schtasks /Query /TN "OrdenaFacil\LaravelServer" /FO LIST /V
    & schtasks /Query /TN "OrdenaFacil\LaravelQueueWorker" /FO LIST /V
    & schtasks /Query /TN "OrdenaFacil\LaravelScheduler" /FO LIST /V
}
catch {
    $tasksConfigured = $false
    Write-Warning "No se pudo configurar Task Scheduler (probable falta de permisos). Se aplicará fallback por Startup del usuario."

    Create-StartupLauncher -fileName "ordena_facil_server_autostart.cmd" -command $serverCmd
    Create-StartupLauncher -fileName "ordena_facil_worker_autostart.cmd" -command $workerCmd
    Create-StartupLauncher -fileName "ordena_facil_scheduler_autostart.cmd" -command $schedulerCmd

    if ($RunNow) {
        Step "Ejecutando procesos ahora (fallback Startup)"
        Start-Process -FilePath "cmd.exe" -ArgumentList "/c $serverCmd" -WindowStyle Minimized
        Start-Sleep -Seconds 1
        Start-Process -FilePath "cmd.exe" -ArgumentList "/c $workerCmd" -WindowStyle Minimized
        Start-Sleep -Seconds 1
        Start-Process -FilePath "cmd.exe" -ArgumentList "/c $schedulerCmd" -WindowStyle Minimized
    }
}

if ($tasksConfigured) {
    Write-Host "`nAutoarranque configurado con Task Scheduler." -ForegroundColor Green
} else {
    Write-Host "`nAutoarranque configurado con Startup del usuario (sin admin)." -ForegroundColor Green
}
