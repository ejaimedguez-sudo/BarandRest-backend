Param(
    [string]$LogPath = "",
    [string]$PhpExe = 'php',
    [string]$BaseUrl = 'http://127.0.0.1:8000',
    [switch]$FailOnSmoke
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
    & $PhpExe artisan migrate:status

    Write-Host "- Listing routes"
    & $PhpExe artisan route:list

    Write-Host "- Running smoke request to /up (if server is up)"
    $smokeOk = $false
    for ($attempt = 1; $attempt -le 2; $attempt++) {
        try {
            $response = Invoke-WebRequest -Uri "$BaseUrl/up" -TimeoutSec 10 -UseBasicParsing
            Write-Host "Smoke check status:" $response.StatusCode
            $smokeOk = $true
            break
        } catch {
            if ($attempt -lt 2) {
                Start-Sleep -Seconds 2
            }
        }
    }

    if (-not $smokeOk) {
        $message = "Server not listening on $BaseUrl"
        Write-Host $message
        if ($FailOnSmoke) {
            throw $message
        }
    }
} finally {
    if ($LogPath) {
        Stop-Transcript | Out-Null
    }
}
