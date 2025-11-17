#!/usr/bin/env bash
set -euo pipefail

# Instalación y despliegue de producción end-to-end en Ubuntu 22.04/24.04
# Uso:
#   sudo LETSENCRYPT_EMAIL="admin@tudominio.com" bash scripts/install_production.sh
# Opcionales:
#   INSTALL_FAIL2BAN=1 para instalar y habilitar fail2ban
#
# Requisitos:
# - Ejecutar como root o con sudo
# - DNS de los dominios apuntando a este servidor

if [[ $EUID -ne 0 ]]; then
  echo "Este script debe ejecutarse como root (sudo)." >&2
  exit 1
fi

if [[ -z "${LETSENCRYPT_EMAIL:-}" ]]; then
  echo "Debes exportar LETSENCRYPT_EMAIL. Ej: sudo LETSENCRYPT_EMAIL='admin@dominio.com' bash $0" >&2
  exit 2
fi

REPO_ROOT="$(cd "$(dirname "$0")/.." && pwd)"
export INSTALL_FAIL2BAN="${INSTALL_FAIL2BAN:-0}"

echo "==> Instalando Docker y utilidades..."
bash "${REPO_ROOT}/scripts/install_docker.sh"

echo "==> Configurando seguridad base (UFW y opcional fail2ban=${INSTALL_FAIL2BAN})..."
INSTALL_FAIL2BAN="${INSTALL_FAIL2BAN}" bash "${REPO_ROOT}/scripts/setup_security.sh"

echo "==> Instalando Nginx y Certbot..."
bash "${REPO_ROOT}/scripts/install_nginx_certbot.sh"

echo "==> Creando estructura /srv/apps y .env iniciales..."
bash "${REPO_ROOT}/scripts/create_structure.sh"

echo "==> ATENCIÓN: Revisa y edita los .env en /srv/apps antes de continuar si necesitas credenciales personalizadas."

COMPOSE_FILE="${REPO_ROOT}/infrastructure/docker-compose.yml"

echo "==> Levantando MySQL y phpMyAdmin..."
docker compose -f "${COMPOSE_FILE}" up -d mysql phpmyadmin

echo "==> Inicializando bases de datos y usuarios..."
bash "${REPO_ROOT}/scripts/init_db.sh"

echo "==> Desplegando configuración de Nginx..."
bash "${REPO_ROOT}/scripts/deploy_nginx.sh"

echo "==> Emisión de certificados Let's Encrypt..."
LETSENCRYPT_EMAIL="${LETSENCRYPT_EMAIL}" bash "${REPO_ROOT}/scripts/issue_certs.sh"

echo "==> Desplegando aplicaciones y validando healthchecks..."
bash "${REPO_ROOT}/scripts/deploy.sh"

echo "==> Configurando backups y timers systemd..."
install -D "${REPO_ROOT}/scripts/backup_mysql.sh" /root/scripts/backup_mysql.sh
chmod 750 /root/scripts/backup_mysql.sh
install -D "${REPO_ROOT}/systemd/backup-mysql.service" /etc/systemd/system/backup-mysql.service
install -D "${REPO_ROOT}/systemd/backup-mysql.timer" /etc/systemd/system/backup-mysql.timer
systemctl daemon-reload
systemctl enable --now backup-mysql.timer

echo "==> Instalando configuraciones de logrotate..."
install -D "${REPO_ROOT}/infrastructure/logrotate/nginx" /etc/logrotate.d/nginx
install -D "${REPO_ROOT}/infrastructure/logrotate/docker-containers" /etc/logrotate.d/docker-containers

echo ""
echo "Instalación y despliegue completados."
echo "Verifica HTTPS en:"
echo " - https://camarainversionistas.com"
echo " - https://factoconsulting.com"
echo " - https://factoinstitute.com"
echo ""
echo "Notas:"
echo " - Edita /srv/apps/.env.mysql y /srv/apps/<servicio>/.env si requieres contraseñas/URLs definitivas."
echo " - Para redeploy: bash ${REPO_ROOT}/scripts/deploy.sh"
echo " - Backups diarios a las 03:00. Revisa /srv/apps/shared/mysql/backups"


