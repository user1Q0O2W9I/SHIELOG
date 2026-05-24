# Guia de defensa del proyecto

## 1. Objetivo

ShieldLog es una aplicacion web didactica de ciberseguridad. Su objetivo no es sustituir a herramientas profesionales, sino demostrar conocimientos de desarrollo web, seguridad basica, base de datos y procesamiento de informacion.

## 2. Arquitectura

El proyecto usa una estructura MVC sencilla:

- `public/index.php`: recibe todas las peticiones y decide que controlador ejecutar.
- `controllers`: contienen la logica principal de cada caso de uso.
- `models`: comunican la aplicacion con MySQL usando PDO y consultas preparadas.
- `views`: contienen HTML con pequenas partes de PHP para pintar datos.
- `config/Database.php`: centraliza la conexion con la base de datos.
- `uploads/logs`: guarda archivos subidos por empresas.

Esta separacion permite explicar que la aplicacion no mezcla todo el codigo en un unico archivo.

## 3. Seguridad aplicada

- Consultas preparadas con PDO para reducir riesgo de SQL Injection.
- Hash de contrasenas con `password_hash`.
- Verificacion de login con `password_verify`.
- Control de sesiones con `session_start` y `session_regenerate_id`.
- Token CSRF en formularios POST.
- Validacion del rol `empresa` antes de entrar al modulo de logs.
- Validacion de extension y tamano maximo en la subida de archivos.
- Salida HTML escapada con `htmlspecialchars`.

## 4. Modulo de analisis de URLs

El analisis es heuristico. Cada regla suma puntos:

- HTTP en vez de HTTPS: +20.
- URL demasiado larga: +15.
- IP en vez de dominio: +25.
- Palabras sospechosas como `login`, `verify`, `account`: +10 por palabra.
- Caracter `@`: +20.
- Muchos guiones o subdominios: +10 o +15.

Clasificacion final:

- 0 a 29 puntos: Seguro.
- 30 a 59 puntos: Sospechoso.
- 60 o mas puntos: Peligroso.

El resultado se guarda en la tabla `analisis_url` junto a la puntuacion y los detalles en JSON.

## 5. Modulo de analisis de logs

Solo pueden usarlo usuarios con rol `empresa`. El flujo es:

1. El usuario sube un archivo `.log` o `.txt`.
2. PHP valida extension y tamano.
3. El archivo se guarda con un nombre aleatorio.
4. El controlador lee el contenido linea a linea con `fgets`.
5. Se detectan patrones sospechosos mediante expresiones regulares.
6. Se cuentan amenazas, lineas peligrosas e IPs repetidas.
7. Se calcula el riesgo: bajo, medio o alto.

Amenazas detectadas:

- Codigo peligroso: `eval`, `exec`, `cmd`, `powershell`.
- Descargas remotas: `wget`, `curl`, `Invoke-WebRequest`.
- Login fallido: `failed password`, `login failed`, `authentication failure`.
- Posible base64.
- Repeticion excesiva de una misma IP.

## 6. Posibles mejoras

- Asociar analisis de URL a usuarios concretos.
- Anadir paginacion en historiales.
- Permitir descargar informes en PDF.
- Usar una API externa de reputacion de dominios.
- Crear pruebas automatizadas para los analizadores.
- Anadir panel de administracion.

