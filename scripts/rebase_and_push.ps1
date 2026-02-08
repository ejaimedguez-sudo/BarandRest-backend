$ErrorActionPreference='Stop'
Write-Output 'Staging resolved file and continuing rebase...'
& git add .github/workflows/ci.yml
try {
    & git rebase --continue
} catch {
    Write-Output 'git rebase --continue failed or no rebase in progress.'
}
$txt = Get-Content -Raw -Path 'DEPLOY_SECRETS.md'
$txt2 = $txt -replace "\r\n",""
if ($txt2 -match 'ghp_[A-Za-z0-9]+') { $pat = $Matches[0] } else { Write-Error 'No PAT found'; exit 1 }
$repoUrl = "https://$($pat)@github.com/ejaimedguez-sudo/BarandRest-backend.git"
Write-Output 'Pushing to remote...'
& git push $repoUrl HEAD:main
Write-Output 'Done.'
