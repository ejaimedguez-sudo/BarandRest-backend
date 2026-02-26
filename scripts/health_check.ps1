Param(
    [string]$LogPath = ""
)

$ErrorActionPreference = 'Stop'

if ($LogPath) {
    $logDirectory = Split-Path -Parent $LogPath
    if ($logDirectory -and -not (Test-Path $logDirectory)) {
        New-Item -ItemType Directory -Path $logDirectory -Force | Out-Null
    }
    Start-Transcript -Path $LogPath -Append | Out-Null
}

try {
    Write-Host "Running basic health checks"

    Set-Location "$PSScriptRoot\..\backend"

    Write-Host "- Checking migrations status"
    php artisan migrate:status

    Write-Host "- Listing routes"
    php artisan route:list

    Write-Host "- Running smoke request to /up (if server is up)"
    try {
        $response = Invoke-WebRequest -Uri "http://127.0.0.1:8000/up" -TimeoutSec 5 -UseBasicParsing
        Write-Host "Smoke check status:" $response.StatusCode
    } catch {
        Write-Host "Server not listening on 127.0.0.1:8000"
    }
} finally {
    if ($LogPath) {
        Stop-Transcript | Out-Null
    }
}
