from fastapi import FastAPI, Request
from fastapi.responses import HTMLResponse
from fastapi.templating import Jinja2Templates
import pyodbc
import threading
import time

app = FastAPI()
templates = Jinja2Templates(directory="templates")

# Configura tu conexión a SQL Server
conn_str = (
    "DRIVER={ODBC Driver 17 for SQL Server};"
    "SERVER=192.168.19.4;"
    "DATABASE=SistGestion;"
    "UID=sa;"
    "PWD=Robin@2021"
)

datos_cache = []

consulta_sql = """
DECLARE @ultimo_instructivo INT;
SELECT @ultimo_instructivo = MAX(id_cab_instructivo) FROM inst_detalle_instructivo;

SELECT
    i.id_cab_instructivo AS 'Numero Instructivo',
    i.numero_pedido,
    i.var_etiquetada,
    e.codigo_emb AS embalaje,
    e.Descripcion_Embalaje, 
    e.Peso_Embalaje,
    et.Nombre_etiqueta,
    d.nombre_destino,
    pl.plu,
    CONCAT(ca.cod_categoria,' - ',ca.nombre_categoria) AS categoria,
    p.Descrip_pallet,
    i.altura_pallet,
    CONCAT(ap.altura,'/',ap.cajas) as Altura,
    i.observacion,
    c.nombre_calibre,
    i.cantidad_pedido
FROM inst_detalle_instructivo i
INNER JOIN inst_embalaje e ON e.id = i.id_embalaje
INNER JOIN inst_calibre c ON c.id = i.id_calibre
INNER JOIN inst_categoria ca ON ca.id = i.id_categoria
INNER JOIN inst_plu pl ON pl.id = i.id_plu
INNER JOIN inst_destino d ON d.id = i.id_destino
INNER JOIN inst_pallet p ON p.id = i.id_pallet
INNER JOIN inst_etiqueta et ON et.id = i.id_etiqueta
INNER JOIN inst_altura_pallet ap ON i.altura_pallet = ap.id
WHERE i.id_cab_instructivo = @ultimo_instructivo
"""

def refrescar_datos():
    global datos_cache
    while True:
        try:
            with pyodbc.connect(conn_str) as conn:
                cursor = conn.cursor()
                cursor.execute(consulta_sql)
                columnas = [column[0] for column in cursor.description]
                datos_cache = [dict(zip(columnas, row)) for row in cursor.fetchall()]
        except Exception as e:
            print("Error al refrescar datos:", e)
        time.sleep(600)  # 10 minutos

# Inicia el hilo en segundo plano
threading.Thread(target=refrescar_datos, daemon=True).start()

@app.get("/", response_class=HTMLResponse)
async def leer_pagina(request: Request):
    return templates.TemplateResponse("index.html", {"request": request})

from collections import defaultdict

@app.get("/datos")
async def obtener_datos():
    agrupado = defaultdict(lambda: {"calibres": set(), "categoria": None})

    for fila in datos_cache:
        clave = tuple((k, v) for k, v in fila.items() if k not in ['nombre_calibre', 'categoria'])
        agrupado[clave]["calibres"].add(fila['nombre_calibre'])
        agrupado[clave]["categoria"] = fila['categoria']

    resultado = []
    for clave, valores in agrupado.items():
        fila_dict = dict(clave)
        fila_dict['nombre_calibre'] = ' - '.join(sorted(valores["calibres"]))
        fila_dict['categoria'] = valores["categoria"]
        resultado.append(fila_dict)

    return resultado


