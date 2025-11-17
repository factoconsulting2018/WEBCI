# Guía de Instalación en el Servidor

Este documento explica cómo instalar todos los archivos de infraestructura en el servidor de producción.

## Problema

Los scripts y archivos de infraestructura están en tu máquina local pero no están en el repositorio Git, por lo que no aparecen cuando clonas el proyecto en el servidor.

## Solución: Script Maestro

Se ha creado un script maestro que genera todos los archivos necesarios directamente en el servidor.

## Pasos de Instalación

### 1. Conectarse al servidor

```bash
ssh ubuntu@<IP_DEL_SERVIDOR>
cd /opt/webci
```

### 2. Crear el script maestro en el servidor

**Opción A: Copiar el contenido del script**

Crea el archivo manualmente en el servidor:

```bash
nano /opt/webci/scripts/create_all_files.sh
```

Luego copia el contenido completo del archivo `scripts/create_all_files.sh` desde tu máquina local.

**Opción B: Usar SCP desde tu máquina local (Windows)**

```powershell
# Desde PowerShell en Windows
scp C:\WEBCI\scripts\create_all_files.sh ubuntu@<IP_SERVIDOR>:/opt/webci/scripts/
```

**Opción C: Crear directamente con heredoc**

Ejecuta en el servidor:

```bash
cd /opt/webci
mkdir -p scripts
# Luego copia el contenido del script create_all_files.sh aquí
```

### 3. Ejecutar el script maestro

```bash
cd /opt/webci
bash scripts/create_all_files.sh
```

Este script creará:
- Todos los scripts en `scripts/`
- `infrastructure/docker-compose.yml`
- Archivos `.env.example` en `infrastructure/env/`
- Configuraciones de Nginx en `nginx/sites-available/`
- Archivos systemd en `systemd/`
- Configuraciones de logrotate en `infrastructure/logrotate/`

### 4. Verificar que los archivos se crearon

```bash
ls -la scripts/
ls -la infrastructure/
ls -la nginx/sites-available/
```

### 5. Continuar con la instalación

Una vez que todos los archivos estén creados, puedes ejecutar el script de instalación completo:

```bash
sudo LETSENCRYPT_EMAIL="admin@tudominio.com" bash scripts/install_production.sh
```

O ejecutar los pasos manualmente:

```bash
# 1. Instalar Docker
sudo bash scripts/install_docker.sh

# 2. Configurar seguridad
sudo bash scripts/setup_security.sh

# 3. Instalar Nginx y Certbot
sudo bash scripts/install_nginx_certbot.sh

# 4. Crear estructura /srv/apps
sudo bash scripts/create_structure.sh

# 5. Editar credenciales (IMPORTANTE)
sudo nano /srv/apps/.env.mysql
sudo nano /srv/apps/camarainversionistas/.env
sudo nano /srv/apps/factoconsulting/.env
sudo nano /srv/apps/factoinstitute/.env

# 6. Levantar MySQL y phpMyAdmin
sudo docker compose -f infrastructure/docker-compose.yml up -d mysql phpmyadmin

# 7. Inicializar bases de datos
sudo bash scripts/init_db.sh

# 8. Desplegar Nginx
sudo bash scripts/deploy_nginx.sh

# 9. Emitir certificados SSL
sudo LETSENCRYPT_EMAIL="admin@tudominio.com" bash scripts/issue_certs.sh

# 10. Desplegar aplicaciones
sudo bash scripts/deploy.sh

# 11. Configurar backups
sudo install -D scripts/backup_mysql.sh /root/scripts/backup_mysql.sh
sudo chmod 750 /root/scripts/backup_mysql.sh
sudo install -D systemd/backup-mysql.service /etc/systemd/system/backup-mysql.service
sudo install -D systemd/backup-mysql.timer /etc/systemd/system/backup-mysql.timer
sudo systemctl daemon-reload
sudo systemctl enable --now backup-mysql.timer

# 12. Configurar logrotate
sudo install -D infrastructure/logrotate/nginx /etc/logrotate.d/nginx
sudo install -D infrastructure/logrotate/docker-containers /etc/logrotate.d/docker-containers
```

## Notas Importantes

1. **DNS**: Asegúrate de que los dominios apunten a la IP del servidor antes de emitir certificados SSL. Ver `infrastructure/dns_instructions.md`.

2. **Credenciales**: Cambia todas las contraseñas por defecto en los archivos `.env` antes de desplegar en producción.

3. **Imágenes Docker**: Actualiza las imágenes en `docker-compose.yml` con las URLs reales de tus registros de contenedores.

4. **Healthchecks**: Las aplicaciones deben tener un endpoint `/health` que responda con HTTP 200 para que los healthchecks funcionen.

## Verificación Final

```bash
# Verificar contenedores
sudo docker ps

# Verificar Nginx
sudo nginx -t
sudo systemctl status nginx

# Verificar certificados
sudo certbot certificates

# Verificar backups
sudo systemctl status backup-mysql.timer
ls -lh /srv/apps/shared/mysql/backups/
```

## Troubleshooting

Si algún script falla:
1. Verifica los permisos: `chmod +x scripts/*.sh`
2. Verifica que estás ejecutando con `sudo`
3. Revisa los logs: `sudo journalctl -xe`
4. Verifica la conectividad DNS antes de emitir certificados

