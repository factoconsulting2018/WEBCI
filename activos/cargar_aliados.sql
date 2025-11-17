/*
 * Script para importar los aliados de activos/aliados_exportados.json
 * Uso:
 *   1. Copia el archivo JSON dentro del contenedor MySQL, por ejemplo /app/activos/aliados_exportados.json
 *   2. Desde MySQL 8+, ejecuta SOURCE activos/cargar_aliados.sql
 *      (ajusta la ruta del LOAD_FILE si usas otra ubicación).
 */

SET NAMES utf8mb4;
SET @json_path := '/app/activos/aliados_exportados.json';
SET @aliados_json := LOAD_FILE(@json_path);

SELECT CASE
           WHEN @aliados_json IS NULL THEN CONCAT('No se pudo leer el archivo JSON en ', @json_path)
           ELSE 'Archivo JSON cargado correctamente'
       END AS estado_carga;

DROP FUNCTION IF EXISTS clean_value;
DELIMITER $$
CREATE FUNCTION clean_value(input TEXT) RETURNS TEXT
    DETERMINISTIC
    SQL SECURITY INVOKER
BEGIN
    DECLARE tmp TEXT;
    IF input IS NULL THEN
        RETURN NULL;
    END IF;
    SET tmp = NULLIF(TRIM(REPLACE(REPLACE(input, CHAR(13), ''), CHAR(10), '')), '');
    IF tmp IS NULL THEN
        RETURN NULL;
    END IF;
    IF UPPER(tmp) IN ('NA','N/A','N.A','N.D','N/D','PENDIENTE','NO TIENE','NO APLICA','IDEM','N.A.','N.A..','N/A.','N/A..','N/D.','N/D..','N/A;', 'NA;') THEN
        RETURN NULL;
    END IF;
    RETURN tmp;
END$$
DELIMITER ;

DROP TEMPORARY TABLE IF EXISTS tmp_allies;

CREATE TEMPORARY TABLE tmp_allies AS
WITH raw AS (
    SELECT JSON_UNQUOTE(
               JSON_EXTRACT(entry, CONCAT(
                   '$."',
                   JSON_UNQUOTE(JSON_EXTRACT(JSON_KEYS(entry), '$[0]')),
                   '"'
               ))
           ) AS row_data
    FROM JSON_TABLE(@aliados_json, '$[*]'
         COLUMNS(entry JSON PATH '$')
    ) jt
),
tokens AS (
    SELECT
        TRIM(SUBSTRING_INDEX(row_data, ';', 1))  AS c1,
        TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(row_data, ';', 2), ';', -1))  AS c2,
        TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(row_data, ';', 4), ';', -1))  AS c4,
        TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(row_data, ';', 5), ';', -1))  AS c5,
        TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(row_data, ';', 8), ';', -1))  AS c8,
        TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(row_data, ';', 9), ';', -1))  AS c9,
        TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(row_data, ';', 12), ';', -1)) AS c12,
        TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(row_data, ';', 13), ';', -1)) AS c13,
        TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(row_data, ';', 14), ';', -1)) AS c14,
        TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(row_data, ';', 15), ';', -1)) AS c15,
        TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(row_data, ';', 16), ';', -1)) AS c16,
        TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(row_data, ';', 20), ';', -1)) AS c20,
        TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(row_data, ';', 21), ';', -1)) AS c21,
        TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(row_data, ';', 22), ';', -1)) AS c22,
        TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(row_data, ';', 23), ';', -1)) AS c23,
        TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(row_data, ';', 24), ';', -1)) AS c24,
        TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(row_data, ';', 25), ';', -1)) AS c25,
        TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(row_data, ';', 26), ';', -1)) AS c26,
        TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(row_data, ';', 27), ';', -1)) AS c27,
        TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(row_data, ';', 28), ';', -1)) AS c28,
        TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(row_data, ';', 29), ';', -1)) AS c29,
        TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(row_data, ';', 30), ';', -1)) AS c30,
        TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(row_data, ';', 31), ';', -1)) AS c31,
        TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(row_data, ';', 32), ';', -1)) AS c32
    FROM raw
)
SELECT
    c1  AS consecutivo,
    clean_value(c2)   AS razon_social,
    clean_value(c5)   AS observaciones,
    clean_value(c8)   AS nombre_comercial,
    clean_value(c13)  AS provincia,
    clean_value(c14)  AS canton,
    clean_value(c15)  AS distrito,
    clean_value(c16)  AS direccion,
    clean_value(c20)  AS sector,
    clean_value(c21)  AS actividades,
    clean_value(c22)  AS servicios_contratados,
    clean_value(c23)  AS beneficio_mas_usado,
    clean_value(c24)  AS sitio_web,
    clean_value(c25)  AS facebook,
    clean_value(c26)  AS instagram,
    clean_value(c27)  AS representante_contacto,
    clean_value(c28)  AS telefono_fijo,
    clean_value(c29)  AS whatsapp_empresarial,
    clean_value(c30)  AS whatsapp_gerente,
    clean_value(c31)  AS correo_general,
    clean_value(c32)  AS correo_fe
FROM tokens;

-- Inserción final en business
INSERT INTO business (
    name,
    slug,
    summary,
    description,
    email,
    whatsapp,
    phone,
    address,
    latitude,
    longitude,
    website,
    facebook,
    instagram,
    linkedin,
    tiktok,
    youtube,
    logo_path,
    cover_path,
    is_active,
    show_on_home,
    created_at,
    updated_at
)
SELECT
    COALESCE(nombre_comercial, razon_social, CONCAT('Aliado ', consecutivo)) AS name_clean,
    CONCAT(
        REGEXP_REPLACE(
            REGEXP_REPLACE(
                LOWER(COALESCE(nombre_comercial, razon_social, CONCAT('aliado-', consecutivo))),
                '[^[:alnum:]]+',
                '-'
            ),
            '(^-+|-+$)',
            ''
        ),
        '-', consecutivo
    ) AS slug,
    sector AS summary,
    NULLIF(CONCAT_WS(' | ', observaciones, actividades, servicios_contratados, beneficio_mas_usado), '') AS description,
    COALESCE(correo_general, correo_fe) AS email,
    COALESCE(whatsapp_empresarial, whatsapp_gerente, telefono_fijo) AS whatsapp,
    telefono_fijo AS phone,
    NULLIF(CONCAT_WS(', ', direccion, distrito, canton, provincia), '') AS address,
    NULL,
    NULL,
    sitio_web,
    facebook,
    instagram,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    1,
    0,
    UNIX_TIMESTAMP(),
    UNIX_TIMESTAMP()
FROM tmp_allies
ON DUPLICATE KEY UPDATE
    summary     = VALUES(summary),
    description = VALUES(description),
    email       = VALUES(email),
    whatsapp    = VALUES(whatsapp),
    phone       = VALUES(phone),
    address     = VALUES(address),
    website     = VALUES(website),
    facebook    = VALUES(facebook),
    instagram   = VALUES(instagram),
    updated_at  = VALUES(updated_at);

DROP TEMPORARY TABLE IF EXISTS tmp_allies;
DROP FUNCTION IF EXISTS clean_value;


