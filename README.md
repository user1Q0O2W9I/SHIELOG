# ShieldLog - Proyecto DAW

Aplicacion web de ciberseguridad desarrollada con PHP, MySQL, HTML, CSS y JavaScript, sin frameworks complejos. El proyecto usa una estructura MVC sencilla para que sea facil de entender, ampliar y defender en un Trabajo de Fin de Grado Superior.

## Modulos principales

- Analisis de URLs: aplica reglas heuristicas para detectar posibles URLs de phishing.
- Analisis de logs: zona privada para usuarios con rol `empresa`, con subida y analisis de archivos `.log` o `.txt`.

## Estructura del proyecto

```text
config/
  Database.php          Conexion PDO con MySQL.
controllers/
  AuthController.php    Registro, login y cierre de sesion.
  LogController.php     Subida, analisis e historial de logs.
  UrlController.php     Analisis e historial de URLs.
models/
  LogAnalysis.php       Acceso a datos de analisis de logs.
  UrlAnalysis.php       Acceso a datos de analisis de URLs.
  User.php              Acceso a datos de usuarios.
public/
  assets/
    css/styles.css      Estilos principales.
    js/main.js          Mejoras de interfaz y validaciones sencillas.
  index.php             Punto de entrada y router simple.
uploads/
  logs/.gitkeep         Carpeta donde se guardan los logs subidos.
views/
  auth/                 Formularios de login y registro.
  logs/                 Vista de analisis de logs.
  urls/                 Vista de analisis de URLs.
  layout/               Header, footer y componentes comunes.
database.sql            Script para crear la base de datos.
```

## Instalacion local

1. Copia el proyecto en una carpeta servida por Apache, por ejemplo `htdocs/shieldlog`.
2. Crea la base de datos importando `database.sql` desde phpMyAdmin o consola MySQL.
3. Revisa las credenciales de `config/Database.php`.
4. Asegurate de que `uploads/logs` tiene permisos de escritura.
5. Abre `http://localhost/shieldlog/public/`.

## Usuarios y roles

En el registro puedes elegir:

- `usuario`: puede analizar URLs y ver el historial general de URLs.
- `empresa`: puede analizar URLs y acceder tambien al modulo privado de logs.

Las contrasenas se guardan con `password_hash()` y se validan con `password_verify()`.

Usuarios demo incluidos en `database.sql`:

- Empresa: `empresa@demo.com`
- Usuario normal: `usuario@demo.com`
- Password de ambos: `password`

## Requisitos tecnicos

- PHP 8.0 o superior, porque se usan `match` y `str_contains`.
- MySQL 5.7 o superior, por el tipo de dato `JSON`.
- Apache o servidor compatible con PHP.

## Ideas para defender el proyecto

- El analisis de phishing es heuristico, no sustituye a una solucion profesional, pero permite explicar reglas de riesgo y puntuacion.
- El analisis de logs muestra como procesar archivos linea a linea, detectar patrones peligrosos y calcular un nivel de riesgo.
- La aplicacion incluye sesiones, roles, validacion de formularios, subida segura de archivos y consultas preparadas con PDO.
