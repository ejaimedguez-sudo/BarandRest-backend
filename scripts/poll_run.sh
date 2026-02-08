#!/bin/bash
set -e
REPO='ejaimedguez-sudo/BarandRest-backend'
PAT=$(grep -oE 'ghp_[A-Za-z0-9]+' /mnt/c/xampp/htdocs/apps/BarandRest/DEPLOY_SECRETS.md || true)
if [ -z "$PAT" ]; then echo 'PAT not found' ; exit 1; fi
RUN_ID=21790078423
for i in {1..12}; do
  STATUS=$(curl -sS -H "Authorization: token $PAT" "https://api.github.com/repos/$REPO/actions/runs/$RUN_ID" | jq -r .status)
  echo "poll $i: status=$STATUS"
  if [ "$STATUS" != "in_progress" ] && [ "$STATUS" != "queued" ]; then
    break
  fi
  sleep 5
done
curl -sS -H "Authorization: token $PAT" "https://api.github.com/repos/$REPO/actions/runs/$RUN_ID" | jq -r '{status:.status, conclusion:.conclusion}'
