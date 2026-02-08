param()

# Setup script for Windows PowerShell: install deps, migrate, create queue table, storage link
Write-Host "Running setup.ps1..."

$php = "C:\\xampp\\php\\php.exe"
$composer = "composer.phar"
if (-not (Test-Path $php)) {
    Write-Error "PHP not found at $php. Please ensure XAMPP PHP is installed and path is correct."
    exit 1
}

Set-Location $PSScriptRoot + "\\.."

if (-not (Test-Path vendor)) {
    if (-not (Test-Path $composer)) {
        Write-Host "Downloading composer.phar..."
        Invoke-WebRequest -Uri https://getcomposer.org/composer-stable.phar -OutFile composer.phar
    }
    & $php composer.phar install
} else {
    Write-Host "vendor/ exists, skipping composer install."
}

if (-not (Test-Path .env)) {
    Copy-Item .env.example .env
    & $php artisan key:generate --force
}

Write-Host "Creating storage link and reports folder..."
& $php artisan storage:link 2>$null
New-Item -ItemType Directory -Force -Path storage\app\reports | Out-Null

Write-Host "Creating queue table and running migrations..."
& $php artisan queue:table 2>$null
& $php artisan migrate --force

Write-Host "Setup complete. To run the queue worker locally: php artisan queue:work"
