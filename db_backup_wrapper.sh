#!/bin/bash
set -e

CRED_FILE=/root/creds/barandrest_db_creds.txt
if [ ! -f "$CRED_FILE" ]; then
  echo "Credenciales no encontradas: $CRED_FILE" >&2
  exit 1
fi
. "$CRED_FILE"

TIMESTAMP=$(date +%F_%H%M%S)
BACKUP_DIR=/var/backups/barandrest
mkdir -p "$BACKUP_DIR"

DUMPFILE="$BACKUP_DIR/db_${TIMESTAMP}.sql.gz"
mysqldump -u"$DB_USERNAME" -p"$DB_PASSWORD" --single-transaction --quick --skip-lock-tables barandrest | gzip > "$DUMPFILE"

if command -v rclone >/dev/null 2>&1; then
  # only attempt upload if remote 's3' is configured
  if rclone listremotes 2>/dev/null | grep -q '^s3:'; then
    rclone copy "$DUMPFILE" s3:barandrest/backups/ || true
  else
    echo "rclone disponible pero no está configurado el remoto 's3', omitiendo upload"
  fi
fi

find "$BACKUP_DIR" -type f -mtime +30 -name '*.sql.gz' -delete
