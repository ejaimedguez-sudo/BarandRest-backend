$ErrorActionPreference='Stop'
$txt = Get-Content -Raw -Path 'DEPLOY_SECRETS.md'
$txt2 = $txt -replace "\r\n",""
if ($txt2 -match 'ghp_[A-Za-z0-9]+') { $pat = $Matches[0] } else { Write-Error 'No PAT found'; exit 1 }
$repo = 'ejaimedguez-sudo/BarandRest-backend'
$now = Get-Date -Format 'yyyyMMdd_HHmmss'
$path = ".github/trigger/trigger_$now.txt"
$content = [System.Convert]::ToBase64String([System.Text.Encoding]::UTF8.GetBytes("trigger $now"))
$body = @{ message = "Trigger CI $now"; content = $content; branch = 'main' } | ConvertTo-Json
$uri = "https://api.github.com/repos/$repo/contents/$path"
$hdr = @{ Authorization = "token $pat"; Accept = 'application/vnd.github+json' }
$res = Invoke-RestMethod -Uri $uri -Headers $hdr -Method Put -Body $body -ContentType 'application/json'
$res | ConvertTo-Json
Write-Output 'Trigger commit created.'
