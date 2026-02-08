#!/usr/bin/env bash
set -euo pipefail
cd "$(dirname "$0")/.."

# Extraer PAT desde DEPLOY_SECRETS.md
PAT=$(tr -d '\r\n' < DEPLOY_SECRETS.md | grep -oE 'ghp_[A-Za-z0-9]+' | head -n1 || true)
if [ -z "$PAT" ]; then
  echo "ERROR: No PAT found in DEPLOY_SECRETS.md"
  exit 1
fi
echo "Using PAT ${PAT:0:8}..."

# Mostrar estado git
echo "== git status =="
git status --porcelain -b || true

# Intentar continuar rebase si existe
for i in $(seq 1 10); do
  if [ -d .git/rebase-apply ] || [ -d .git/rebase-merge ] || git status --porcelain -b 2>/dev/null | grep -iq "rebase"; then
    echo "Rebase in progress (iter $i): attempting git add and git rebase --continue"
    git add .github/workflows/ci.yml || true
    if git rebase --continue; then
      echo "rebase continued"
      sleep 0.3
      continue
    else
      echo "rebase --continue failed"
      break
    fi
  else
    break
  fi
done

# Check unmerged files
if [ -n "$(git ls-files -u)" ]; then
  echo "ERROR: Unmerged files present:" >&2
  git ls-files -u
  exit 1
fi

# Push using PAT over HTTPS, if fails try pull --rebase then push
REPO_URL="https://$PAT@github.com/ejaimedguez-sudo/BarandRest-backend.git"
echo "Pushing to remote..."
if ! git push "$REPO_URL" HEAD:main; then
  echo "Push failed, attempting git pull --rebase and push"
  git pull --rebase "$REPO_URL" main
  git push "$REPO_URL" HEAD:main
fi

# Create trigger commit via API
TIMESTAMP=$(date -u +%Y%m%d_%H%M%S)
TRIGGER_PATH=".github/trigger/trigger_${TIMESTAMP}.txt"
CONTENT_B64=$(printf "trigger %s\n" "$TIMESTAMP" | base64 -w0)
JSON=$(printf '{"message":"Trigger CI %s","content":"%s","branch":"main"}' "$TIMESTAMP" "$CONTENT_B64")

echo "Creating trigger commit $TRIGGER_PATH"
curl -sS -X PUT -H "Authorization: token $PAT" -H "Content-Type: application/json" -d "$JSON" "https://api.github.com/repos/ejaimedguez-sudo/BarandRest-backend/contents/$TRIGGER_PATH" | python3 -m json.tool || true

# Fetch latest CI run id
RUN_ID=$(curl -sS -H "Authorization: token $PAT" "https://api.github.com/repos/ejaimedguez-sudo/BarandRest-backend/actions/runs?branch=main&per_page=10" | python3 -c 'import sys,json; d=json.load(sys.stdin); runs=d.get("workflow_runs",[]); r=[x for x in runs if x.get("name")=="CI"]; print(r[0]["id"] if r else "")')
if [ -z "$RUN_ID" ]; then
  echo "No CI run found. Exiting.";
  exit 0
fi

echo "Found CI run id: $RUN_ID"
OUT="temp_logs_${RUN_ID}.zip"

echo "Downloading logs to $OUT"
curl -sS -H "Authorization: token $PAT" -L -o "$OUT" "https://api.github.com/repos/ejaimedguez-sudo/BarandRest-backend/actions/runs/$RUN_ID/logs"

DEST_DIR="scripts/logs_${RUN_ID}"
mkdir -p "$DEST_DIR"
python3 - <<PY
import zipfile
zipfile.ZipFile("$OUT").extractall("$DEST_DIR")
print('Extracted logs to', "$DEST_DIR")
PY

echo "Done. Review $DEST_DIR/php/4_Install dependencies.txt for the dependency error."
