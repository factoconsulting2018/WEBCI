# -*- coding: utf-8 -*-
"""
Información de la Cámara de Inversionistas de Costa Rica y sus beneficios para 2025.
Datos extraídos del documento: BENEFICIOS-CI-2025-NOV-2024.pdf
"""

camara_inversionistas_cr = {
    "organizacion": {
        [cite_start]"nombre": "CÁMARA DE INVERSIONISTAS DE COSTA RICA", # [cite: 3, 11]
        [cite_start]"grupo": "GRUPO EMPRESARIAL", # [cite: 8]
        [cite_start]"periodo_beneficios": 2025, # [cite: 9]
        "contacto": {
            [cite_start]"telefono": "4070-0485", # [cite: 4, 20]
            [cite_start]"ubicacion": "San Ramón, Alajuela", # [cite: 5]
            [cite_start]"sitio_web": "camarainversionistas.com" # [cite: 6, 21]
        }
    },
    "descripcion_general": {
        [cite_start]"quienes_somos": "La Cámara de Inversionistas de Costa Rica somos una organización dedicada a apoyar y fortalecer a los emprendedores, pequeñas y medianas empresas (PYMES), y empresarios en su crecimiento y éxito empresarial.", # [cite: 13]
        [cite_start]"concepto_clave": "Creemos en el concepto de \"capitalismo solidario\", donde nuestros aliados no solo reciben asesoría y recursos, sino que también colaboran entre ellos, compartiendo conocimientos y creando redes de apoyo.", # [cite: 14]
        [cite_start]"mision": "Proveer a nuestros aliados con acceso a asesorías estratégicas, servicios financieros, beneficios exclusivos, capacitación continua y oportunidades de networking, promoviendo el desarrollo sostenible y el éxito de cada negocio que forma parte de nuestra comunidad.", # [cite: 16]
        "vision": "Es ser el pilar que sostiene y potencia el crecimiento de los empresarios y emprendedores de nuestro país." [cite_start]# [cite: 18]
    },
    "tabla_de_beneficios": [
        {
            [cite_start]"tipo_servicio": "Servicios Financieros y Tributarios", # [cite: 26]
            "descripcion": [
                [cite_start]"Asesoría para su mejor financiamiento en diversas entidades debidamente inscritas ante SUGEF.", # [cite: 28]
                [cite_start]"Servicios de presentación de IVA y renta. Contabilidad. Asesoría Tributaria.", # [cite: 30]
                "Acceso a nuestro programa de microcréditos*." [cite_start]# [cite: 31]
            ]
        },
        {
            [cite_start]"tipo_servicio": "Servicios Empresariales y Administrativos", # [cite: 33]
            "descripcion": [
                [cite_start]"Servicio de facturación electrónica.", # [cite: 34]
                [cite_start]"Asesoría completa para la obtención del sello PYME del MEIC Exención IVA en alquileres y muchos beneficios", # [cite: 35, 36]
                [cite_start]"Creación de sitios web.", # [cite: 36]
                [cite_start]"Asesoría en Registro de Marca.", # [cite: 37]
                [cite_start]"Acceso preferencial a nuestro programa de oficina virtual.", # [cite: 38]
                "Acceso preferencial a nuestro servicio de call center." [cite_start]# [cite: 38]
            ]
        },
        {
            [cite_start]"tipo_servicio": "Beneficios de Salud y Bienestar", # [cite: 40]
            "descripcion": [
                [cite_start]"Acceso a la Cobertura de Gastos Fúnebres.", # [cite: 41]
                "Acceso a nuestra red privada de salud empresarial." [cite_start]# [cite: 42]
            ]
        },
        {
            [cite_start]"tipo_servicio": "Servicios de Promoción y Publicidad", # [cite: 43]
            "descripcion": [
                [cite_start]"Acceso preferencial en nuestra revista digital.", # [cite: 44]
                "Acceso a la promoción de su negocio dentro de un encadenamiento entre nuestro club de clientes y redes sociales de la Cámara de Inversionistas." [cite_start]# [cite: 44]
            ]
        },
        {
            [cite_start]"tipo_servicio": "Capacitación y Desarrollo", # [cite: 45]
            "descripcion": [
                [cite_start]"Participación en talleres, ruedas de negocios, capacitaciones, cursos, charlas y ferias estratégicas.", # [cite: 46]
                "Participar en reconocimientos anuales." [cite_start]# [cite: 46]
            ]
        },
        {
            [cite_start]"tipo_servicio": "Infraestructura y Espacios", # [cite: 49]
            "descripcion": [
                [cite_start]"Uso 2 horas al mes mediante previa cita de nuestras oficinas, sala de capacitaciones (capacidad 20 personas) y sala de juntas (capacidad 8 personas).", # [cite: 50]
                "1 hora diaria de parqueo gratuito en el estacionamiento del Templo Parroquial San Ramón. No aplica Domingos." [cite_start]# [cite: 51]
            ]
        },
        {
            [cite_start]"tipo_servicio": "Descuentos y Convenios Especiales", # [cite: 52, 54]
            "descripcion": [
                [cite_start]"Acceso a su carnet con importantes descuentos en diversos locales comerciales, clínicas dentales, veterinarias, entre otros.", # [cite: 53]
                "Servicio de rent a car sin depósito de garantía con autos propios y cuenta corporativa." [cite_start]# [cite: 55]
            ]
        },
        {
            [cite_start]"tipo_servicio": "Programas de Apoyo y Desarrollo Laboral", # [cite: 56, 58]
            "descripcion": [
                [cite_start]"Acceso a nuestro programa de pasantías donde su empresa podrá contar con pasantes.", # [cite: 57]
                "Acceso a nuestra bolsa de empleo." [cite_start]# [cite: 57]
            ]
        }
    ]
}

if __name__ == "__main__":
    # Ejemplo de cómo acceder a la información
    print(f"Organización: {camara_inversionistas_cr['organizacion']['nombre']}")
    print(f"Teléfono: {camara_inversionistas_cr['organizacion']['contacto']['telefono']}")
    print("\n--- Misión ---")
    print(camara_inversionistas_cr['descripcion_general']['mision'])
    print("\n--- Algunos Beneficios ---")
    for beneficio in camara_inversionistas_cr['tabla_de_beneficios']:
        if beneficio['tipo_servicio'] == "Servicios Financieros y Tributarios":
            print(f"\nCategoría: {beneficio['tipo_servicio']}")
            for desc in beneficio['descripcion']:
                print(f"  - {desc}")