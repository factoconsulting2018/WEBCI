#!/usr/bin/env bash
set -euo pipefail

# Backup diario de MySQL mediante mysqldump (contenedor mysql)
# Variables: lee /srv/apps/.env.mysql para MYSQL_ROOT_PASSWORD

BACKUP_DIR="/srv/apps/shared/mysql/backups"
TS="$(date +%Y%m%d_%H%M%S)"
MYSQL_ROOT_PASSWORD=$(grep -E '^MYSQL_ROOT_PASSWORD=' /srv/apps/.env.mysql | cut -d= -f2-)

mkdir -p "${BACKUP_DIR}"

# Lista de bases (excluye system)
DBS=$(docker exec mysql sh -c "mysql -uroot -p${MYSQL_ROOT_PASSWORD} -e 'SHOW DATABASES;' | grep -Ev 'Database|information_schema|performance_schema|mysql|sys'")

for db in ${DBS}; do
  OUT="${BACKUP_DIR}/${db}_${TS}.sql.gz"
  docker exec mysql sh -c "mysqldump -uroot -p${MYSQL_ROOT_PASSWORD} --single-transaction --routines --events ${db}" | gzip -9 > "${OUT}"
done

# Retención: 14 días
find "${BACKUP_DIR}" -type f -name "*.sql.gz" -mtime +14 -delete

echo "Backups completados en ${BACKUP_DIR}"


