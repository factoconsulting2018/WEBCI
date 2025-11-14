# Comandos de despliegue y mantenimiento

Todos los comandos se ejecutan desde la raíz del proyecto (`webci-app`). Ajusta las variables según tu entorno.

## Preparación

```bash
# Copiar variables de entorno (editar según producción)
cp env/docker.env.example env/docker.env

# Instalar dependencias
docker compose run --rm php composer install --no-dev --optimize-autoloader
```

## Construcción y arranque

```bash
# Construir imágenes con las últimas versiones
docker compose build --pull

# Levantar servicios en segundo plano
docker compose up -d
```

## Migraciones y datos iniciales

```bash
# Ejecutar migraciones de base de datos
docker compose run --rm php php yii migrate --interactive=0

# Crear usuario administrador (seguir asistente)
docker compose run --rm php php yii user/create
```

## Tareas recurrentes

```bash
# Ver logs de PHP FPM
docker compose logs -f php

# Reiniciar servicios después de cambios
docker compose restart php frontend backend

# Limpiar assets/cache
docker compose run --rm php php yii cache/flush-all
docker compose run --rm php php yii asset/compress
```

## Respaldo de base de datos

```bash
docker exec webci-mysql \
  mysqldump -u"$DB_USER" -p"$DB_PASSWORD" "$DB_NAME" \
  > backup-$(date +%Y%m%d%H%M).sql
```

## Apagado y limpieza

```bash
# Detener contenedores
docker compose down

# Detener y eliminar volúmenes (incluye datos de MySQL)
docker compose down --volumes
```

