#!/bin/bash
REPO="ejaimedguez-sudo/BarandRest-backend"
PAT=$(grep -oP "ghp_[A-Za-z0-9]+" /mnt/c/xampp/htdocs/apps/BarandRest/DEPLOY_SECRETS.md || true)
if [ -z "$PAT" ]; then
  echo "PAT not found"
  exit 0
fi
curl -sS -H "Authorization: token $PAT" "https://api.github.com/repos/$REPO/actions/runs?per_page=6" | jq ".workflow_runs[] | {id: .id, name: .name, head_branch: .head_branch, status: .status, conclusion: .conclusion, created_at: .created_at, html_url: .html_url}"
