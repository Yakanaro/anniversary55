#!/bin/sh
set -e

cd "$(dirname "$0")"

BACKUP_DIR="${BACKUP_DIR:-backups}"
KEEP="${KEEP:-7}"

COMPOSE="docker compose -f docker-compose.prod.yml --env-file .env.production"

mkdir -p "$BACKUP_DIR"

FILE="$BACKUP_DIR/db-$(date +%Y-%m-%d_%H-%M-%S).sql.gz"

$COMPOSE exec -T mysql sh -c 'MYSQL_PWD="$MYSQL_ROOT_PASSWORD" mysqldump -uroot --single-transaction --no-tablespaces "$MYSQL_DATABASE"' | gzip > "$FILE"

if [ ! -s "$FILE" ] || ! gzip -t "$FILE"; then
    rm -f "$FILE"
    echo "Бэкап не удался: файл пустой или повреждён." >&2
    exit 1
fi

ls -1t "$BACKUP_DIR"/db-*.sql.gz | tail -n +"$((KEEP + 1))" | xargs -r rm --

echo "Бэкап сохранён: $FILE"
