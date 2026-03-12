Param(
    [string]$ProjectDir = (Split-Path -Parent $PSScriptRoot),
    [int]$Port = 8000,
    [string]$AppPath = '/dashboard',
    [string]$ShortcutName = 'BarandRest - Iniciar',
    [switch]$CreateStopShortcut,
    [switch]$CreateStartMenuShortcuts,
    [string]$IconPath = ''
)

$ErrorActionPreference = 'Stop'

function New-Shortcut {
    param(
        [Parameter(Mandatory = $true)][string]$Path,
        [Parameter(Mandatory = $true)][string]$TargetPath,
        [Parameter(Mandatory = $true)][string]$Arguments,
        [Parameter(Mandatory = $true)][string]$WorkingDirectory,
        [Parameter(Mandatory = $true)][string]$Description,
        [string]$IconLocation
    )

    $shell = New-Object -ComObject WScript.Shell
    $shortcut = $shell.CreateShortcut($Path)
    $shortcut.TargetPath = $TargetPath
    $shortcut.Arguments = $Arguments
    $shortcut.WorkingDirectory = $WorkingDirectory
    $shortcut.Description = $Description
    $shortcut.WindowStyle = 7

    if (-not [string]::IsNullOrWhiteSpace($IconLocation)) {
        $shortcut.IconLocation = $IconLocation
    }

    $shortcut.Save()
}

if (-not (Test-Path $ProjectDir)) {
    throw "No existe el directorio del proyecto: $ProjectDir"
}

$desktop = [Environment]::GetFolderPath('Desktop')
if ([string]::IsNullOrWhiteSpace($desktop)) {
    throw 'No se pudo resolver la ruta del Escritorio.'
}

$startMenuPrograms = Join-Path $env:APPDATA 'Microsoft\Windows\Start Menu\Programs'
if ([string]::IsNullOrWhiteSpace($startMenuPrograms)) {
    throw 'No se pudo resolver la ruta del Menu Inicio.'
}

$startMenuFolder = Join-Path $startMenuPrograms 'BarandRest'
if ($CreateStartMenuShortcuts -and -not (Test-Path $startMenuFolder)) {
    New-Item -ItemType Directory -Path $startMenuFolder | Out-Null
}

$runtimeScript = Join-Path $PSScriptRoot 'runtime.ps1'
if (-not (Test-Path $runtimeScript)) {
    throw "No existe runtime.ps1 en: $runtimeScript"
}

$defaultIconPath = Join-Path $PSScriptRoot 'assets\barandrest.ico'
if ([string]::IsNullOrWhiteSpace($IconPath) -and (Test-Path $defaultIconPath)) {
    $IconPath = $defaultIconPath
}

$phpExe = (Get-Command php -ErrorAction SilentlyContinue)
$icon = if (-not [string]::IsNullOrWhiteSpace($IconPath) -and (Test-Path $IconPath)) {
    $IconPath
} elseif ($phpExe) {
    $phpExe.Source
} else {
    "$env:SystemRoot\System32\shell32.dll,220"
}

$startShortcutPath = Join-Path $desktop ("{0}.lnk" -f $ShortcutName)
$normalizedAppPath = if ([string]::IsNullOrWhiteSpace($AppPath)) { '/' } elseif ($AppPath.StartsWith('/')) { $AppPath } else { "/$AppPath" }
$appUrl = "http://127.0.0.1:$Port$normalizedAppPath"
$startArguments = "-NoProfile -ExecutionPolicy Bypass -Command `"& '$runtimeScript' -Action start -ProjectDir '$ProjectDir' -Port $Port; Start-Sleep -Seconds 2; Start-Process '$appUrl'`""

$startShortcutParams = @{
    Path = $startShortcutPath
    TargetPath = 'powershell.exe'
    Arguments = $startArguments
    WorkingDirectory = $ProjectDir
    Description = 'Inicia BarandRest (server + queue + scheduler) y abre el navegador.'
    IconLocation = $icon
}

New-Shortcut @startShortcutParams

Write-Host "Acceso directo creado: $startShortcutPath" -ForegroundColor Green

if ($CreateStartMenuShortcuts) {
    $startMenuStartPath = Join-Path $startMenuFolder ("{0}.lnk" -f $ShortcutName)

    $startMenuStartParams = @{
        Path = $startMenuStartPath
        TargetPath = 'powershell.exe'
        Arguments = $startArguments
        WorkingDirectory = $ProjectDir
        Description = 'Inicia BarandRest (server + queue + scheduler) y abre el navegador.'
        IconLocation = $icon
    }

    New-Shortcut @startMenuStartParams
    Write-Host "Acceso directo Menu Inicio creado: $startMenuStartPath" -ForegroundColor Green
}

if ($CreateStopShortcut) {
    $stopShortcutPath = Join-Path $desktop 'BarandRest - Detener.lnk'
    $stopArguments = "-NoProfile -ExecutionPolicy Bypass -Command `"& '$runtimeScript' -Action stop -ProjectDir '$ProjectDir' -Port $Port`""

    $stopShortcutParams = @{
        Path = $stopShortcutPath
        TargetPath = 'powershell.exe'
        Arguments = $stopArguments
        WorkingDirectory = $ProjectDir
        Description = 'Detiene procesos gestionados de BarandRest.'
        IconLocation = "$env:SystemRoot\System32\shell32.dll,131"
    }

    New-Shortcut @stopShortcutParams

    Write-Host "Acceso directo creado: $stopShortcutPath" -ForegroundColor Green

    if ($CreateStartMenuShortcuts) {
        $startMenuStopPath = Join-Path $startMenuFolder 'BarandRest - Detener.lnk'

        $startMenuStopParams = @{
            Path = $startMenuStopPath
            TargetPath = 'powershell.exe'
            Arguments = $stopArguments
            WorkingDirectory = $ProjectDir
            Description = 'Detiene procesos gestionados de BarandRest.'
            IconLocation = "$env:SystemRoot\System32\shell32.dll,131"
        }

        New-Shortcut @startMenuStopParams
        Write-Host "Acceso directo Menu Inicio creado: $startMenuStopPath" -ForegroundColor Green
    }
}
