import json
import os
import re
import time
from pathlib import Path

BASE_DIR = Path(__file__).resolve().parent
JSON_FILE = BASE_DIR / "aliados_exportados.json"
SQL_FILE = BASE_DIR / "aliados_import.sql"
CATEGORY_SQL_FILE = BASE_DIR / "categorias_import.sql"

PLACEHOLDERS = {
    "",
    "NA",
    "N/A",
    "N.A",
    "N.A.",
    "N.A..",
    "N.D",
    "N/D",
    "N/D.",
    "N/D..",
    "N/A.",
    "N/A..",
    "PENDIENTE",
    "NO TIENE",
    "NO APLICA",
    "IDEM",
    "N.A..",
    "N.D..",
    "N.A..",
}


def clean(value: str | None) -> str | None:
    if value is None:
        return None
    text = value.strip().strip(";")
    if not text:
        return None
    if text.upper() in PLACEHOLDERS:
        return None
    return text


def slugify(text: str, fallback: str, suffix: str, existing: set[str]) -> str:
    base = text or fallback or "aliado"
    slug = re.sub(r"[^a-z0-9]+", "-", base.lower()).strip("-")
    if not slug:
        slug = "aliado"
    candidate = f"{slug}-{suffix}"
    counter = 1
    while candidate in existing:
        candidate = f"{slug}-{suffix}-{counter}"
        counter += 1
    existing.add(candidate)
    return candidate


def sql_escape(value: str | None) -> str:
    if value is None:
        return "NULL"
    return "'" + value.replace("\\", "\\\\").replace("'", "''") + "'"


def parse_row(raw_string: str) -> dict:
    parts = raw_string.split(";")
    # Ensure length to 34 (pad)
    if len(parts) < 34:
        parts += [""] * (34 - len(parts))
    data = [clean(p) for p in parts]
    return {
        "consecutivo": data[0] or "0",
        "razon_social": data[1],
        "observaciones": data[4],
        "nombre_comercial": data[7],
        "provincia": data[12],
        "canton": data[13],
        "distrito": data[14],
        "direccion": data[15],
        "sector": data[19],
        "actividades": data[20],
        "servicios": data[21],
        "beneficio": data[22],
        "sitio_web": data[23],
        "facebook": data[24],
        "instagram": data[25],
        "representante": data[26],
        "telefono": data[27],
        "whatsapp_emp": data[28],
        "whatsapp_ger": data[29],
        "correo_general": data[30],
        "correo_fe": data[31],
    }


def build_row(entry: dict, slug_set: set[str]) -> dict:
    keys = list(entry.keys())
    raw_block = entry[keys[0]]
    parsed = parse_row(raw_block)
    name = parsed["nombre_comercial"] or parsed["razon_social"] or f"Aliado {parsed['consecutivo']}"
    slug = slugify(parsed["nombre_comercial"] or parsed["razon_social"], "aliado", parsed["consecutivo"], slug_set)
    description_parts = [
        parsed["observaciones"],
        parsed["actividades"],
        parsed["servicios"],
        parsed["beneficio"],
    ]
    description = " | ".join([p for p in description_parts if p]) or None
    address_parts = [
        parsed["direccion"],
        parsed["distrito"],
        parsed["canton"],
        parsed["provincia"],
    ]
    address = ", ".join([p for p in address_parts if p]) or None
    contact_email = parsed["correo_general"] or parsed["correo_fe"]
    whatsapp = parsed["whatsapp_emp"] or parsed["whatsapp_ger"] or parsed["telefono"]
    return {
        "name": name,
        "slug": slug,
        "summary": parsed["sector"],
        "description": description,
        "email": contact_email,
        "whatsapp": whatsapp,
        "phone": parsed["telefono"],
        "address": address,
        "website": parsed["sitio_web"],
        "facebook": parsed["facebook"],
        "instagram": parsed["instagram"],
    }


def main():
    if not JSON_FILE.exists():
        raise SystemExit(f"No se encontró el archivo {JSON_FILE}")
    payload = json.loads(JSON_FILE.read_text(encoding="utf-8"))
    rows = []
    slug_set: set[str] = set()
    for entry in payload:
        try:
            rows.append(build_row(entry, slug_set))
        except Exception as exc:
            print(f"⚠️  No se pudo procesar un registro: {exc}")
    timestamp = int(time.time())

    columns = [
        "name",
        "slug",
        "summary",
        "description",
        "email",
        "whatsapp",
        "phone",
        "address",
        "latitude",
        "longitude",
        "website",
        "facebook",
        "instagram",
        "linkedin",
        "tiktok",
        "youtube",
        "logo_path",
        "cover_path",
        "is_active",
        "show_on_home",
        "created_at",
        "updated_at",
    ]

    values_sql = []
    for row in rows:
        record = [
            sql_escape(row["name"]),
            sql_escape(row["slug"]),
            sql_escape(row["summary"]),
            sql_escape(row["description"]),
            sql_escape(row["email"]),
            sql_escape(row["whatsapp"]),
            sql_escape(row["phone"]),
            sql_escape(row["address"]),
            "NULL",
            "NULL",
            sql_escape(row["website"]),
            sql_escape(row["facebook"]),
            sql_escape(row["instagram"]),
            "NULL",
            "NULL",
            "NULL",
            "NULL",
            "NULL",
            "1",
            "0",
            str(timestamp),
            str(timestamp),
        ]
        values_sql.append(f"({', '.join(record)})")

    sql_content = [
        "-- Archivo generado automáticamente desde aliados_exportados.json",
        "INSERT INTO business (",
        "    name, slug, summary, description, email, whatsapp, phone, address,",
        "    latitude, longitude, website, facebook, instagram, linkedin, tiktok,",
        "    youtube, logo_path, cover_path, is_active, show_on_home, created_at, updated_at",
        ") VALUES",
        ",\n".join(values_sql) + ";",
    ]

    SQL_FILE.write_text("\n".join(sql_content), encoding="utf-8")
    print(f"Se generó el archivo SQL con {len(rows)} registros en {SQL_FILE}")

    category_slug_set: set[str] = set()
    categories: dict[str, dict] = {}
    for row in rows:
        raw_sector = row.get("summary")
        sector = clean(raw_sector) if isinstance(raw_sector, str) else None
        if not sector:
            continue
        name = sector.upper()
        if name in categories:
            continue
        slug = slugify(name, "categoria", f"cat-{len(categories)+1}", category_slug_set).upper()
        categories[name] = {
            "name": name,
            "slug": slug,
            "description": f"SECTOR {name}",
        }

    if categories:
        category_values = [
            f"({sql_escape(cat['name'])}, {sql_escape(cat['slug'])}, {sql_escape(cat['description'])}, {timestamp}, {timestamp})"
            for cat in categories.values()
        ]
        category_sql = [
            "-- Categorías generadas automáticamente desde aliados_exportados.json",
            "INSERT INTO category (name, slug, description, created_at, updated_at) VALUES",
            ",\n".join(category_values),
            "ON DUPLICATE KEY UPDATE",
            "    name = VALUES(name),",
            "    description = VALUES(description),",
            "    updated_at = VALUES(updated_at);",
            "",
            "INSERT INTO business_category (business_id, category_id)",
            "SELECT b.id, c.id",
            "FROM business b",
            "JOIN category c ON c.name = UPPER(TRIM(b.summary))",
            "LEFT JOIN business_category bc ON bc.business_id = b.id AND bc.category_id = c.id",
            "WHERE b.summary IS NOT NULL",
            "  AND TRIM(b.summary) <> ''",
            "  AND bc.business_id IS NULL;",
        ]
        CATEGORY_SQL_FILE.write_text("\n".join(category_sql), encoding="utf-8")
        print(f"Se generó el archivo SQL de categorías en {CATEGORY_SQL_FILE}")
    else:
        print("No se detectaron sectores para crear categorías.")


if __name__ == "__main__":
    main()

