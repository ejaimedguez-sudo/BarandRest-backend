#!/usr/bin/env bash
# Basic healthcheck for BarAndRest
# Usage: check_health.sh http://your-app-url

APP_URL=${1:-http://127.0.0.1}

HTTP_STATUS=$(curl -s -o /dev/null -w "%{http_code}" "$APP_URL")
if [ "$HTTP_STATUS" -ne 200 ]; then
  echo "HTTP check failed: $HTTP_STATUS"
  exit 2
fi

echo "HTTP OK: $APP_URL returned 200"

# Optionally check DB connectivity (requires mysql client and env vars)
if command -v mysql >/dev/null 2>&1; then
  mysql -u"${DB_USER:-root}" -p"${DB_PASSWORD:-}" -e "SELECT 1;" ${DB_NAME:-barandrest} >/dev/null 2>&1
  if [ $? -ne 0 ]; then
    echo "DB check failed"
    exit 3
  fi
  echo "DB OK"
fi

exit 0
