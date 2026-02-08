<#
Auto-run helper: intenta continuar un rebase, sincronizar con remoto usando el PAT
almacenado en DEPLOY_SECRETS.md, crear un trigger para Actions, y descargar
los logs del último run `CI`.

Ejecución:
  powershell -NoProfile -ExecutionPolicy Bypass -File .\scripts\auto_run_ci_fix.ps1

Nota: Si aparecen conflictos no resueltos distintos a `.github/workflows/ci.yml`
el script se detendrá y te pedirá resolverlos manualmente para evitar pérdida.
#>

Set-StrictMode -Version Latest
$ErrorActionPreference = 'Stop'

function Get-Pat {
    $path = Join-Path $PSScriptRoot '..\DEPLOY_SECRETS.md' | Resolve-Path -ErrorAction SilentlyContinue
    if (-not $path) { Write-Error 'No se encontró DEPLOY_SECRETS.md en el repo.'; return $null }
    $txt = Get-Content -Raw -Path $path
    $one = ($txt -replace "\r\n","")
    if ($one -match 'ghp_[A-Za-z0-9]+') { return $Matches[0] }
    Write-Error 'No se pudo extraer un PAT de DEPLOY_SECRETS.md'
    return $null
}

Push-RepoUsingPat {
    param($pat)
    $repoUrl = "https://$($pat)@github.com/ejaimedguez-sudo/BarandRest-backend.git"
    Write-Output "Pushing to $repoUrl (token hidden)"
    try {
        git push $repoUrl HEAD:main
        return $true
    } catch {
        Write-Warning "Push falló: $($_.Exception.Message)"
        Write-Output 'Intentando git pull --rebase y reintentar push...'
        try {
            git pull --rebase $repoUrl main
            git push $repoUrl HEAD:main
            return $true
        } catch {
            Write-Error "Push falló de nuevo: $($_.Exception.Message)"
            return $false
        }
    }
}

Write-Output '1) Comprobando estado git local'
git status --porcelain -b | Write-Output

# Si hay rebase en curso, intentar continuar
$maxLoops = 10
$loop = 0
$rebaseContinued = $false
while ($loop -lt $maxLoops) {
    $loop++
    $status = git status --porcelain -b 2>$null
    if ($status -match 'rebase' -or Test-Path .git/rebase-apply -ErrorAction SilentlyContinue -or Test-Path .git/rebase-merge -ErrorAction SilentlyContinue) {
        Write-Output "Rebase en curso detectado (iteración $loop). Intentando 'git add .github/workflows/ci.yml' y 'git rebase --continue'"
        try {
            git add .github/workflows/ci.yml 2>$null
            git rebase --continue
            $rebaseContinued = $true
            Start-Sleep -Milliseconds 300
            continue
        } catch {
            Write-Warning "git rebase --continue falló: $($_.Exception.Message)"
            break
        }
    } else {
        break
    }
}

# Comprobar conflictos no resueltos
$unmerged = (git ls-files -u) -join "`n"
if ($unmerged) {
    Write-Error "Hay conflictos no resueltos. Archivos en conflicto:\n$unmerged"
    Write-Output 'Si el único conflicto es `.github/workflows/ci.yml`, añade el archivo resuelto con `git add .github/workflows/ci.yml` y ejecuta `git rebase --continue`.'
    exit 1
}

Write-Output '2) Asegurando que la rama local está actualizada y subiendo cambios'
$pat = Get-Pat
if (-not $pat) { Write-Error 'PAT no disponible. Abortando.'; exit 1 }

$ok = Push-RepoUsingPat -pat $pat
if (-not $ok) { Write-Error 'No fue posible empujar los cambios. Revisa conflictos y permisos.'; exit 1 }

Write-Output '3) Creando trigger commit para forzar workflow (usando script existente)'
$triggerScript = Join-Path $PSScriptRoot 'create_trigger_commit.ps1'
if (Test-Path $triggerScript) {
    try { powershell -NoProfile -ExecutionPolicy Bypass -File $triggerScript } catch { Write-Warning "Fallo al ejecutar create_trigger_commit.ps1: $($_.Exception.Message)" }
} else { Write-Warning 'No existe scripts\create_trigger_commit.ps1; no se creó trigger.' }

Write-Output '4) Consultando últimos runs y descargando logs del CI más reciente'
$checkScript = Join-Path $PSScriptRoot 'check_actions_runs.ps1'
$downloadScript = Join-Path $PSScriptRoot 'download_run_logs.ps1'
$ciId = $null
if (Test-Path $checkScript) {
    try {
        $json = & powershell -NoProfile -ExecutionPolicy Bypass -File $checkScript | Out-String
        $runs = $json | ConvertFrom-Json
        if ($runs -is [System.Array]) { $first = $runs | Where-Object { $_.name -eq 'CI' } | Select-Object -First 1 }
        else { $first = $runs }
        if ($first) { $ciId = $first.id; Write-Output "Found CI run id: $ciId" }
        else { Write-Warning 'No se encontró run CI en la lista.' }
    } catch {
        Write-Warning "Error al consultar runs: $($_.Exception.Message)"
    }
} else { Write-Warning 'No existe scripts\check_actions_runs.ps1' }

if ($ciId -and (Test-Path $downloadScript)) {
    try {
        Write-Output "Descargando logs para run $ciId"
        powershell -NoProfile -ExecutionPolicy Bypass -File $downloadScript $ciId
        Write-Output "Logs descargados en scripts\logs_$ciId"
        Write-Output 'Revisa scripts\logs_<id>\php\4_Install dependencies.txt para el fallo exacto.'
    } catch {
        Write-Warning "Fallo al descargar logs: $($_.Exception.Message)"
    }
} else {
    if (-not $ciId) { Write-Warning 'No se obtuvo ID de run CI; omitiendo descarga de logs.' }
}

Write-Output 'Hecho. Si hubo errores, pégame aquí la salida para que la analice.'
