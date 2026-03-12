Param(
    [string]$BackendRoot = (Split-Path -Parent $PSScriptRoot),
    [switch]$SyncLatest,
    [string]$Branch = 'main'
)

$ErrorActionPreference = 'Stop'

function Write-Step($msg) {
    Write-Host "==> $msg" -ForegroundColor Cyan
}

if (-not (Test-Path $BackendRoot)) {
    throw "No existe el backend root: $BackendRoot"
}

Set-Location $BackendRoot

if ($SyncLatest -and (Test-Path (Join-Path $BackendRoot '..\\.git'))) {
    Write-Step "Sincronizando cambios del repositorio"
    git fetch --all
    git checkout $Branch
    git pull --ff-only
}

$releaseDir = Join-Path $BackendRoot 'release'
$installerScript = Join-Path $BackendRoot 'installer\\windows\\ordena-facil.iss'

if (-not (Test-Path $releaseDir)) {
    New-Item -ItemType Directory -Path $releaseDir | Out-Null
}

$gitHash = 'nogit'
try {
    $gitHash = (git rev-parse --short HEAD).Trim()
} catch {
    $gitHash = 'nogit'
}

$version = (Get-Date -Format 'yyyy.MM.dd.HHmm') + "-$gitHash"
$zipName = "OrdenaFacil-backend-$version.zip"
$zipPath = Join-Path $releaseDir $zipName
$stagingDir = Join-Path $releaseDir ("staging-" + $version)
$includePaths = @(
    'app',
    'bootstrap',
    'config',
    'database',
    'public',
    'resources',
    'routes',
    'scripts',
    'storage',
    'composer.json',
    'composer.lock',
    'artisan',
    '.env.example',
    'README.md',
    'README-SETUP.md',
    'README-VALIDATION.md',
    'README-REPORTS.md',
    'INSTALLATION.md'
)

Write-Step "Generando paquete ZIP actualizado"
if (Test-Path $zipPath) {
    Remove-Item $zipPath -Force
}

if (Test-Path $stagingDir) {
    Remove-Item -Recurse -Force $stagingDir
}

New-Item -ItemType Directory -Path $stagingDir | Out-Null

foreach ($entry in $includePaths) {
    $source = Join-Path $BackendRoot $entry
    $dest = Join-Path $stagingDir $entry

    if (-not (Test-Path $source)) {
        continue
    }

    if ((Get-Item $source).PSIsContainer) {
        New-Item -ItemType Directory -Path $dest -Force | Out-Null
        robocopy $source $dest /E /R:1 /W:1 /NFL /NDL /NP /XD release .git node_modules vendor /XF database.sqlite runtime_processes.json *.log | Out-Null
        $roboCode = $LASTEXITCODE
        if ($roboCode -ge 8) {
            throw "Fallo al copiar '$entry' al staging."
        }
    } else {
        Copy-Item $source $dest -Force
    }
}

Compress-Archive -Path (Join-Path $stagingDir '*') -DestinationPath $zipPath -CompressionLevel Optimal
Remove-Item -Recurse -Force $stagingDir
Write-Host "ZIP generado: $zipPath" -ForegroundColor Green

Write-Step "Intentando compilar instalador EXE (Inno Setup)"
$iscc = Get-Command iscc.exe -ErrorAction SilentlyContinue
if (-not $iscc) {
    $common = @(
        'C:\\Program Files (x86)\\Inno Setup 6\\ISCC.exe',
        'C:\\Program Files\\Inno Setup 6\\ISCC.exe'
    )
    foreach ($path in $common) {
        if (Test-Path $path) {
            $iscc = @{ Source = $path }
            break
        }
    }
}

if ($iscc) {
    & $iscc.Source "/DMyAppVersion=$version" "/DMySourceRoot=$BackendRoot\\" "/DMyOutputDir=$releaseDir" $installerScript
    if ($LASTEXITCODE -ne 0) {
        throw "La compilacion de Inno Setup fallo."
    }
    Write-Host "Instalador EXE generado en: $releaseDir" -ForegroundColor Green
} else {
    Write-Host "Inno Setup no esta instalado. Se genero solo ZIP portable." -ForegroundColor Yellow
    Write-Host "Instala Inno Setup 6 para compilar EXE automaticamente." -ForegroundColor Yellow
}

Write-Host "Proceso finalizado. Version: $version" -ForegroundColor Green
