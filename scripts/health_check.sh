#!/usr/bin/env bash
set -euo pipefail
echo "Running basic health checks"
cd backend
echo "- Checking migrations status"
php artisan migrate:status || true
echo "- Listing routes (first 20)"
php artisan route:list --compact | sed -n '1,20p' || true
echo "- Running a smoke request if server is up"
if nc -z 127.0.0.1 8000 >/dev/null 2>&1; then
  curl -sS http://127.0.0.1:8000 || true
else
  echo "Server not listening on 127.0.0.1:8000"
fi
