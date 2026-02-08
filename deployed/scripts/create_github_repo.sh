#!/usr/bin/env bash
set -euo pipefail

# Creates a GitHub repo and pushes current repo. Requires `gh` CLI and git configured.
# Usage: ./create_github_repo.sh <owner/repo> [--private]

REPO=${1:-}
PRIVATE_FLAG=""
if [ "${2:-}" = "--private" ]; then PRIVATE_FLAG="--private"; fi

if ! command -v gh >/dev/null 2>&1; then
  echo "gh CLI not found. Install from https://cli.github.com/"
  exit 1
fi

if [ -z "$REPO" ]; then
  echo "Usage: $0 owner/repo [--private]"
  exit 1
fi

echo "Creating repo $REPO on GitHub..."
gh repo create "$REPO" $PRIVATE_FLAG --confirm || true

echo "Adding remote origin and pushing..."
git remote add origin "https://github.com/$REPO.git" 2>/dev/null || git remote set-url origin "https://github.com/$REPO.git"
git push -u origin HEAD:main --force

echo "Repo created and pushed. CI will run on GitHub Actions (if enabled)."
