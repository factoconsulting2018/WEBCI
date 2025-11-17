#!/usr/bin/env bash
set -euo pipefail

# Instala Nginx y Certbot para Nginx
# Ejecutar como root o con sudo: sudo bash scripts/install_nginx_certbot.sh

if [[ $EUID -ne 0 ]]; then
  echo "Este script debe ejecutarse como root (sudo)." >&2
  exit 1
fi

export DEBIAN_FRONTEND=noninteractive
apt-get update
apt-get install -y nginx python3-certbot-nginx

systemctl enable nginx
systemctl start nginx

nginx -t
systemctl reload nginx || true

echo "Nginx y Certbot instalados."


