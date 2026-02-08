#!/usr/bin/env bash
# Simple MySQL backup script for BarAndRest
# Usage: db_backup.sh /path/to/backup_dir

BACKUP_DIR=${1:-/var/backups/barandrest}
mkdir -p "$BACKUP_DIR"

# Environment variables expected: DB_USER, DB_PASSWORD, DB_NAME
DB_USER=${DB_USER:-root}
DB_PASSWORD=${DB_PASSWORD:-}
DB_NAME=${DB_NAME:-barandrest}

TIMESTAMP=$(date +%F_%H%M%S)
OUTFILE="$BACKUP_DIR/barandrest_${TIMESTAMP}.sql.gz"

mysqldump -u"$DB_USER" -p"$DB_PASSWORD" "$DB_NAME" | gzip > "$OUTFILE"

# Retention: remove backups older than 7 days
find "$BACKUP_DIR" -type f -name 'barandrest_*.sql.gz' -mtime +7 -delete

echo "Backup written to $OUTFILE"
