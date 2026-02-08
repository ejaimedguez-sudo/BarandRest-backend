#!/bin/bash
set -e
REPO='ejaimedguez-sudo/BarandRest-backend'
PAT=$(grep -oE 'ghp_[A-Za-z0-9]+' /mnt/c/xampp/htdocs/apps/BarandRest/DEPLOY_SECRETS.md || true)
if [ -z "$PAT" ]; then echo 'PAT not found'; exit 1; fi
FILE='.github/workflows/ci.yml'
CONTENT=$(base64 -w0 < "$FILE")
# Get current file sha if exists
RESP=$(curl -sS -H "Authorization: token $PAT" "https://api.github.com/repos/$REPO/contents/$FILE?ref=main")
SHA=$(echo "$RESP" | jq -r .sha)
if [ "$SHA" = "null" ] || [ -z "$SHA" ]; then
	JSON=$(jq -n --arg m 'ci: run composer in backend directory' --arg c "$CONTENT" --arg b 'main' '{message:$m,content:$c,branch:$b}')
else
	JSON=$(jq -n --arg m 'ci: run composer in backend directory' --arg c "$CONTENT" --arg b 'main' --arg s "$SHA" '{message:$m,content:$c,branch:$b,sha:$s}')
fi
curl -sS -X PUT -H "Authorization: token $PAT" -H "Content-Type: application/json" -d "$JSON" "https://api.github.com/repos/$REPO/contents/$FILE" | jq .
