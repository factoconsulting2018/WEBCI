#!/usr/bin/env bash
set -euo pipefail

# Copia sites-available al host y habilita sitios para Nginx
# Ejecutar como root o con sudo en el servidor

CONF_SRC="$(dirname "$0")/../nginx/sites-available"
CONF_DST="/etc/nginx/sites-available"
ENABLED="/etc/nginx/sites-enabled"

if [[ $EUID -ne 0 ]]; then
  echo "Este script debe ejecutarse como root (sudo)." >&2
  exit 1
fi

install -m 0755 -d "${CONF_DST}"
install -m 0644 "${CONF_SRC}/camarainversionistas.conf" "${CONF_DST}/"
install -m 0644 "${CONF_SRC}/factoconsulting.conf" "${CONF_DST}/"
install -m 0644 "${CONF_SRC}/factoinstitute.conf" "${CONF_DST}/"

ln -sf "${CONF_DST}/camarainversionistas.conf" "${ENABLED}/camarainversionistas.conf"
ln -sf "${CONF_DST}/factoconsulting.conf" "${ENABLED}/factoconsulting.conf"
ln -sf "${CONF_DST}/factoinstitute.conf" "${ENABLED}/factoinstitute.conf"

nginx -t
systemctl reload nginx
echo "Nginx sitios habilitados y recargados."


