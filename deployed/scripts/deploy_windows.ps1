param(
    [string]$ZipPath = "backend_release_clean.zip",
    [string]$TargetDir = "C:\inetpub\wwwroot\barandrest"
)

Write-Host "Deploying $ZipPath to $TargetDir"

if (-Not (Test-Path $TargetDir)) { New-Item -ItemType Directory -Path $TargetDir | Out-Null }

Add-Type -AssemblyName System.IO.Compression.FileSystem
[System.IO.Compression.ZipFile]::ExtractToDirectory($ZipPath, $TargetDir, $true)
Set-Location $TargetDir

if (Test-Path .\composer.phar) {
    php .\composer.phar install --no-dev --optimize-autoloader
} else {
    composer install --no-dev --optimize-autoloader
}

if (-Not (Test-Path .\.env)) { Copy-Item .\.env.example .\.env }

php artisan key:generate --force
php artisan migrate --force
php artisan db:seed --force
php artisan storage:link -n || Write-Host "storage:link skipped"

Write-Host "Deployment complete. Start a worker using nssm or run: php artisan queue:work"
