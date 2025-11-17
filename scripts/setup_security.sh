#!/usr/bin/env bash
set -euo pipefail

# Configura UFW y (opcional) fail2ban en Ubuntu
# Ejecutar como root o con sudo: sudo bash scripts/setup_security.sh

if [[ $EUID -ne 0 ]]; then
  echo "Este script debe ejecutarse como root (sudo)." >&2
  exit 1
fi

export DEBIAN_FRONTEND=noninteractive
apt-get update
apt-get install -y ufw

ufw default deny incoming
ufw default allow outgoing
ufw allow OpenSSH
ufw allow 80/tcp
ufw allow 443/tcp

yes | ufw enable
ufw status verbose

# Opcional: fail2ban
if [[ "${INSTALL_FAIL2BAN:-0}" == "1" ]]; then
  apt-get install -y fail2ban
  systemctl enable fail2ban
  systemctl start fail2ban
fi

echo "UFW configurado. Puertos abiertos: 22, 80, 443."


