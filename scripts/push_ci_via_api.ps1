$ErrorActionPreference = 'Stop'
$repo = 'ejaimedguez-sudo/BarandRest-backend'
$file = '.github/workflows/ci.yml'
$txt = Get-Content -Raw -Path 'DEPLOY_SECRETS.md'
$txt2 = $txt -replace "\r\n",""
if ($txt2 -match 'ghp_[A-Za-z0-9]+') { $pat = $Matches[0] } else { Write-Error 'No PAT found in DEPLOY_SECRETS.md'; exit 1 }
Write-Output "Using PAT: $($pat.Substring(0,8))..."
$uri = "https://api.github.com/repos/$repo/contents/$file"
$hdr = @{ Authorization = "token $pat"; Accept = 'application/vnd.github+json' }
$resp = Invoke-RestMethod -Headers $hdr -Uri $uri -Method Get
$sha = $resp.sha
Write-Output "Remote SHA: $sha"
$bytes = [System.IO.File]::ReadAllBytes($file)
$b64 = [System.Convert]::ToBase64String($bytes)
$body = @{ message = 'CI: run composer inside backend'; content = $b64; sha = $sha; branch = 'main' } | ConvertTo-Json -Depth 5
Write-Output 'Uploading...'
$res = Invoke-RestMethod -Headers $hdr -Uri $uri -Method Put -Body $body -ContentType 'application/json'
$res | ConvertTo-Json -Depth 5
Write-Output 'Done.'
