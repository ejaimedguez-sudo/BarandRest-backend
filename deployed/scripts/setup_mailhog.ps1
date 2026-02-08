<#
Run this script to start MailHog (Docker) and set Mail settings for local testing.

Usage (PowerShell):
    .\setup_mailhog.ps1

Requirements:
- Docker installed and running
#>

Write-Host "Checking for Docker..."
$docker = Get-Command docker -ErrorAction SilentlyContinue
if (-not $docker) {
    Write-Host "Docker not found. Install Docker Desktop or run MailHog another way." -ForegroundColor Yellow
    exit 1
}

Write-Host "Starting MailHog container (mailhog/mailhog)..."
docker run -d -p 8025:8025 -p 1025:1025 --name mailhog_mailhog mailhog/mailhog | Out-Null

Write-Host "Creating or updating .env for MailHog (deployed/.env)"
$envPath = Join-Path $PSScriptRoot "..\..\.env"
if (-not (Test-Path $envPath)) {
    Write-Host "No .env file found at $envPath; creating a new .env.mailhog for manual review." -ForegroundColor Yellow
    $envPath = Join-Path $PSScriptRoot "..\..\.env.mailhog"
}

$lines = @(
    "MAIL_MAILER=smtp",
    "MAIL_HOST=127.0.0.1",
    "MAIL_PORT=1025",
    "MAIL_USERNAME=",
    "MAIL_PASSWORD=",
    "MAIL_ENCRYPTION=",
    "MAIL_FROM_ADDRESS=reports@local.test",
    "MAIL_FROM_NAME=\"BarAndRest Local\""
)

Set-Content -Path $envPath -Value ($lines -join "`n") -Encoding UTF8

Write-Host "MailHog started. Web UI: http://localhost:8025" -ForegroundColor Green
Write-Host ".env written to: $envPath" -ForegroundColor Green
Write-Host "Now run: C:\xampp\php\php.exe artisan config:clear && C:\xampp\php\php.exe artisan config:cache" -ForegroundColor Cyan
