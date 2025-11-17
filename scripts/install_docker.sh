#!/usr/bin/env bash
set -euo pipefail

# Instala Docker Engine y Docker Compose Plugin en Ubuntu 22.04/24.04
# Ejecutar como root o con sudo: sudo bash scripts/install_docker.sh

if [[ $EUID -ne 0 ]]; then
  echo "Este script debe ejecutarse como root (sudo)." >&2
  exit 1
fi

export DEBIAN_FRONTEND=noninteractive
apt-get update
apt-get install -y ca-certificates curl gnupg lsb-release

install -m 0755 -d /etc/apt/keyrings
curl -fsSL https://download.docker.com/linux/ubuntu/gpg | gpg --dearmor -o /etc/apt/keyrings/docker.gpg
chmod a+r /etc/apt/keyrings/docker.gpg

ARCH=$(dpkg --print-architecture)
CODENAME=$(lsb_release -cs)
echo \
  "deb [arch=${ARCH} signed-by=/etc/apt/keyrings/docker.gpg] https://download.docker.com/linux/ubuntu \
  ${CODENAME} stable" | tee /etc/apt/sources.list.d/docker.list > /dev/null

apt-get update
apt-get install -y docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin git jq

systemctl enable docker
systemctl start docker

if ! getent group docker >/dev/null; then
  groupadd docker
fi

if [[ -n "${SUDO_USER:-}" ]]; then
  usermod -aG docker "$SUDO_USER"
  echo "Se añadió al usuario $SUDO_USER al grupo docker (vuelva a iniciar sesión para aplicar)."
fi

docker --version
docker compose version
echo "Docker instalado correctamente."


