#!/bin/bash
REPO="ejaimedguez-sudo/BarandRest-backend"
PAT=$(grep -oP "ghp_[A-Za-z0-9]+" /mnt/c/xampp/htdocs/apps/BarandRest/DEPLOY_SECRETS.md || true)
if [ -z "$PAT" ]; then
  echo "PAT not found"
  exit 0
fi
RUN_IDS=(21789328617 21789328517 21789328511)
for id in "${RUN_IDS[@]}"; do
  echo "--- Run $id jobs ---"
  curl -sS -H "Authorization: token $PAT" "https://api.github.com/repos/$REPO/actions/runs/$id/jobs" | jq '.jobs[] | {id: .id, name: .name, status: .status, conclusion: .conclusion, html_url: .html_url, steps: [.steps[] | {name: .name, status: .status, conclusion: .conclusion}] }'
done
