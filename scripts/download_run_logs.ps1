param([string]$runId = '21790196969')
$ErrorActionPreference='Stop'
$txt = Get-Content -Raw -Path 'DEPLOY_SECRETS.md'
$txt2 = $txt -replace "\r\n",""
if ($txt2 -match 'ghp_[A-Za-z0-9]+') { $pat = $Matches[0] } else { Write-Error 'No PAT'; exit 1 }
$repo='ejaimedguez-sudo/BarandRest-backend'
$uri = "https://api.github.com/repos/$repo/actions/runs/$runId/logs"
$hdr = @{ Authorization = "token $pat"; Accept='application/vnd.github+json' }
$out = "temp_logs_$runId.zip"
Write-Output "Downloading logs to $out..."
Invoke-RestMethod -Headers $hdr -Uri $uri -Method Get -OutFile $out
# Extract
$dest = Join-Path -Path 'scripts' -ChildPath "logs_$runId"
if (-Not (Test-Path $dest)) { New-Item -ItemType Directory -Path $dest | Out-Null }
Add-Type -AssemblyName System.IO.Compression.FileSystem
[System.IO.Compression.ZipFile]::ExtractToDirectory($out, $dest)
Write-Output "Extracted logs to $dest"
Get-ChildItem -Path $dest -Recurse | Select-Object FullName, Length | ConvertTo-Json -Depth 3
