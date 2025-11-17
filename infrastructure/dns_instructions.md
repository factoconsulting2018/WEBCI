# Instrucciones DNS para producción

Configura los siguientes registros A (y opcionalmente AAAA si tienes IPv6) en tu proveedor DNS, apuntando a la IP pública del servidor de producción.

Reemplaza `<IP_PUBLICA>` con la IP real del servidor.

## Registros requeridos
- camarainversionistas.com
  - A    camarainversionistas.com -> <IP_PUBLICA>
  - A    www.camarainversionistas.com -> <IP_PUBLICA>
- factoconsulting.com
  - A    factoconsulting.com -> <IP_PUBLICA>
  - A    www.factoconsulting.com -> <IP_PUBLICA>
- factoinstitute.com
  - A    factoinstitute.com -> <IP_PUBLICA>
  - A    www.factoinstitute.com -> <IP_PUBLICA>

## Verificación
Tras propagar los DNS (puede tardar entre 5 y 30 minutos en promedio):

```bash
dig +short A camarainversionistas.com
dig +short A www.camarainversionistas.com
dig +short A factoconsulting.com
dig +short A www.factoconsulting.com
dig +short A factoinstitute.com
dig +short A www.factoinstitute.com
```

Los resultados deben devolver `<IP_PUBLICA>`.

## Notas
- Mantén un TTL de 300 segundos (5 minutos) durante el despliegue inicial para facilitar cambios rápidos.
- No configures redirecciones en el DNS (CNAME a servicios externos) si el certificado será emitido con `certbot --nginx` en tu servidor.


