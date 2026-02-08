#! /usr/bin/env pwsh
# PowerShell setup script for BarAndRest (Windows)
Write-Host "Starting setup for BarAndRest..."
$root = Split-Path -Path $MyInvocation.MyCommand.Definition -Parent
Set-Location $root

# Copy example env if no .env
if (-not (Test-Path .env)) {
    Copy-Item .env.example .env -Force
    Write-Host "Created .env from .env.example"
}

# Use XAMPP PHP if available
$php = "C:\\xampp\\php\\php.exe"
if (-not (Test-Path $php)) {
    $php = "php"
}

# Composer: prefer local composer.phar if present
$composer = "composer"
if (Test-Path "$root\\composer.phar") { $composer = "$php $root\\composer.phar" }

Write-Host "Running composer install..."
& cmd /c "$composer install --no-interaction --prefer-dist"

Write-Host "Generating app key"
& cmd /c "$php artisan key:generate --ansi"

Write-Host "Running migrations"
& cmd /c "$php artisan migrate --force"

Write-Host "Creating queue table (if needed) and migrating"
try { & cmd /c "$php artisan queue:table" } catch { Write-Host "queue:table may already exist or failed" }
& cmd /c "$php artisan migrate --force"

Write-Host "Seeding test data (if TestDataSeeder exists)"
try { & cmd /c "$php artisan db:seed --class=TestDataSeeder --force" } catch { Write-Host "Seeder skipped or failed: $_" }

Write-Host "Creating storage folder for reports"
if (-not (Test-Path "storage/app/reports")) { New-Item -ItemType Directory -Path "storage/app/reports" | Out-Null }

Write-Host "Creating storage symlink"
try { & cmd /c "$php artisan storage:link" } catch { Write-Host "storage:link skipped or failed" }

Write-Host "Setup complete. To generate a report now run:`n php artisan reports:daily` and then `php artisan queue:work --once`"
Write-Host "For production, set MAIL_* env vars in .env and run a persistent queue worker."
