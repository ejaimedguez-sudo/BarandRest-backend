#!/usr/bin/env bash
set -euo pipefail
ENV_FILE=/var/www/barandrest/.env
if [ ! -f "$ENV_FILE" ]; then echo '.env not found'; exit 1; fi
DB_NAME=$(grep -E '^DB_DATABASE=' "$ENV_FILE" | cut -d'=' -f2- | tr -d '"')
DB_USER=$(grep -E '^DB_USERNAME=' "$ENV_FILE" | cut -d'=' -f2- | tr -d '"')
DB_PASS=$(grep -E '^DB_PASSWORD=' "$ENV_FILE" | cut -d'=' -f2- | tr -d '"')
DB_HOST=$(grep -E '^DB_HOST=' "$ENV_FILE" | cut -d'=' -f2- | tr -d '"')
DB_PORT=$(grep -E '^DB_PORT=' "$ENV_FILE" | cut -d'=' -f2- | tr -d '"')
TS=$(date +%Y%m%d_%H%M%S)
OUTDIR=/var/backups/barandrest
OUTFILE="$OUTDIR/DEPLOY_DB_${TS}.sql.gz"
mkdir -p "$OUTDIR"
mysqldump -h "${DB_HOST:-127.0.0.1}" -P "${DB_PORT:-3306}" -u "${DB_USER}" -p"${DB_PASS}" "${DB_NAME}" | gzip > "$OUTFILE"
rclone copy "$OUTFILE" s3:barandrest/backups/ --transfers 1 --max-age 0
find "$OUTDIR" -type f -name 'DEPLOY_DB_*.sql.gz' -mtime +7 -delete
echo "DONE $OUTFILE"
