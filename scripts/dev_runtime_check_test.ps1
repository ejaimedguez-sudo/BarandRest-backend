Param(
    [string]$PhpExe = 'C:\xampp\php\php.exe',
    [int]$Port = 8000,
    [switch]$SkipTests,
    [switch]$StopOnFinish
)

$ErrorActionPreference = 'Stop'

$scriptDir = Split-Path -Parent $MyInvocation.MyCommand.Definition
$repoRoot = Resolve-Path (Join-Path $scriptDir '..')
$backendPath = Join-Path $repoRoot 'backend'
$runtimeScript = Join-Path $backendPath 'scripts\runtime.ps1'
$healthScript = Join-Path $scriptDir 'health_check.ps1'
$baseUrl = "http://127.0.0.1:$Port"

if (-not (Test-Path $PhpExe)) {
    $phpFromPath = (Get-Command php -ErrorAction SilentlyContinue)
    if ($null -ne $phpFromPath) {
        $PhpExe = $phpFromPath.Source
    } else {
        throw "No se encontró PHP. Define -PhpExe con una ruta válida."
    }
}

if (-not (Test-Path $runtimeScript)) {
    throw "No existe runtime script: $runtimeScript"
}

if (-not (Test-Path $healthScript)) {
    throw "No existe health check script: $healthScript"
}

Write-Host "== Dev runtime + health + tests ==" -ForegroundColor Cyan
Write-Host "PHP: $PhpExe"
Write-Host "Backend: $backendPath"
Write-Host "Base URL: $baseUrl"

& $runtimeScript -Action start -ProjectDir $backendPath -PhpExe $PhpExe -Port $Port

$healthLog = Join-Path $repoRoot ("storage\logs\health_check_{0:yyyyMMdd_HHmmss}.log" -f (Get-Date))
& $healthScript -PhpExe $PhpExe -BaseUrl $baseUrl -FailOnSmoke -LogPath $healthLog

if (-not $SkipTests) {
    Write-Host "`n==> Running Laravel tests" -ForegroundColor Cyan
    Push-Location $backendPath
    try {
        & $PhpExe artisan test --testsuite=Feature,Unit
    } finally {
        Pop-Location
    }
}

Write-Host "`nOK: runtime iniciado, health check y pruebas completados." -ForegroundColor Green
Write-Host "Log health check: $healthLog"

if ($StopOnFinish) {
    Write-Host "`n==> Deteniendo runtime por -StopOnFinish" -ForegroundColor Cyan
    & $runtimeScript -Action stop -ProjectDir $backendPath -PhpExe $PhpExe -Port $Port
}
