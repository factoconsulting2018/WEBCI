# Rollback operativo

Este procedimiento asume que las imágenes anteriores están presentes en el host o accesibles desde el registry.

## 1) Ver estado actual
```bash
docker ps
docker image ls | grep -E 'camarainversionistas|factoconsulting|factoinstitute'
```

## 2) Fijar versión anterior
Edita `/srv/apps/.../.env` o ajusta el compose para usar una etiqueta específica:
```yaml
services:
  camarainversionistas:
    image: ghcr.io/yourorg/camarainversionistas:<tag_anterior>
  factoconsulting:
    image: ghcr.io/yourorg/factoconsulting:<tag_anterior>
  factoinstitute:
    image: ghcr.io/yourorg/factoinstitute:<tag_anterior>
```

Aplicar:
```bash
docker compose -f /path/a/infrastructure/docker-compose.yml pull
docker compose -f /path/a/infrastructure/docker-compose.yml up -d
```

## 3) Validar healthchecks
```bash
curl -I http://127.0.0.1:8081/health
curl -I http://127.0.0.1:8082/health
curl -I http://127.0.0.1:8083/health
```

## 4) Restaurar base de datos (si necesario)
Selecciona un backup `.sql.gz` para la base afectada:
```bash
gunzip -c /srv/apps/shared/mysql/backups/camarainversionistas_YYYYmmdd_HHMMSS.sql.gz | \
docker exec -i mysql mysql -uroot -p"$MYSQL_ROOT_PASSWORD" camarainversionistas
```

## 5) Revisar logs
```bash
journalctl -u docker --since "1 hour ago"
docker logs --tail=300 app-camarainversionistas
tail -F /var/log/nginx/*.error.log
```

## Notas
- Evita cambios simultáneos de versión y de esquema de DB sin un plan de migración reversible.
- Mantén al menos dos versiones estables etiquetadas en el registry.


