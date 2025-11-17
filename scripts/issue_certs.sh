#!/usr/bin/env bash
set -euo pipefail

# Emite/renueva certificados Let's Encrypt para los 3 dominios usando el plugin nginx
# Ejecutar como root o con sudo después de tener los server blocks HTTP activos

DOMAINS=(
  "camarainversionistas.com www.camarainversionistas.com"
  "factoconsulting.com www.factoconsulting.com"
  "factoinstitute.com www.factoinstitute.com"
)

EMAIL="${LETSENCRYPT_EMAIL:-admin@camarainversionistas.com}"

if [[ $EUID -ne 0 ]]; then
  echo "Este script debe ejecutarse como root (sudo)." >&2
  exit 1
fi

for entry in "${DOMAINS[@]}"; do
  set -- ${entry}
  DOMAIN_APEX="$1"
  DOMAIN_WWW="$2"
  certbot --nginx -n --agree-tos -m "${EMAIL}" -d "${DOMAIN_APEX}" -d "${DOMAIN_WWW}" || true
done

systemctl reload nginx
echo "Certificados emitidos/verificados. Renovación está programada por el timer de certbot."


