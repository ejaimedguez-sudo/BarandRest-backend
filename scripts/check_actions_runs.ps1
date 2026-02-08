$ErrorActionPreference='Stop'
$txt = Get-Content -Raw -Path 'DEPLOY_SECRETS.md'
$txt2 = $txt -replace "\r\n",""
if ($txt2 -match 'ghp_[A-Za-z0-9]+') { $pat = $Matches[0] } else { Write-Error 'No PAT'; exit 1 }
$repo='ejaimedguez-sudo/BarandRest-backend'
$uri = "https://api.github.com/repos/$repo/actions/runs?branch=main&per_page=5"
$hdr = @{ Authorization = "token $pat"; Accept='application/vnd.github+json' }
$res = Invoke-RestMethod -Headers $hdr -Uri $uri -Method Get
$res.workflow_runs | Select-Object id,name,event,status,conclusion,html_url | ConvertTo-Json -Depth 4
