# -*- coding: utf-8 -*-
"""
Este script lee el archivo CSV de aliados (pestaña MENSUAL),
carga los datos en memoria y luego los guarda permanentemente 
en un archivo JSON.
"""

import csv
import os
import json  # <-- Módulo necesario para guardar en formato JSON

# Nombre del archivo CSV que vamos a leer
NOMBRE_ARCHIVO_ENTRADA = 'MENSUAL.csv'

# Nombre del nuevo archivo que vamos a crear
NOMBRE_ARCHIVO_SALIDA = 'aliados_exportados.json'


def cargar_datos_aliados(archivo_csv):
    """
    Carga los datos de aliados desde un archivo CSV.
    Devuelve una lista de diccionarios.
    """
    datos_aliados = []
    ruta_script = os.path.dirname(os.path.abspath(__file__))
    ruta_archivo_csv = os.path.join(ruta_script, archivo_csv)

    if not os.path.exists(ruta_archivo_csv):
        print(f"Error: El archivo '{ruta_archivo_csv}' no se encontró.")
        return None

    try:
        with open(ruta_archivo_csv, mode='r', encoding='utf-8-sig') as file:
            lector_csv = csv.DictReader(file)
            for fila in lector_csv:
                datos_aliados.append(fila)
        return datos_aliados
    except Exception as e:
        print(f"Ocurrió un error al leer el archivo CSV: {e}")
        return None

def guardar_en_json(datos, nombre_archivo_salida):
    """
    Guarda una lista de diccionarios en un archivo JSON.
    """
    ruta_script = os.path.dirname(os.path.abspath(__file__))
    ruta_archivo_json = os.path.join(ruta_script, nombre_archivo_salida)

    try:
        # Abrir el archivo en modo escritura ('w') con codificación utf-8
        with open(ruta_archivo_json, mode='w', encoding='utf-8') as file:
            # json.dump escribe la variable 'datos' en el 'file'
            # indent=4 formatea el archivo para que sea legible
            # ensure_ascii=False permite guardar tildes y caracteres especiales
            json.dump(datos, file, indent=4, ensure_ascii=False)
        
        print(f"\n✅ ¡Información guardada con éxito!")
        print(f"   Se ha creado el archivo: {ruta_archivo_json}")

    except Exception as e:
        print(f"\n❌ Ocurrió un error al guardar el archivo JSON: {e}")


# --- Esta es la parte principal que se ejecuta ---
if __name__ == "__main__":
    print(f"Iniciando la carga de datos desde '{NOMBRE_ARCHIVO_ENTRADA}'...")
    
    # 1. Cargar los datos del CSV a la memoria
    aliados_mensuales = cargar_datos_aliados(NOMBRE_ARCHIVO_ENTRADA)
    
    if aliados_mensuales:
        print(f"¡Éxito! Se cargaron {len(aliados_mensuales)} registros de aliados.")
        
        # 2. Guardar los datos de la memoria a un archivo JSON
        guardar_en_json(aliados_mensuales, NOMBRE_ARCHIVO_SALIDA)
            
    else:
        print("No se pudieron cargar los datos.")