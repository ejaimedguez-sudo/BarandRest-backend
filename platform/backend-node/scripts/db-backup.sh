#!/usr/bin/env bash
set -euo pipefail

DB_HOST="${DB_HOST:-localhost}"
DB_PORT="${DB_PORT:-3306}"
DB_NAME="${DB_NAME:-barandrest_platform}"
DB_USER="${DB_USER:-barandrest_app}"
DB_PASSWORD="${DB_PASSWORD:-}"
OUTPUT_DIR="${1:-./backups}"

mkdir -p "$OUTPUT_DIR"
STAMP="$(date +%Y%m%d-%H%M%S)"
OUT_FILE="$OUTPUT_DIR/${DB_NAME}-${STAMP}.sql"

MYSQLDUMP_BIN="${MYSQLDUMP_BIN:-mysqldump}"
MYSQL_PWD="$DB_PASSWORD" "$MYSQLDUMP_BIN" -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USER" "$DB_NAME" > "$OUT_FILE"

echo "Backup generado: $OUT_FILE"
