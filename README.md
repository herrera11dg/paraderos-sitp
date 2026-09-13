# Paraderos SITP - Bogotá

Aplicación web que integra PHP y Python para consultar la API pública de Datos Abiertos Bogotá (SITP) y mostrar la cantidad de paraderos por localidad.

## Cómo ejecutar

1. Instalar [XAMPP](https://www.apachefriends.org/) (incluye PHP)
2. Instalar [Python 3](https://www.python.org/downloads/)
3. Copiar `paraderos_sitp.php` y `paraderos.py` en `C:\xampp\htdocs\paraderos-sitp\`
4. Iniciar Apache desde el panel de XAMPP
5. Abrir en el navegador: `http://localhost/paraderos-sitp/paraderos_sitp.php`

## Funcionalidad

- Seleccionar una localidad de Bogotá desde un menú desplegable
- PHP ejecuta un script de Python que consulta la API de Datos Abiertos
- Python filtra los paraderos por coordenadas de la localidad
- Se muestra la cantidad total de paraderos encontrados

## Tecnologías

- PHP 8.2 (interfaz web)
- Python 3.12 (consumo y procesamiento de la API)
- API REST (OpenDataSoft - Datos Abiertos Bogotá)
- HTML/CSS

## Estructura

```
paraderos-sitp/
├── paraderos_sitp.php   # Interfaz web (formulario y resultado)
├── paraderos.py         # Script Python que consulta la API
└── README.md
```
