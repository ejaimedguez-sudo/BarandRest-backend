#!/bin/bash
set -e
REPO='ejaimedguez-sudo/BarandRest-backend'
PAT=$(grep -oE 'ghp_[A-Za-z0-9]+' /mnt/c/xampp/htdocs/apps/BarandRest/DEPLOY_SECRETS.md || true)
if [ -z "$PAT" ]; then echo 'PAT not found'; exit 1; fi
API="https://api.github.com/repos/$REPO"
RUN_IDS=$(curl -sS -H "Authorization: token $PAT" "$API/actions/runs?per_page=4" | jq -r '.workflow_runs[] | .id')
for id in $RUN_IDS; do
  echo "--- Run $id jobs ---"
  curl -sS -H "Authorization: token $PAT" "$API/actions/runs/$id/jobs" | jq '.jobs[] | {id: .id, name: .name, status: .status, conclusion: .conclusion, html_url: .html_url, steps: [.steps[] | {name: .name, status: .status, conclusion: .conclusion}] }'
done
