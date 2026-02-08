#!/bin/bash
REPO='ejaimedguez-sudo/BarandRest-backend'
PAT=$(grep -oE 'ghp_[A-Za-z0-9]+' /mnt/c/xampp/htdocs/apps/BarandRest/DEPLOY_SECRETS.md || true)
if [ -z "$PAT" ]; then echo 'PAT not found' ; exit 1; fi
TIMESTAMP=$(date -u +%Y%m%d%H%M%S)
PATHNAME=".github/trigger/trigger_${TIMESTAMP}.txt"
CONTENT=$(echo "trigger $TIMESTAMP" | base64 -w0)
jq -n --arg m "Trigger commit $TIMESTAMP" --arg c "$CONTENT" --arg b "main" '{message:$m,content:$c,branch:$b}' > /tmp/payload.json
curl -sS -X PUT -H "Authorization: token $PAT" -H "Content-Type: application/json" --data-binary @/tmp/payload.json "https://api.github.com/repos/$REPO/contents/$PATHNAME" | jq .
rm -f /tmp/payload.json
