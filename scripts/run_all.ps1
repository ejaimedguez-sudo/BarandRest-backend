Param(
    [switch]$Start,
    [switch]$SkipTests,
    [string]$HealthLogPath = ""
)

$ErrorActionPreference = 'Stop'

$scriptDir = Split-Path -Parent $MyInvocation.MyCommand.Definition
$setupScript = Join-Path $scriptDir 'setup_local.ps1'
$healthScript = Join-Path $scriptDir 'health_check.ps1'
$startScript = Join-Path $scriptDir 'start_local.ps1'

Write-Host '== BarandRest local automation (Windows) =='

Write-Host '[1/4] Running setup...'
& powershell -ExecutionPolicy Bypass -File $setupScript

if (-not $SkipTests) {
    Write-Host '[2/4] Running backend tests...'
    Set-Location (Join-Path $scriptDir '..\backend')
    php artisan test
    Set-Location $scriptDir
} else {
    Write-Host '[2/4] Skipping backend tests (--SkipTests)'
}

Write-Host '[3/4] Running health checks...'
if ($HealthLogPath) {
    & powershell -ExecutionPolicy Bypass -File $healthScript -LogPath $HealthLogPath
} else {
    & powershell -ExecutionPolicy Bypass -File $healthScript
}

if ($Start) {
    Write-Host '[4/4] Starting server and queue worker in background...'
    & powershell -ExecutionPolicy Bypass -File $startScript
} else {
    Write-Host '[4/4] Start skipped. Use -Start to launch server and worker.'
}

Write-Host 'Automation completed successfully.'
