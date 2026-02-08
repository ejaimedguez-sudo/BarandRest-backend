<#[
Creates a GitHub repository using the `GITHUB_TOKEN` environment variable.

Usage (PowerShell):
  $env:GITHUB_TOKEN = 'ghp_xxx'
  .\create_github_repo_via_token.ps1 youruser/BarandRest-backend

Notes:
- Do NOT paste tokens into chat. Revoke any token you already shared.
- This script only calls the GitHub API to create the repo and prints git
  commands to push from your machine. It does not store the token.
]#>

param(
    [Parameter(Mandatory=$true, Position=0)]
    [string]$OwnerRepo
)

$token = $env:GITHUB_TOKEN
if (-not $token) {
    Write-Error "Set environment variable GITHUB_TOKEN before running. Example:`n  $env:GITHUB_TOKEN='ghp_xxx'`nThen run: .\create_github_repo_via_token.ps1 youruser/BarandRest-backend"
    exit 1
}

$parts = $OwnerRepo.Split('/')
if ($parts.Count -ne 2) {
    Write-Error "Parameter must be in owner/repo format (e.g. youruser/BarandRest-backend)."
    exit 1
}
$owner = $parts[0]
$repo = $parts[1]

$headers = @{ Authorization = "token $token"; 'User-Agent' = 'BarAndRest-Deploy-Script' }

try {
    $user = Invoke-RestMethod -Uri 'https://api.github.com/user' -Headers $headers -Method Get
} catch {
    Write-Error "Authentication failed. Check GITHUB_TOKEN and network connectivity."
    exit 1
}

if ($user.login -eq $owner) {
    $createUri = 'https://api.github.com/user/repos'
} else {
    $createUri = "https://api.github.com/orgs/$owner/repos"
}

$body = @{ name = $repo; private = $false } | ConvertTo-Json
try {
    Invoke-RestMethod -Uri $createUri -Headers $headers -Method Post -Body $body -ContentType 'application/json' -ErrorAction Stop
    Write-Host "Repository '$OwnerRepo' created (or already exists)."
} catch {
    Write-Error "Failed to create repo: $($_.Exception.Message)"
    exit 1
}

Write-Host "\nNext steps to push your local repository to GitHub:"
Write-Host "1) Add remote (run in your git repo):"
Write-Host "   git remote add origin https://github.com/$OwnerRepo.git" -ForegroundColor Green
Write-Host "2) Push your branch as 'main' (adjust if you use a different default branch):"
Write-Host "   git push -u origin HEAD:main --force" -ForegroundColor Green

Write-Host "\nIf you prefer to push non-interactively using the token (be careful, token will appear in command history), you can run:" -ForegroundColor Yellow
Write-Host "   git push https://$($user.login):<TOKEN>@github.com/$OwnerRepo.git HEAD:main --force" -ForegroundColor Yellow

Write-Host "\nRemember to revoke any token you pasted in chat and use environment variables or local credential helpers instead." -ForegroundColor Cyan
