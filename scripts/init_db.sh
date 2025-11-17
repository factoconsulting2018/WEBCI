#!/usr/bin/env bash
set -euo pipefail

# Crea bases de datos y usuarios separados para cada servicio
# Requiere que el contenedor mysql esté levantado y /srv/apps/.env.mysql exista

MYSQL_ROOT_PASSWORD=$(grep -E '^MYSQL_ROOT_PASSWORD=' /srv/apps/.env.mysql | cut -d= -f2-)
SERVICES=("camarainversionistas" "factoconsulting" "factoinstitute")

get_env_value() {
  local key="$1"
  local file="$2"
  grep -E "^${key}=" "$file" | tail -n1 | cut -d= -f2-
}

for svc in "${SERVICES[@]}"; do
  ENV_FILE="/srv/apps/${svc}/.env"
  if [[ ! -f "${ENV_FILE}" ]]; then
    echo "⚠️  Saltando ${svc}: no existe ${ENV_FILE}" >&2
    continue
  fi

  DB_NAME=$(get_env_value "DB_DATABASE" "${ENV_FILE}")
  DB_USER=$(get_env_value "DB_USERNAME" "${ENV_FILE}")
  DB_PASS=$(get_env_value "DB_PASSWORD" "${ENV_FILE}")

  if [[ -z "${DB_NAME}" || -z "${DB_USER}" || -z "${DB_PASS}" ]]; then
    echo "⚠️  Saltando ${svc}: faltan variables DB_DATABASE/DB_USERNAME/DB_PASSWORD en ${ENV_FILE}" >&2
    continue
  fi

  echo "Creando/verificando base '${DB_NAME}' y usuario '${DB_USER}'..."
  docker exec -i mysql mysql -uroot -p"${MYSQL_ROOT_PASSWORD}" <<SQL
CREATE DATABASE IF NOT EXISTS \`${DB_NAME}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS '${DB_USER}'@'%' IDENTIFIED BY '${DB_PASS}';
GRANT ALL PRIVILEGES ON \`${DB_NAME}\`.* TO '${DB_USER}'@'%';
FLUSH PRIVILEGES;
SQL
done

echo "Bases y usuarios creados/asegurados."


