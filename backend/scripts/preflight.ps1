Param(
    [switch]$SkipNpm,
    [switch]$SkipAudit,
    [switch]$SkipHttp,
    [int]$Port = 8000
)

$ErrorActionPreference = 'Stop'

function Step($message) {
    Write-Host "`n==> $message" -ForegroundColor Cyan
}

function Run-Step($name, [scriptblock]$action) {
    Step $name
    & $action
    if ($LASTEXITCODE -ne 0) {
        throw "Falló: $name"
    }
}

$projectRoot = Split-Path -Parent $PSScriptRoot
Set-Location $projectRoot

Step "Preflight Ordena Facil (backend)"
Write-Host "Directorio: $projectRoot"

$composerCmd = $null
if (Test-Path (Join-Path $projectRoot 'composer.phar')) {
    $composerCmd = "php composer.phar"
} else {
    $composerCmd = "composer"
}

Run-Step "Instalar/verificar dependencias PHP" {
    Invoke-Expression "$composerCmd install --no-interaction"
}

if (-not $SkipNpm) {
    Run-Step "Instalar/verificar dependencias Node" {
        npm install
    }
} else {
    Step "Se omitió npm install (--SkipNpm)"
}

if (-not (Test-Path '.env')) {
    Run-Step "Crear .env desde .env.example" {
        Copy-Item .env.example .env
    }
}

Run-Step "Configurar APP_KEY (si falta)" {
    php artisan key:generate --force
}

Run-Step "Migraciones + seed" {
    php artisan migrate:fresh --seed --force
}

if (Test-Path (Join-Path $projectRoot 'public\storage')) {
    Step "Storage link ya existe (se omite)"
} else {
    Run-Step "Storage link" {
        php artisan storage:link
    }
}

Run-Step "Caches Laravel" {
    php artisan optimize:clear
    php artisan config:cache
    php artisan event:cache
    php artisan view:cache
}

Run-Step "Preparar entorno para pruebas (sin cache)" {
    # RefreshDatabase and other test helpers may invoke Artisan commands
    # that become interactive when config cache forces a non-testing env.
    php artisan optimize:clear
}

if (-not $SkipAudit) {
    Run-Step "Auditoría Composer" {
        Invoke-Expression "$composerCmd audit --no-interaction"
    }

    Run-Step "Auditoría NPM (prod)" {
        npm audit --audit-level=high --omit=dev
    }
} else {
    Step "Se omitieron auditorías (--SkipAudit)"
}

Run-Step "Estado de migraciones y scheduler" {
    php artisan migrate:status
    php artisan schedule:list
}

Run-Step "Pruebas backend" {
    php artisan test
}

Run-Step "Flujo de reporte + cola" {
    php artisan reports:daily
    php artisan queue:work --once
}

Run-Step "Restaurar caches Laravel" {
    php artisan config:cache
    php artisan event:cache
    php artisan view:cache
}

if (-not $SkipHttp) {
    Step "Prueba HTTP en /up y /api/products"
    $server = Start-Process -FilePath "php" -ArgumentList @("artisan", "serve", "--host=127.0.0.1", "--port=$Port") -PassThru -WindowStyle Hidden
    try {
        Start-Sleep -Seconds 2
        $up = Invoke-WebRequest -Uri "http://127.0.0.1:$Port/up" -UseBasicParsing
        $products = Invoke-WebRequest -Uri "http://127.0.0.1:$Port/api/products" -UseBasicParsing

        if ($up.StatusCode -ne 200 -or $products.StatusCode -ne 200) {
            throw "Health/API no devolvieron 200"
        }

        Write-Host "HTTP OK: /up=$($up.StatusCode), /api/products=$($products.StatusCode)" -ForegroundColor Green
    }
    finally {
        if ($server -and -not $server.HasExited) {
            Stop-Process -Id $server.Id -Force
        }
    }
} else {
    Step "Se omitió prueba HTTP (--SkipHttp)"
}

Write-Host "`nPreflight completado correctamente." -ForegroundColor Green
