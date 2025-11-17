# Guía de Despliegue en Producción

## Flujo Simplificado

### 1. Subir el script maestro a Git (desde tu máquina local)

```bash
# En C:\WEBCI (tu máquina local)
git add scripts/create_all_files.sh
git add infrastructure/ nginx/ systemd/
git commit -m "Agregar script maestro y archivos de infraestructura para despliegue"
git push origin main  # o master, según tu rama
```

### 2. En el servidor: hacer pull y ejecutar

```bash
# Conectarse al servidor
ssh ubuntu@<IP_DEL_SERVIDOR>

# Ir al directorio del proyecto
cd /opt/webci

# Hacer pull del repositorio
git pull origin main  # o master

# Verificar que el script existe
ls -la scripts/create_all_files.sh

# Ejecutar el script maestro (crea todos los archivos)
bash scripts/create_all_files.sh

# Verificar que se crearon los archivos
ls -la scripts/
ls -la infrastructure/
ls -la nginx/sites-available/
```

### 3. Continuar con la instalación

Una vez que todos los archivos estén creados, ejecuta la instalación completa:

```bash
# Opción 1: Instalación automática completa
sudo LETSENCRYPT_EMAIL="admin@tudominio.com" bash scripts/install_production.sh

# Opción 2: Instalación paso a paso (recomendado para revisar cada paso)
sudo bash scripts/install_docker.sh
sudo bash scripts/setup_security.sh
sudo bash scripts/install_nginx_certbot.sh
sudo bash scripts/create_structure.sh

# IMPORTANTE: Editar credenciales antes de continuar
sudo nano /srv/apps/.env.mysql
sudo nano /srv/apps/camarainversionistas/.env
sudo nano /srv/apps/factoconsulting/.env
sudo nano /srv/apps/factoinstitute/.env

# Continuar con el despliegue
sudo docker compose -f infrastructure/docker-compose.yml up -d mysql phpmyadmin
sudo bash scripts/init_db.sh
sudo bash scripts/deploy_nginx.sh
sudo LETSENCRYPT_EMAIL="admin@tudominio.com" bash scripts/issue_certs.sh
sudo bash scripts/deploy.sh

# Configurar backups y logrotate
sudo install -D scripts/backup_mysql.sh /root/scripts/backup_mysql.sh
sudo chmod 750 /root/scripts/backup_mysql.sh
sudo install -D systemd/backup-mysql.service /etc/systemd/system/backup-mysql.service
sudo install -D systemd/backup-mysql.timer /etc/systemd/system/backup-mysql.timer
sudo systemctl daemon-reload
sudo systemctl enable --now backup-mysql.timer
sudo install -D infrastructure/logrotate/nginx /etc/logrotate.d/nginx
sudo install -D infrastructure/logrotate/docker-containers /etc/logrotate.d/docker-containers
```

## Verificación

```bash
# Verificar contenedores
sudo docker ps

# Verificar Nginx
sudo nginx -t
sudo systemctl status nginx

# Verificar certificados SSL
sudo certbot certificates

# Verificar backups programados
sudo systemctl status backup-mysql.timer

# Verificar sitios web
curl -I https://camarainversionistas.com
curl -I https://factoconsulting.com
curl -I https://factoinstitute.com
```

## Notas Importantes

1. **DNS**: Los dominios deben apuntar a la IP del servidor antes de emitir certificados SSL. Ver `infrastructure/dns_instructions.md`.

2. **Credenciales**: Cambia todas las contraseñas por defecto en `/srv/apps/.env.mysql` y `/srv/apps/*/.env` antes de desplegar.

3. **Imágenes Docker**: Actualiza las URLs de las imágenes en `infrastructure/docker-compose.yml` con tus registros reales.

4. **Healthchecks**: Las aplicaciones deben tener un endpoint `/health` que responda HTTP 200.

## Estructura de Archivos

Después de ejecutar `create_all_files.sh`, tendrás:

```
/opt/webci/
├── scripts/
│   ├── create_all_files.sh      # Script maestro (ejecutar primero)
│   ├── install_production.sh    # Instalación completa automática
│   ├── install_docker.sh
│   ├── setup_security.sh
│   ├── install_nginx_certbot.sh
│   ├── create_structure.sh
│   ├── init_db.sh
│   ├── deploy_nginx.sh
│   ├── issue_certs.sh
│   ├── deploy.sh
│   └── backup_mysql.sh
├── infrastructure/
│   ├── docker-compose.yml
│   ├── env/
│   │   ├── mysql.env.example
│   │   ├── camarainversionistas.env.example
│   │   ├── factoconsulting.env.example
│   │   └── factoinstitute.env.example
│   ├── logrotate/
│   │   ├── nginx
│   │   └── docker-containers
│   └── dns_instructions.md
├── nginx/
│   └── sites-available/
│       ├── camarainversionistas.conf
│       ├── factoconsulting.conf
│       └── factoinstitute.conf
└── systemd/
    ├── backup-mysql.service
    └── backup-mysql.timer
```

## Troubleshooting

Si `git pull` no trae los archivos:
- Verifica que hiciste `git add` y `git commit` en tu máquina local
- Verifica que hiciste `git push`
- Verifica la rama: `git branch` y `git pull origin <rama>`

Si el script falla:
- Verifica permisos: `chmod +x scripts/create_all_files.sh`
- Verifica que estás en `/opt/webci`
- Revisa los mensajes de error del script

