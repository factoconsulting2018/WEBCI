<h1 align="center">WebCI · Directorio Comercial</h1>

Aplicación construida con Yii2 Advanced para gestionar un directorio comercial con diseño Material Design 3, panel administrativo completo y despliegue en contenedores Docker.

## Características principales

- **Portada pública** inspirada en Material Design 3 con cards elevadas, patrocinadores y modal de contacto.
- **Directorio de aliados** con filtros por id/nombre, etiquetas de categorías y correo anti-spam renderizado como imagen.
- **Administrador** con CRUD de aliados, categorías, patrocinadores y plantillas de correo.
- **Reportes descargables** en Excel y PDF generados con PhpSpreadsheet y TCPDF.
- **Contactos** guardados en base de datos y enviados mediante plantillas HTML configurables.
- **Stack Docker** con PHP 8.2 FPM, Nginx, MySQL 8.0 y Mailhog para pruebas SMTP.

## Requisitos

- Docker Desktop + Docker Compose
- PHP 8.1+ y Composer (solo si se desea ejecutar scripts fuera de Docker)
- Node no es requerido

## Configuración inicial

```bash
# 1. Copiar variables por entorno si se desea personalizar
cp env/docker.env.example env/docker.env

# 2. Inicializar Yii (solo la primera vez)
php init --env=Development --overwrite=All

# 3. Instalar dependencias usando el contenedor PHP
docker compose run --rm php composer install

# 4. Levantar servicios
docker compose up -d

# 5. Ejecutar migraciones
docker compose run --rm php php yii migrate
```

Servicios expuestos por defecto:

| Servicio | URL                     |
|----------|-------------------------|
| Portada  | http://localhost:3080   |
| Admin    | http://localhost:3180   |
| MySQL    | localhost:3309          |
| Mailhog  | http://localhost:8028   |
| phpMyAdmin | http://localhost:9090 |

Las credenciales de la base de datos y puertos pueden ajustarse en `env/docker.env`.

## Panel administrativo

- Acceso vía `http://localhost:3180`
- El enlace “Panel administrativo” en la portada apunta a `Yii::$app->params['adminPanelUrl']`
- Crear un usuario administrador con el comando `docker compose run --rm php php yii user/create`.

### Módulos disponibles

- **Aliados:** registro de comercios con logo, redes sociales (formato `Nombre|URL` por línea), categorías y selección para portada.
- **Categorías:** etiquetas reutilizables que se muestran como chips MD3.
- **Patrocinadores:** gestor de cuatro imágenes responsivas antes del footer.
- **Plantillas de Email:** contenido HTML con placeholders `{{businessName}}`, `{{fullName}}`, `{{phone}}`, `{{address}}`, `{{subject}}`.
- **Reportes:** descarga en Excel (`/report/excel`) o PDF (`/report/pdf`).

## Envío de correos

Por defecto se usa Mailhog (`mailhog:1025`). Para producción configura en `env/docker.env` (o variables de entorno) los siguientes valores:

```
SMTP_HOST=smtp.tudominio.com
SMTP_PORT=587
SMTP_USERNAME=usuario
SMTP_PASSWORD=clave
SMTP_ENCRYPTION=tls
MAIL_FILE_TRANSPORT=false
```

## Estructura relevante

- `backend/controllers` · controladores de administración (`BusinessController`, `CategoryController`, `SponsorController`, `EmailTemplateController`, `ReportController`).
- `common/models` · modelos compartidos (`Business`, `Category`, `EmailTemplate`, `ContactSubmission`, `SponsorSet`).
- `common/services/EmailTemplateService.php` · render de plantillas HTML.
- `frontend/views` · portada y modal de contacto (`site/index.php`).
- `docker/` · Dockerfiles y configuraciones de Nginx/PHP.
- `console/migrations` · migraciones para categorías, aliados, plantillas, contactos y patrocinadores.

## Scripts útiles

Revisa el archivo [`DEPLOY_COMMANDS.md`](DEPLOY_COMMANDS.md) para una lista resumida de comandos de despliegue y mantenimiento en producción.

## Tests

Se puede ejecutar la suite básica con:

```bash
docker compose run --rm php vendor/bin/codecept run
```

## Licencia

Proyecto privado WebCI. Basado en Yii2 Advanced, respetando su licencia BSD 3-Clause.
