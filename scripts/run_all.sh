#!/usr/bin/env bash
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
REPO_ROOT="$(cd "$SCRIPT_DIR/.." && pwd)"
BACKEND_DIR="$REPO_ROOT/backend"

START=false
SKIP_TESTS=false

for arg in "$@"; do
  case "$arg" in
    --start)
      START=true
      ;;
    --skip-tests)
      SKIP_TESTS=true
      ;;
    *)
      echo "Unknown option: $arg"
      echo "Usage: ./scripts/run_all.sh [--start] [--skip-tests]"
      exit 1
      ;;
  esac
done

echo "== BarandRest local automation (Linux/macOS) =="

echo "[1/5] Running setup..."
"$SCRIPT_DIR/setup_local.sh"

if [ "$SKIP_TESTS" = false ]; then
  echo "[2/5] Running backend tests..."
  cd "$BACKEND_DIR"
  php artisan test
else
  echo "[2/5] Skipping backend tests (--skip-tests)"
fi

echo "[3/5] Running health checks..."
"$SCRIPT_DIR/health_check.sh"

echo "[4/5] Running pre-release checks..."
"$SCRIPT_DIR/pre_release_check.sh"

if [ "$START" = true ]; then
  echo "[5/5] Starting server and queue worker in background..."
  "$SCRIPT_DIR/start_local.sh"
else
  echo "[5/5] Start skipped. Use --start to launch server and worker."
fi

echo "Automation completed successfully."
