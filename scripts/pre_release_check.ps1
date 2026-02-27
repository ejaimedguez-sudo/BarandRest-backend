Param(
    [string]$BackendPath = ""
)

$ErrorActionPreference = 'Stop'

$scriptDir = Split-Path -Parent $MyInvocation.MyCommand.Definition
if (-not $BackendPath) {
    $repoRoot = Resolve-Path (Join-Path $scriptDir '..')
    $BackendPath = Join-Path $repoRoot 'backend'
}

$envPath = Join-Path $BackendPath '.env'
if (-not (Test-Path $envPath)) {
    throw ".env no encontrado en $BackendPath"
}

Set-Location $BackendPath

$envLines = Get-Content $envPath
$kv = @{}
foreach ($line in $envLines) {
    if ($line -match '^\s*#' -or $line -notmatch '=') { continue }
    $parts = $line -split '=', 2
    $kv[$parts[0].Trim()] = $parts[1].Trim()
}

$errors = New-Object System.Collections.Generic.List[string]
$warnings = New-Object System.Collections.Generic.List[string]

function Get-Val([string]$name) {
    if ($kv.ContainsKey($name)) { return $kv[$name] }
    return ''
}

$appEnv = Get-Val 'APP_ENV'
$appDebug = (Get-Val 'APP_DEBUG').ToLowerInvariant()
$appKey = Get-Val 'APP_KEY'
$appUrl = Get-Val 'APP_URL'
$dashboardKey = Get-Val 'DASHBOARD_API_KEY'
$mailMailer = Get-Val 'MAIL_MAILER'

if (-not $appKey -or $appKey -eq 'base64:') {
    $errors.Add('APP_KEY no está configurada correctamente.')
}

if ($appEnv -eq 'production' -and $appDebug -eq 'true') {
    $errors.Add('APP_DEBUG=true en producción.')
}

if ($appEnv -eq 'production' -and -not $appUrl.StartsWith('https://')) {
    $warnings.Add('APP_URL en producción no usa https://')
}

if (-not $dashboardKey -or $dashboardKey -eq 'change_me_to_a_secure_value') {
    $errors.Add('DASHBOARD_API_KEY no está rotada.')
}

if ($mailMailer -eq 'smtp') {
    $mailUser = Get-Val 'MAIL_USERNAME'
    $mailPass = Get-Val 'MAIL_PASSWORD'
    if (-not $mailUser -or $mailUser -eq 'null' -or -not $mailPass -or $mailPass -eq 'null') {
        $warnings.Add('MAIL_MAILER=smtp sin credenciales completas.')
    }
}

$procA = Start-Process -FilePath 'php' -ArgumentList @('artisan', 'about') -NoNewWindow -Wait -PassThru
if ($procA.ExitCode -ne 0) {
    $errors.Add('php artisan about falló.')
}

$procB = Start-Process -FilePath 'php' -ArgumentList @('artisan', 'route:list') -NoNewWindow -Wait -PassThru
if ($procB.ExitCode -ne 0) {
    $errors.Add('php artisan route:list falló.')
}

if ($errors.Count -gt 0) {
    Write-Host 'PRE_RELEASE_CHECK_FAILED' -ForegroundColor Red
    foreach ($e in $errors) { Write-Host "ERROR: $e" -ForegroundColor Red }
    foreach ($w in $warnings) { Write-Host "WARN: $w" -ForegroundColor Yellow }
    exit 1
}

Write-Host 'PRE_RELEASE_CHECK_OK' -ForegroundColor Green
foreach ($w in $warnings) { Write-Host "WARN: $w" -ForegroundColor Yellow }
exit 0
