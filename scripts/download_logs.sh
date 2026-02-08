#!/bin/bash
set -e
REPO='ejaimedguez-sudo/BarandRest-backend'
PAT=$(grep -oE 'ghp_[A-Za-z0-9]+' /mnt/c/xampp/htdocs/apps/BarandRest/DEPLOY_SECRETS.md || true)
if [ -z "$PAT" ]; then echo 'PAT not found' ; exit 1; fi
RUN_ID=$1
if [ -z "$RUN_ID" ]; then echo 'Usage: $0 <run_id>'; exit 1; fi
OUT=/tmp/actions_${RUN_ID}.zip
echo "Downloading logs to $OUT"
curl -sS -H "Authorization: token $PAT" -L "https://api.github.com/repos/$REPO/actions/runs/$RUN_ID/logs" -o "$OUT"
if [ -f "$OUT" ]; then
  echo 'Listing zip contents:'
  unzip -l "$OUT" | sed -n '1,200p'
  mkdir -p /tmp/actions_logs_$RUN_ID
  unzip -o "$OUT" -d /tmp/actions_logs_$RUN_ID >/dev/null 2>&1 || true
  echo 'Listing extracted files:'
  find /tmp/actions_logs_$RUN_ID -maxdepth 2 -type f -print
else
  echo 'No log file downloaded'
fi
