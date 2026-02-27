#!/usr/bin/env bash
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
REPO_ROOT="$(cd "$SCRIPT_DIR/.." && pwd)"
BACKEND_DIR="$REPO_ROOT/backend"
ENV_FILE="$BACKEND_DIR/.env"

if [[ ! -f "$ENV_FILE" ]]; then
  echo "PRE_RELEASE_CHECK_FAILED"
  echo "ERROR: .env no encontrado en $BACKEND_DIR"
  exit 1
fi

get_val() {
  local key="$1"
  local line
  line=$(grep -E "^${key}=" "$ENV_FILE" | tail -n 1 || true)
  echo "${line#*=}"
}

errors=()
warnings=()

APP_ENV="$(get_val APP_ENV)"
APP_DEBUG="$(echo "$(get_val APP_DEBUG)" | tr '[:upper:]' '[:lower:]')"
APP_KEY="$(get_val APP_KEY)"
APP_URL="$(get_val APP_URL)"
DASHBOARD_API_KEY="$(get_val DASHBOARD_API_KEY)"
MAIL_MAILER="$(get_val MAIL_MAILER)"

if [[ -z "$APP_KEY" || "$APP_KEY" == "base64:" ]]; then
  errors+=("APP_KEY no está configurada correctamente.")
fi

if [[ "$APP_ENV" == "production" && "$APP_DEBUG" == "true" ]]; then
  errors+=("APP_DEBUG=true en producción.")
fi

if [[ "$APP_ENV" == "production" && "$APP_URL" != https://* ]]; then
  warnings+=("APP_URL en producción no usa https://")
fi

if [[ -z "$DASHBOARD_API_KEY" || "$DASHBOARD_API_KEY" == "change_me_to_a_secure_value" ]]; then
  errors+=("DASHBOARD_API_KEY no está rotada.")
fi

if [[ "$MAIL_MAILER" == "smtp" ]]; then
  MAIL_USERNAME="$(get_val MAIL_USERNAME)"
  MAIL_PASSWORD="$(get_val MAIL_PASSWORD)"
  if [[ -z "$MAIL_USERNAME" || "$MAIL_USERNAME" == "null" || -z "$MAIL_PASSWORD" || "$MAIL_PASSWORD" == "null" ]]; then
    warnings+=("MAIL_MAILER=smtp sin credenciales completas.")
  fi
fi

cd "$BACKEND_DIR"
php artisan about >/dev/null
php artisan route:list >/dev/null

if (( ${#errors[@]} > 0 )); then
  echo "PRE_RELEASE_CHECK_FAILED"
  for e in "${errors[@]}"; do echo "ERROR: $e"; done
  for w in "${warnings[@]}"; do echo "WARN: $w"; done
  exit 1
fi

echo "PRE_RELEASE_CHECK_OK"
for w in "${warnings[@]}"; do echo "WARN: $w"; done
