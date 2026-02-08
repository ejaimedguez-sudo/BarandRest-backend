$ErrorActionPreference='Stop'
$txt = Get-Content -Raw -Path 'DEPLOY_SECRETS.md'
$txt2 = $txt -replace "\r\n",""
if ($txt2 -match 'ghp_[A-Za-z0-9]+') { $pat = $Matches[0] } else { Write-Error 'No PAT found'; exit 1 }
$repoUrl = "https://$($pat)@github.com/ejaimedguez-sudo/BarandRest-backend.git"
Write-Output "Pushing to $repoUrl (token hidden)"
# Use git from PATH
& git push $repoUrl HEAD:main
Write-Output 'Push finished.'
