#!/usr/bin/env bash
set -euo pipefail

# Script maestro que crea todos los archivos de infraestructura en el servidor
# 
# Flujo de uso:
# 1. Subir este script a Git desde tu máquina local:
#    git add scripts/create_all_files.sh
#    git commit -m "Agregar script maestro de infraestructura"
#    git push origin main
#
# 2. En el servidor, hacer pull y ejecutar:
#    cd /opt/webci
#    git pull origin main
#    bash scripts/create_all_files.sh
#
# Este script genera todos los archivos necesarios: scripts, docker-compose,
# configuraciones de Nginx, archivos .env de ejemplo, systemd, logrotate, etc.

REPO_ROOT="${1:-/opt/webci}"

echo "==> Creando estructura de directorios..."
mkdir -p "${REPO_ROOT}/scripts"
mkdir -p "${REPO_ROOT}/infrastructure/env"
mkdir -p "${REPO_ROOT}/infrastructure/logrotate"
mkdir -p "${REPO_ROOT}/nginx/sites-available"
mkdir -p "${REPO_ROOT}/systemd"

# ============================================================================
# SCRIPTS
# ============================================================================

echo "==> Creando scripts..."

cat > "${REPO_ROOT}/scripts/install_docker.sh" << 'SCRIPT_END'
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
SCRIPT_END

cat > "${REPO_ROOT}/scripts/setup_security.sh" << 'SCRIPT_END'
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
SCRIPT_END

cat > "${REPO_ROOT}/scripts/install_nginx_certbot.sh" << 'SCRIPT_END'
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
SCRIPT_END

cat > "${REPO_ROOT}/scripts/create_structure.sh" << 'SCRIPT_END'
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

REPO_ROOT="$(cd "$(dirname "$0")/.." && pwd)"

for svc in "${SERVICES[@]}"; do
  mkdir -p "${BASE_DIR}/${svc}"
  if [[ -f "${REPO_ROOT}/infrastructure/env/${svc}.env.example" ]]; then
    cp -n "${REPO_ROOT}/infrastructure/env/${svc}.env.example" "${BASE_DIR}/${svc}/.env"
  fi
done

if [[ -f "${REPO_ROOT}/infrastructure/env/mysql.env.example" ]]; then
  cp -n "${REPO_ROOT}/infrastructure/env/mysql.env.example" "${BASE_DIR}/.env.mysql"
fi

echo "Estructura creada en ${BASE_DIR}. Revisa y ajusta los archivos .env."
SCRIPT_END

cat > "${REPO_ROOT}/scripts/init_db.sh" << 'SCRIPT_END'
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
SCRIPT_END

cat > "${REPO_ROOT}/scripts/deploy_nginx.sh" << 'SCRIPT_END'
#!/usr/bin/env bash
set -euo pipefail

# Copia sites-available al host y habilita sitios para Nginx
# Ejecutar como root o con sudo en el servidor

CONF_SRC="$(cd "$(dirname "$0")/.." && pwd)/nginx/sites-available"
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
SCRIPT_END

cat > "${REPO_ROOT}/scripts/issue_certs.sh" << 'SCRIPT_END'
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
SCRIPT_END

cat > "${REPO_ROOT}/scripts/deploy.sh" << 'SCRIPT_END'
#!/usr/bin/env bash
set -euo pipefail

# Despliegue/actualización de los servicios con healthchecks
# Requiere docker y docker compose plugin

STACK_DIR="/srv/apps"
COMPOSE_FILE="$(cd "$(dirname "$0")/.." && pwd)/infrastructure/docker-compose.yml"

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
SCRIPT_END

cat > "${REPO_ROOT}/scripts/backup_mysql.sh" << 'SCRIPT_END'
#!/usr/bin/env bash
set -euo pipefail

# Backup diario de MySQL mediante mysqldump (contenedor mysql)
# Variables: lee /srv/apps/.env.mysql para MYSQL_ROOT_PASSWORD

BACKUP_DIR="/srv/apps/shared/mysql/backups"
TS="$(date +%Y%m%d_%H%M%S)"
MYSQL_ROOT_PASSWORD=$(grep -E '^MYSQL_ROOT_PASSWORD=' /srv/apps/.env.mysql | cut -d= -f2-)

mkdir -p "${BACKUP_DIR}"

# Lista de bases (excluye system)
DBS=$(docker exec mysql sh -c "mysql -uroot -p${MYSQL_ROOT_PASSWORD} -e 'SHOW DATABASES;' | grep -Ev 'Database|information_schema|performance_schema|mysql|sys'")

for db in ${DBS}; do
  OUT="${BACKUP_DIR}/${db}_${TS}.sql.gz"
  docker exec mysql sh -c "mysqldump -uroot -p${MYSQL_ROOT_PASSWORD} --single-transaction --routines --events ${db}" | gzip -9 > "${OUT}"
done

# Retención: 14 días
find "${BACKUP_DIR}" -type f -name "*.sql.gz" -mtime +14 -delete

echo "Backups completados en ${BACKUP_DIR}"
SCRIPT_END

cat > "${REPO_ROOT}/scripts/install_production.sh" << 'SCRIPT_END'
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

echo "==> Configurando logrotate..."
install -D "${REPO_ROOT}/infrastructure/logrotate/nginx" /etc/logrotate.d/nginx
install -D "${REPO_ROOT}/infrastructure/logrotate/docker-containers" /etc/logrotate.d/docker-containers

echo ""
echo "✓ Instalación completada."
echo ""
echo "Verifica:"
echo "  - https://camarainversionistas.com"
echo "  - https://factoconsulting.com"
echo "  - https://factoinstitute.com"
echo ""
echo "Revisa y ajusta credenciales en /srv/apps/.env.mysql y /srv/apps/*/.env si es necesario."
SCRIPT_END

# Hacer ejecutables todos los scripts
chmod +x "${REPO_ROOT}/scripts"/*.sh

# ============================================================================
# DOCKER COMPOSE
# ============================================================================

echo "==> Creando docker-compose.yml..."

cat > "${REPO_ROOT}/infrastructure/docker-compose.yml" << 'COMPOSE_END'
version: "3.9"

name: prod-stack

services:
  mysql:
    image: mysql:8.0
    container_name: mysql
    restart: unless-stopped
    env_file:
      - /srv/apps/.env.mysql
    command: ["--default-authentication-plugin=mysql_native_password","--character-set-server=utf8mb4","--collation-server=utf8mb4_unicode_ci"]
    healthcheck:
      test: ["CMD-SHELL", "mysqladmin ping -h 127.0.0.1 -uroot -p$$MYSQL_ROOT_PASSWORD || exit 1"]
      interval: 10s
      timeout: 5s
      retries: 10
    volumes:
      - /srv/apps/shared/mysql/data:/var/lib/mysql
    networks:
      - backend

  phpmyadmin:
    image: phpmyadmin:latest
    container_name: phpmyadmin
    restart: unless-stopped
    environment:
      - PMA_HOST=mysql
      - PMA_USER=root
      - PMA_PASSWORD=${MYSQL_ROOT_PASSWORD}
    depends_on:
      - mysql
    ports:
      - "9090:80" # expuesto localmente; se recomienda acceder vía Nginx con auth
    networks:
      - backend

  camarainversionistas:
    image: ghcr.io/yourorg/camarainversionistas:latest
    container_name: app-camarainversionistas
    restart: unless-stopped
    env_file:
      - /srv/apps/camarainversionistas/.env
    depends_on:
      - mysql
    ports:
      - "8081:8081"
    healthcheck:
      test: ["CMD", "curl", "-fsS", "http://127.0.0.1:8081/health"]
      interval: 15s
      timeout: 5s
      retries: 10
    networks:
      - backend
      - frontend

  factoconsulting:
    image: ghcr.io/yourorg/factoconsulting:latest
    container_name: app-factoconsulting
    restart: unless-stopped
    env_file:
      - /srv/apps/factoconsulting/.env
    depends_on:
      - mysql
    ports:
      - "8082:8082"
    healthcheck:
      test: ["CMD", "curl", "-fsS", "http://127.0.0.1:8082/health"]
      interval: 15s
      timeout: 5s
      retries: 10
    networks:
      - backend
      - frontend

  factoinstitute:
    image: ghcr.io/yourorg/factoinstitute:latest
    container_name: app-factoinstitute
    restart: unless-stopped
    env_file:
      - /srv/apps/factoinstitute/.env
    depends_on:
      - mysql
    ports:
      - "8083:8083"
    healthcheck:
      test: ["CMD", "curl", "-fsS", "http://127.0.0.1:8083/health"]
      interval: 15s
      timeout: 5s
      retries: 10
    networks:
      - backend
      - frontend

networks:
  backend:
    driver: bridge
  frontend:
    driver: bridge
COMPOSE_END

# ============================================================================
# NGINX CONFIGURATIONS
# ============================================================================

echo "==> Creando configuraciones de Nginx..."

cat > "${REPO_ROOT}/nginx/sites-available/camarainversionistas.conf" << 'NGINX_END'
server {
    listen 80;
    listen [::]:80;
    server_name camarainversionistas.com www.camarainversionistas.com;

    # Redirigir www -> apex
    if ($host = www.camarainversionistas.com) {
        return 301 https://camarainversionistas.com$request_uri;
    }

    location / {
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        proxy_read_timeout 60s;
        proxy_connect_timeout 60s;
        proxy_pass http://127.0.0.1:8081;
    }

    access_log /var/log/nginx/camarainversionistas.access.log;
    error_log  /var/log/nginx/camarainversionistas.error.log;
}
NGINX_END

cat > "${REPO_ROOT}/nginx/sites-available/factoconsulting.conf" << 'NGINX_END'
server {
    listen 80;
    listen [::]:80;
    server_name factoconsulting.com www.factoconsulting.com;

    if ($host = www.factoconsulting.com) {
        return 301 https://factoconsulting.com$request_uri;
    }

    location / {
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        proxy_read_timeout 60s;
        proxy_connect_timeout 60s;
        proxy_pass http://127.0.0.1:8082;
    }

    access_log /var/log/nginx/factoconsulting.access.log;
    error_log  /var/log/nginx/factoconsulting.error.log;
}
NGINX_END

cat > "${REPO_ROOT}/nginx/sites-available/factoinstitute.conf" << 'NGINX_END'
server {
    listen 80;
    listen [::]:80;
    server_name factoinstitute.com www.factoinstitute.com;

    if ($host = www.factoinstitute.com) {
        return 301 https://factoinstitute.com$request_uri;
    }

    location / {
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        proxy_read_timeout 60s;
        proxy_connect_timeout 60s;
        proxy_pass http://127.0.0.1:8083;
    }

    access_log /var/log/nginx/factoinstitute.access.log;
    error_log  /var/log/nginx/factoinstitute.error.log;
}
NGINX_END

# ============================================================================
# ENV FILES
# ============================================================================

echo "==> Creando archivos .env de ejemplo..."

cat > "${REPO_ROOT}/infrastructure/env/mysql.env.example" << 'ENV_END'
# Variables para MySQL (usadas por docker-compose)
MYSQL_ROOT_PASSWORD=changeme_root_strong
MYSQL_DATABASE=bootstrap_db
MYSQL_USER=bootstrap_user
MYSQL_PASSWORD=changeme_user_strong
ENV_END

cat > "${REPO_ROOT}/infrastructure/env/camarainversionistas.env.example" << 'ENV_END'
# Variables de la app camarainversionistas
APP_NAME=camarainversionistas
APP_PORT=8081
APP_ENV=production
APP_URL=https://camarainversionistas.com

# Conexión MySQL
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=camarainversionistas
DB_USERNAME=camara_user
DB_PASSWORD=changeme_camara
ENV_END

cat > "${REPO_ROOT}/infrastructure/env/factoconsulting.env.example" << 'ENV_END'
# Variables de la app factoconsulting
APP_NAME=factoconsulting
APP_PORT=8082
APP_ENV=production
APP_URL=https://factoconsulting.com

# Conexión MySQL
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=factoconsulting
DB_USERNAME=facto_user
DB_PASSWORD=changeme_facto
ENV_END

cat > "${REPO_ROOT}/infrastructure/env/factoinstitute.env.example" << 'ENV_END'
# Variables de la app factoinstitute
APP_NAME=factoinstitute
APP_PORT=8083
APP_ENV=production
APP_URL=https://factoinstitute.com

# Conexión MySQL
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=factoinstitute
DB_USERNAME=fi_user
DB_PASSWORD=changeme_fi
ENV_END

# ============================================================================
# SYSTEMD FILES
# ============================================================================

echo "==> Creando archivos systemd..."

cat > "${REPO_ROOT}/systemd/backup-mysql.service" << 'SERVICE_END'
[Unit]
Description=Backup MySQL (Docker)
Wants=network-online.target
After=network-online.target

[Service]
Type=oneshot
ExecStart=/usr/bin/bash /root/scripts/backup_mysql.sh
Nice=10
IOSchedulingClass=best-effort
IOSchedulingPriority=7

[Install]
WantedBy=multi-user.target
SERVICE_END

cat > "${REPO_ROOT}/systemd/backup-mysql.timer" << 'TIMER_END'
[Unit]
Description=Ejecuta backup MySQL diario

[Timer]
OnCalendar=*-*-* 03:00:00
Persistent=true

[Install]
WantedBy=timers.target
TIMER_END

# ============================================================================
# LOGROTATE FILES
# ============================================================================

echo "==> Creando configuraciones de logrotate..."

cat > "${REPO_ROOT}/infrastructure/logrotate/nginx" << 'LOGROTATE_END'
/var/log/nginx/*.log {
    daily
    missingok
    rotate 14
    compress
    delaycompress
    notifempty
    create 0640 www-data adm
    sharedscripts
    postrotate
        [ -s /run/nginx.pid ] && kill -USR1 `cat /run/nginx.pid`
    endscript
}
LOGROTATE_END

cat > "${REPO_ROOT}/infrastructure/logrotate/docker-containers" << 'LOGROTATE_END'
/var/lib/docker/containers/*/*-json.log {
    daily
    rotate 7
    size 50M
    missingok
    copytruncate
    compress
    delaycompress
    notifempty
}
LOGROTATE_END

# ============================================================================
# DNS INSTRUCTIONS
# ============================================================================

echo "==> Creando instrucciones de DNS..."

cat > "${REPO_ROOT}/infrastructure/dns_instructions.md" << 'DNS_END'
# Instrucciones de configuración DNS

Para que Let's Encrypt pueda emitir certificados, los siguientes dominios deben apuntar a la IP de este servidor:

## Registros DNS requeridos

Para cada dominio, crea registros **A** apuntando a la IP del servidor:

### camarainversionistas.com
- Tipo: A
- Nombre: @ (o camarainversionistas.com)
- Valor: `<IP_DEL_SERVIDOR>`
- TTL: 3600 (o el predeterminado)

- Tipo: A
- Nombre: www
- Valor: `<IP_DEL_SERVIDOR>`
- TTL: 3600

### factoconsulting.com
- Tipo: A
- Nombre: @ (o factoconsulting.com)
- Valor: `<IP_DEL_SERVIDOR>`
- TTL: 3600

- Tipo: A
- Nombre: www
- Valor: `<IP_DEL_SERVIDOR>`
- TTL: 3600

### factoinstitute.com
- Tipo: A
- Nombre: @ (o factoinstitute.com)
- Valor: `<IP_DEL_SERVIDOR>`
- TTL: 3600

- Tipo: A
- Nombre: www
- Valor: `<IP_DEL_SERVIDOR>`
- TTL: 3600

## Verificación

Después de configurar DNS, verifica con:

```bash
dig +short A camarainversionistas.com
dig +short A factoconsulting.com
dig +short A factoinstitute.com
```

Todos deben mostrar la IP del servidor.
DNS_END

echo ""
echo "✓ Todos los archivos creados en ${REPO_ROOT}"
echo ""
echo "Estructura creada:"
echo "  - scripts/ (10 scripts)"
echo "  - infrastructure/docker-compose.yml"
echo "  - infrastructure/env/ (4 archivos .env.example)"
echo "  - infrastructure/logrotate/ (2 configuraciones)"
echo "  - nginx/sites-available/ (3 configuraciones)"
echo "  - systemd/ (2 archivos)"
echo ""
echo "Siguiente paso: ejecutar los scripts de instalación desde ${REPO_ROOT}"
