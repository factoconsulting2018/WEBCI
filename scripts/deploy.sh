#!/usr/bin/env bash
set -euo pipefail

# Despliegue/actualización de los servicios con healthchecks
# Requiere docker y docker compose plugin

STACK_DIR="/srv/apps"
COMPOSE_FILE="$(dirname "$0")/../infrastructure/docker-compose.yml"

SERVICES=("mysql" "phpmyadmin" "camarainversionistas" "factoconsulting" "factoinstitute")
HEALTH_URLS=("http://127.0.0.1:8081/health" "http://127.0.0.1:8082/health" "http://127.0.0.1:8083/health")

echo "Levantando stack..."
docker compose -f "${COMPOSE_FILE}" up -d --remove-orphans

echo "Esperando healthchecks de aplicaciones..."
for url in "${HEALTH_URLS[@]}"; do
  for i in {1..30}; do
    if curl -fsS "${url}" >/dev/null 2>&1; then
      echo "OK -> ${url}"
      break
    fi
    sleep 3
    if [[ $i -eq 30 ]]; then
      echo "ERROR: Healthcheck no respondió en ${url}" >&2
      exit 1
    fi
  done
done

echo "Despliegue finalizado correctamente."


