Param()
Write-Host "== Setup local development environment (Windows) =="
if (Test-Path composer.phar) {
    $composer = "php composer.phar"
} else {
    $composer = "composer"
}
if (-not (Test-Path .env) -and (Test-Path .env.example)) {
    Copy-Item .env.example .env
    Write-Host "Created .env from .env.example"
}
Write-Host "Running composer install..."
& cmd /c "$composer install --no-interaction --prefer-dist"
Write-Host "Generating app key..."
php artisan key:generate || Write-Host "key:generate failed or already present"
if (-not (Test-Path database)) { New-Item -ItemType Directory -Path database | Out-Null }
$db = "database/database.sqlite"
if (-not (Test-Path $db)) { New-Item -ItemType File -Path $db | Out-Null; Write-Host "Created $db" }
Write-Host "Running migrations and seeders..."
php artisan migrate --force
php artisan db:seed --force
Write-Host "Clearing and caching configs..."
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
Write-Host "Setup complete. Start app with scripts/start_local.ps1"
