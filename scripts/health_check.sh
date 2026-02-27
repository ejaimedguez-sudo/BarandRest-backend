#!/usr/bin/env bash
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
REPO_ROOT="$(cd "$SCRIPT_DIR/.." && pwd)"
BACKEND_DIR="$REPO_ROOT/backend"

echo "Running basic health checks"
cd "$BACKEND_DIR"
echo "- Checking migrations status"
php artisan migrate:status || true
echo "- Listing routes"
php artisan route:list || true
echo "- Running smoke request to /up (if server is up)"
if command -v curl >/dev/null 2>&1; then
  curl -fsS --max-time 5 http://127.0.0.1:8000/up || echo "Server not listening on 127.0.0.1:8000"
else
  echo "curl not found, skipping HTTP smoke check"
fi
