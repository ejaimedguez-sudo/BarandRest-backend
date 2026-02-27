Param()

$ErrorActionPreference = 'Stop'

$scriptDir = Split-Path -Parent $MyInvocation.MyCommand.Definition
$repoRoot = Resolve-Path (Join-Path $scriptDir '..')
$backendPath = Join-Path $repoRoot 'backend'

Set-Location $backendPath
Write-Host "== Setup local development environment (Windows) =="

$php = 'C:\xampp\php\php.exe'
if (-not (Test-Path $php)) {
    $php = 'php'
}

if (Test-Path 'composer.phar') {
    $composerCmd = "$php composer.phar"
} else {
    $composerCmd = 'composer'
}

if (-not (Test-Path '.env') -and (Test-Path '.env.example')) {
    Copy-Item '.env.example' '.env'
    Write-Host "Created .env from .env.example"
}

Write-Host "Running composer install..."
& cmd /c "$composerCmd install --no-interaction --prefer-dist"

Write-Host "Generating app key..."
try {
    & cmd /c "$php artisan key:generate --force"
} catch {
    Write-Host "key:generate failed or already present"
}

if (-not (Test-Path 'database')) {
    New-Item -ItemType Directory -Path 'database' | Out-Null
}

$db = 'database/database.sqlite'
if (-not (Test-Path $db)) {
    New-Item -ItemType File -Path $db | Out-Null
    Write-Host "Created $db"
}

Write-Host "Running migrations and seeders..."
& cmd /c "$php artisan migrate --force"
& cmd /c "$php artisan db:seed --force"

Write-Host "Clearing and caching configs..."
& cmd /c "$php artisan optimize:clear"
& cmd /c "$php artisan config:cache"
& cmd /c "$php artisan route:cache"
& cmd /c "$php artisan view:cache"

Write-Host "Setup complete. Start app with scripts/start_local.ps1"
