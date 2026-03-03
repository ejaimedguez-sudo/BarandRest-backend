Param(
    [ValidateSet('start', 'stop', 'status')]
    [string]$Action = 'status',
    [string]$ProjectDir = (Split-Path -Parent $PSScriptRoot),
    [string]$PhpExe = 'php',
    [int]$Port = 8000
)

$ErrorActionPreference = 'Stop'

$stateFile = Join-Path $PSScriptRoot 'runtime_processes.json'

function Step($message) {
    Write-Host "`n==> $message" -ForegroundColor Cyan
}

function Get-State {
    if (-not (Test-Path $stateFile)) {
        return @{}
    }

    $raw = Get-Content -Path $stateFile -Raw
    if ([string]::IsNullOrWhiteSpace($raw)) {
        return @{}
    }

    $obj = $raw | ConvertFrom-Json
    $state = @{}
    foreach ($p in $obj.PSObject.Properties) {
        $state[$p.Name] = [int]$p.Value
    }
    return $state
}

function Save-State([hashtable]$state) {
    $state | ConvertTo-Json | Set-Content -Path $stateFile -Encoding UTF8
}

function Is-Running([int]$processId) {
    if ($processId -le 0) {
        return $false
    }

    $proc = Get-Process -Id $processId -ErrorAction SilentlyContinue
    return $null -ne $proc
}

function Find-ExistingProcessId([string]$pattern1, [string]$pattern2 = $null) {
    $found = Get-CimInstance Win32_Process |
        Where-Object {
            $_.CommandLine -and
            $_.CommandLine -like "*$pattern1*" -and
            ($null -eq $pattern2 -or $_.CommandLine -like "*$pattern2*")
        } |
        Select-Object -First 1

    if ($null -eq $found) {
        return 0
    }

    return [int]$found.ProcessId
}

function Ensure-Started([string]$name, [string[]]$commandArgs, [string]$pattern1, [string]$pattern2 = $null) {
    $state = Get-State
    if ($state.ContainsKey($name) -and (Is-Running $state[$name])) {
        Write-Host "$name ya está activo (PID $($state[$name]))" -ForegroundColor Yellow
        return
    }

    $existingPid = Find-ExistingProcessId -pattern1 $pattern1 -pattern2 $pattern2
    if ($existingPid -gt 0) {
        $state[$name] = $existingPid
        Save-State $state
        Write-Host "$name ya estaba activo (PID $existingPid)" -ForegroundColor Yellow
        return
    }

    $proc = Start-Process -FilePath $PhpExe -ArgumentList $commandArgs -WorkingDirectory $ProjectDir -WindowStyle Hidden -PassThru
    $state[$name] = $proc.Id
    Save-State $state
    Write-Host "$name iniciado (PID $($proc.Id))" -ForegroundColor Green
}

function Stop-Managed([string]$name) {
    $state = Get-State
    if (-not $state.ContainsKey($name)) {
        Write-Host "$name no tiene PID gestionado" -ForegroundColor Yellow
        return
    }

    $processId = [int]$state[$name]
    if (Is-Running $processId) {
        Stop-Process -Id $processId -Force -ErrorAction SilentlyContinue
        Write-Host "$name detenido (PID $processId)" -ForegroundColor Green
    } else {
        Write-Host "$name ya no estaba activo (PID $processId)" -ForegroundColor Yellow
    }

    $state.Remove($name) | Out-Null
    Save-State $state
}

function Show-Status {
    $state = Get-State

    $items = @(
        @{ Name = 'server'; Pattern1 = 'artisan serve'; Pattern2 = "--port=$Port" },
        @{ Name = 'queue'; Pattern1 = 'artisan queue:work'; Pattern2 = $null },
        @{ Name = 'scheduler'; Pattern1 = 'artisan schedule:work'; Pattern2 = $null }
    )

    foreach ($item in $items) {
        $managedPid = if ($state.ContainsKey($item.Name)) { [int]$state[$item.Name] } else { 0 }
        $isManagedRunning = $managedPid -gt 0 -and (Is-Running $managedPid)

        if ($isManagedRunning) {
            Write-Host "$($item.Name): RUNNING (PID $managedPid, gestionado)" -ForegroundColor Green
            continue
        }

        $existingPid = Find-ExistingProcessId -pattern1 $item.Pattern1 -pattern2 $item.Pattern2
        if ($existingPid -gt 0) {
            Write-Host "$($item.Name): RUNNING (PID $existingPid, no gestionado)" -ForegroundColor Yellow
        } else {
            Write-Host "$($item.Name): STOPPED" -ForegroundColor Red
        }
    }
}

if (-not (Test-Path $ProjectDir)) {
    throw "No existe ProjectDir: $ProjectDir"
}

switch ($Action) {
    'start' {
        Step 'Iniciando runtime (server + queue + scheduler)'
        Ensure-Started -name 'server' -commandArgs @('artisan', 'serve', '--host=127.0.0.1', "--port=$Port") -pattern1 'artisan serve' -pattern2 "--port=$Port"
        Ensure-Started -name 'queue' -commandArgs @('artisan', 'queue:work', '--sleep=3', '--tries=3', '--timeout=0') -pattern1 'artisan queue:work'
        Ensure-Started -name 'scheduler' -commandArgs @('artisan', 'schedule:work') -pattern1 'artisan schedule:work'
        Show-Status
    }
    'stop' {
        Step 'Deteniendo runtime gestionado'
        Stop-Managed -name 'scheduler'
        Stop-Managed -name 'queue'
        Stop-Managed -name 'server'
        Show-Status
    }
    'status' {
        Step 'Estado runtime'
        Show-Status
    }
}
