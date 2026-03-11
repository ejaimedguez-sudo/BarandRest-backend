#!/usr/bin/env bash
set -euo pipefail

if [[ $# -lt 6 ]]; then
  echo "Uso: $0 <environment> <deploy_host> <deploy_user> <deploy_app_path> <ssh_key_path> <backend_env_path> [repo] [deploy_port]"
  exit 1
fi

ENVIRONMENT="$1"
DEPLOY_HOST="$2"
DEPLOY_USER="$3"
DEPLOY_APP_PATH="$4"
SSH_KEY_PATH="$5"
BACKEND_ENV_PATH="$6"
REPO="${7:-ejaimedguez-sudo/BarandRest-backend}"
DEPLOY_PORT="${8:-22}"

if ! command -v gh >/dev/null 2>&1; then
  echo "GitHub CLI (gh) no esta instalado"
  exit 1
fi

[[ -f "$SSH_KEY_PATH" ]] || { echo "No existe llave SSH: $SSH_KEY_PATH"; exit 1; }
[[ -f "$BACKEND_ENV_PATH" ]] || { echo "No existe backend env: $BACKEND_ENV_PATH"; exit 1; }

gh secret set DEPLOY_SSH_KEY --env "$ENVIRONMENT" -R "$REPO" < "$SSH_KEY_PATH"
printf "%s" "$DEPLOY_HOST" | gh secret set DEPLOY_SSH_HOST --env "$ENVIRONMENT" -R "$REPO"
printf "%s" "$DEPLOY_USER" | gh secret set DEPLOY_SSH_USER --env "$ENVIRONMENT" -R "$REPO"
printf "%s" "$DEPLOY_APP_PATH" | gh secret set DEPLOY_APP_PATH --env "$ENVIRONMENT" -R "$REPO"
printf "%s" "$DEPLOY_PORT" | gh secret set DEPLOY_SSH_PORT --env "$ENVIRONMENT" -R "$REPO"
gh secret set BACKEND_ENV_FILE --env "$ENVIRONMENT" -R "$REPO" < "$BACKEND_ENV_PATH"

echo "Secrets configurados para environment '$ENVIRONMENT' en repo '$REPO'."
