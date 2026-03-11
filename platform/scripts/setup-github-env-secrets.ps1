param(
  [Parameter(Mandatory=$true)]
  [ValidateSet("staging", "production")]
  [string]$Environment,

  [Parameter(Mandatory=$true)]
  [string]$DeployHost,

  [Parameter(Mandatory=$true)]
  [string]$DeployUser,

  [Parameter(Mandatory=$true)]
  [string]$DeployAppPath,

  [Parameter(Mandatory=$true)]
  [string]$SshKeyPath,

  [Parameter(Mandatory=$true)]
  [string]$BackendEnvPath,

  [string]$Repo = "ejaimedguez-sudo/BarandRest-backend",
  [int]$DeployPort = 22
)

if (!(Get-Command gh -ErrorAction SilentlyContinue)) {
  Write-Error "GitHub CLI (gh) no esta instalado"
  exit 1
}

if (!(Test-Path $SshKeyPath)) {
  Write-Error "No existe archivo de llave SSH: $SshKeyPath"
  exit 1
}

if (!(Test-Path $BackendEnvPath)) {
  Write-Error "No existe archivo .env backend: $BackendEnvPath"
  exit 1
}

$sshKeyValue = Get-Content $SshKeyPath -Raw
$backendEnvValue = Get-Content $BackendEnvPath -Raw

$sshKeyValue | gh secret set DEPLOY_SSH_KEY --env $Environment -R $Repo
$DeployHost | gh secret set DEPLOY_SSH_HOST --env $Environment -R $Repo
$DeployUser | gh secret set DEPLOY_SSH_USER --env $Environment -R $Repo
$DeployAppPath | gh secret set DEPLOY_APP_PATH --env $Environment -R $Repo
$DeployPort.ToString() | gh secret set DEPLOY_SSH_PORT --env $Environment -R $Repo
$backendEnvValue | gh secret set BACKEND_ENV_FILE --env $Environment -R $Repo

Write-Host "Secrets configurados para environment '$Environment' en repo '$Repo'."
Write-Host "Siguiente paso: ejecutar workflow 'Platform Deploy' con runSmoke=true en staging."
