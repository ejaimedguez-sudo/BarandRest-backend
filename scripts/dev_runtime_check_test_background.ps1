Param(
    [string]$PhpExe = 'C:\xampp\php\php.exe',
    [int]$Port = 8000,
    [switch]$SkipTests,
    [switch]$StopOnFinish
)

$ErrorActionPreference = 'Stop'

$scriptDir = Split-Path -Parent $MyInvocation.MyCommand.Definition
$repoRoot = Resolve-Path (Join-Path $scriptDir '..')
$flowScript = Join-Path $scriptDir 'dev_runtime_check_test.ps1'
$logsDir = Join-Path $repoRoot 'storage\logs'

if (-not (Test-Path $flowScript)) {
    throw "No existe script principal: $flowScript"
}

if (-not (Test-Path $logsDir)) {
    New-Item -ItemType Directory -Path $logsDir -Force | Out-Null
}

$stamp = Get-Date -Format 'yyyyMMdd_HHmmss'
$stdoutLogPath = Join-Path $logsDir "dev_runtime_check_test_$stamp.out.log"
$stderrLogPath = Join-Path $logsDir "dev_runtime_check_test_$stamp.err.log"

$argsList = @(
    '-NoProfile',
    '-ExecutionPolicy', 'Bypass',
    '-File', $flowScript,
    '-PhpExe', $PhpExe,
    '-Port', "$Port"
)

if ($SkipTests) {
    $argsList += '-SkipTests'
}

if ($StopOnFinish) {
    $argsList += '-StopOnFinish'
}

$proc = Start-Process -FilePath 'powershell.exe' -ArgumentList $argsList -WindowStyle Hidden -RedirectStandardOutput $stdoutLogPath -RedirectStandardError $stderrLogPath -PassThru

Write-Host "Proceso lanzado en segundo plano." -ForegroundColor Green
Write-Host "PID: $($proc.Id)"
Write-Host "Log stdout: $stdoutLogPath"
Write-Host "Log stderr: $stderrLogPath"
Write-Host "Para monitorear: Get-Content '$stdoutLogPath' -Wait"
