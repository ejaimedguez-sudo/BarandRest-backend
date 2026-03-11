param(
  [string]$Host = "localhost",
  [int]$Port = 3306,
  [string]$Database = "barandrest_platform",
  [string]$User = "barandrest_app",
  [string]$Password = "",
  [string]$OutputDir = "./backups"
)

$timestamp = Get-Date -Format "yyyyMMdd-HHmmss"
New-Item -ItemType Directory -Path $OutputDir -Force | Out-Null
$outFile = Join-Path $OutputDir "$Database-$timestamp.sql"

$mysqldump = "C:\xampp\mysql\bin\mysqldump.exe"
if (!(Test-Path $mysqldump)) {
  Write-Error "No se encontro mysqldump en $mysqldump"
  exit 1
}

$env:MYSQL_PWD = $Password
& $mysqldump -h $Host -P $Port -u $User $Database --result-file="$outFile"
if ($LASTEXITCODE -ne 0) {
  Write-Error "Backup fallo"
  exit $LASTEXITCODE
}

Write-Host "Backup generado: $outFile"
