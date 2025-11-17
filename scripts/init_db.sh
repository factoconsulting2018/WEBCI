#!/usr/bin/env bash
set -euo pipefail

# Crea bases de datos y usuarios separados para cada servicio
# Requiere que el contenedor mysql esté levantado y /srv/apps/.env.mysql exista

MYSQL_ROOT_PASSWORD=$(grep -E '^MYSQL_ROOT_PASSWORD=' /srv/apps/.env.mysql | cut -d= -f2-)

declare -A DBS
DBS=( \
  ["camarainversionistas"]="camara_user:changeme_camara" \
  ["factoconsulting"]="facto_user:changeme_facto" \
  ["factoinstitute"]="fi_user:changeme_fi" \
)

for db in "${!DBS[@]}"; do
  IFS=":" read -r user pass <<< "${DBS[$db]}"
  docker exec -i mysql mysql -uroot -p"${MYSQL_ROOT_PASSWORD}" <<SQL
CREATE DATABASE IF NOT EXISTS \`${db}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS '${user}'@'%' IDENTIFIED BY '${pass}';
GRANT ALL PRIVILEGES ON \`${db}\`.* TO '${user}'@'%';
FLUSH PRIVILEGES;
SQL
done

echo "Bases y usuarios creados/asegurados."


