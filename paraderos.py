import sys
import json
import urllib.request

# Coordenadas de cada localidad (limites aproximados)
LOCALIDADES = {
    "Usaquen": {"lat_min": 4.72, "lat_max": 4.82, "lon_min": -74.07, "lon_max": -73.98},
    "Chapinero": {"lat_min": 4.63, "lat_max": 4.72, "lon_min": -74.07, "lon_max": -74.03},
    "Santa Fe": {"lat_min": 4.59, "lat_max": 4.63, "lon_min": -74.08, "lon_max": -74.04},
    "San Cristobal": {"lat_min": 4.55, "lat_max": 4.60, "lon_min": -74.10, "lon_max": -74.05},
    "Usme": {"lat_min": 4.45, "lat_max": 4.56, "lon_min": -74.15, "lon_max": -74.08},
    "Tunjuelito": {"lat_min": 4.54, "lat_max": 4.59, "lon_min": -74.15, "lon_max": -74.10},
    "Bosa": {"lat_min": 4.56, "lat_max": 4.63, "lon_min": -74.18, "lon_max": -74.13},
    "Kennedy": {"lat_min": 4.59, "lat_max": 4.66, "lon_min": -74.18, "lon_max": -74.10},
    "Fontibon": {"lat_min": 4.63, "lat_max": 4.70, "lon_min": -74.20, "lon_max": -74.13},
    "Engativa": {"lat_min": 4.67, "lat_max": 4.76, "lon_min": -74.16, "lon_max": -74.08},
    "Suba": {"lat_min": 4.70, "lat_max": 4.80, "lon_min": -74.13, "lon_max": -74.03},
    "Barrios Unidos": {"lat_min": 4.65, "lat_max": 4.71, "lon_min": -74.08, "lon_max": -74.04},
    "Teusaquillo": {"lat_min": 4.62, "lat_max": 4.67, "lon_min": -74.08, "lon_max": -74.05},
    "Los Martires": {"lat_min": 4.59, "lat_max": 4.63, "lon_min": -74.10, "lon_max": -74.07},
    "Antonio Narino": {"lat_min": 4.56, "lat_max": 4.60, "lon_min": -74.12, "lon_max": -74.08},
    "Puente Aranda": {"lat_min": 4.60, "lat_max": 4.65, "lon_min": -74.13, "lon_max": -74.09},
    "La Candelaria": {"lat_min": 4.59, "lat_max": 4.61, "lon_min": -74.08, "lon_max": -74.06},
    "Rafael Uribe Uribe": {"lat_min": 4.51, "lat_max": 4.57, "lon_min": -74.12, "lon_max": -74.07},
    "Ciudad Bolivar": {"lat_min": 4.44, "lat_max": 4.53, "lon_min": -74.17, "lon_max": -74.10},
    "Sumapaz": {"lat_min": 4.30, "lat_max": 4.45, "lon_min": -74.30, "lon_max": -74.15},
}

API_URL = "https://transport.opendatasoft.com/api/explore/v2.1/catalog/datasets/paraderos-sitp/records"


def consultar_api(offset=0, limite=100):
    url = f"{API_URL}?limit={limite}&offset={offset}&select=geopoint"
    respuesta = urllib.request.urlopen(url, timeout=15)
    return json.loads(respuesta.read().decode())


def contar_paraderos(localidad):
    limites = LOCALIDADES[localidad]
    total = 0
    pagina = 0

    while pagina < 50:
        datos = consultar_api(offset=pagina * 100)

        if not datos.get("results"):
            break

        for registro in datos["results"]:
            punto = registro.get("geopoint", {})
            lat = punto.get("lat")
            lon = punto.get("lon")

            if lat is None or lon is None:
                continue

            if (lat >= limites["lat_min"] and lat <= limites["lat_max"] and
                lon >= limites["lon_min"] and lon <= limites["lon_max"]):
                total += 1

        pagina += 1

    return total


def main():
    if len(sys.argv) < 2:
        print(json.dumps({"error": "No se envio la localidad"}))
        return

    localidad = sys.argv[1]

    if localidad not in LOCALIDADES:
        print(json.dumps({"error": f"Localidad '{localidad}' no encontrada"}))
        return

    try:
        total = contar_paraderos(localidad)
        print(json.dumps({"localidad": localidad, "total": total}))
    except Exception as e:
        print(json.dumps({"error": str(e)}))


if __name__ == "__main__":
    main()
