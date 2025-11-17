#!/usr/bin/env bash
set -euo pipefail

# Crea la estructura /srv/apps y copia plantillas .env
# Ejecutar como root o con sudo: sudo bash scripts/create_structure.sh

BASE_DIR="/srv/apps"
SERVICES=("camarainversionistas" "factoconsulting" "factoinstitute")

if [[ $EUID -ne 0 ]]; then
  echo "Este script debe ejecutarse como root (sudo)." >&2
  exit 1
fi

mkdir -p "${BASE_DIR}/shared/mysql/data"
mkdir -p "${BASE_DIR}/shared/mysql/backups"
mkdir -p "${BASE_DIR}/shared/logs"

for svc in "${SERVICES[@]}"; do
  mkdir -p "${BASE_DIR}/${svc}"
  if [[ -f "$(dirname "$0")/../infrastructure/env/${svc}.env.example" ]]; then
    cp -n "$(dirname "$0")/../infrastructure/env/${svc}.env.example" "${BASE_DIR}/${svc}/.env"
  fi
done

if [[ -f "$(dirname "$0")/../infrastructure/env/mysql.env.example" ]]; then
  cp -n "$(dirname "$0")/../infrastructure/env/mysql.env.example" "${BASE_DIR}/.env.mysql"
fi

echo "Estructura creada en ${BASE_DIR}. Revisa y ajusta los archivos .env."


