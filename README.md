# BookSwap

Proyecto académico de una plataforma de intercambio de libros, con API REST en PHP y persistencia en MySQL.

## Creador y desarrollador

- Juan Isais Garcia

Desarrollado para Tópicos de Desarrollo Web con apoyo de Cursor y OpenSpec mediante Spec Driven Development. Desarrollado íntegramente por Juan Isais Garcia.

## Tecnologías

- Backend: PHP 8, Composer, PDO y API REST.
- Base de datos: MySQL, tablas InnoDB y claves foráneas.
- Frontend: HTML, CSS y JavaScript nativo.
- Pruebas existentes: PHPUnit.

Esta versión no utiliza Vue.js.

## Funcionalidades implementadas

- Registro y consulta de perfiles de usuario.
- Registro, búsqueda y consulta de libros disponibles.
- Creación, aceptación y rechazo de solicitudes de intercambio.
- Mensajes asociados a solicitudes.
- Notificaciones, historial y calificaciones.
- Baja lógica de libros mediante su disponibilidad.

El registro de perfiles no equivale a un sistema de inicio de sesión. La versión actual usa identificadores capturados en formularios; sus limitaciones se detallan en [la revisión técnica](docs/REVISION.md).

## Estructura

```text
bookswap/
├── public/                 # Interfaz y punto de entrada HTTP
│   ├── assets/
│   ├── frontend.html
│   └── index.php
├── src/
│   ├── Config/
│   ├── Controllers/
│   ├── Exceptions/
│   ├── Http/
│   ├── Models/
│   ├── Repositories/
│   ├── Services/
│   └── Utils/
├── database/migrations/    # Estructura SQL sin registros personales
├── tests/
├── docs/
├── .env.example
├── composer.json
└── composer.lock
```

## Ejecutar localmente

### Requisitos

PHP 8 con PDO y el controlador `pdo_mysql`, Composer y un servidor MySQL compatible. XAMPP puede proporcionar PHP y MySQL. Para las pruebas de PHPUnit también se necesitan sus extensiones de PHP requeridas por Composer.

### 1. Instalar dependencias

Desde la raíz del proyecto:

```powershell
composer install
Copy-Item .env.example .env
```

Editar `.env` con la conexión de tu equipo. Nunca publicar este archivo. El ejemplo de usuario `root` sin contraseña corresponde únicamente a una instalación local de XAMPP.

### 2. Crear e importar la base de datos

Desde phpMyAdmin, crear una base vacía llamada `bookswap` con cotejamiento `utf8mb4_unicode_ci`.

Seleccionar esa base e importar:

```text
database/migrations/001_create_bookswap_tables.sql
```

El archivo `001_create_database.sql` original contiene comentarios de ayuda, no instrucciones SQL activas. La migración de tablas incluye siete tablas y sus relaciones, pero no los registros de una instalación anterior.

### 3. Iniciar la aplicación

Con MySQL activo:

```powershell
php -S localhost:8080 -t public
```

Abrir:

- Interfaz: `http://localhost:8080/frontend.html`.
- Estado de API y conexión: `http://localhost:8080/api/health`.

La interfaz original tiene fijada la API en `http://localhost:8080/api`; usar ese puerto para reproducir esta versión. Antes de cambiar el dominio o publicar una demo se debe adaptar esa dirección. Los comandos anteriores sirven la aplicación desde la raíz de ese servidor local; una instalación de Apache en una subcarpeta puede requerir ajustes adicionales de rutas.

## Probar el flujo principal

Usar únicamente información ficticia:

1. Registrar dos usuarios y conservar sus identificadores.
2. Publicar un libro del segundo usuario.
3. Buscar el libro por título, autor, género o ubicación.
4. Crear una solicitud del primer usuario al propietario del libro.
5. Consultar las notificaciones y mensajes.
6. Aceptar o rechazar una solicitud pendiente.
7. Consultar el historial y registrar una calificación de prueba.
8. Marcar el libro como no disponible cuando corresponda.

La [guía académica](docs/GUIA_ACADEMICA.md) conserva las instrucciones originales del proyecto.

## Recursos principales de la API

| Recurso | Métodos disponibles |
| --- | --- |
| `/api/health` | GET |
| `/api/users` | GET, POST |
| `/api/books` | GET, POST |
| `/api/books/search?q=texto` | GET |
| `/api/books/{id}` | GET, DELETE |
| `/api/exchanges` | GET, POST |
| `/api/exchanges/{id}/accept` | PUT |
| `/api/exchanges/{id}/reject` | PUT |
| `/api/exchanges/{id}/messages` | GET |
| `/api/messages` | POST |
| `/api/history` | GET |
| `/api/ratings` | POST |
| `/api/users/{id}/notifications` | GET |
| `/api/users/{id}/ratings` | GET |

## Pruebas existentes

```powershell
composer test
```

El ZIP incluye una prueba del controlador de estado. En modo CLI ese controlador omite la conexión real a MySQL, de modo que pasar esta prueba no demuestra que la base de datos o los intercambios funcionen. Es necesario validar el flujo con MySQL por separado.

## Publicación y demo

GitHub alojará el código, el esquema SQL y las instrucciones. No ejecutará el backend PHP ni almacenará la base de datos MySQL de la aplicación.

La versión pública debe excluir `.env`, `vendor/`, cachés y datos personales. `composer.lock` y `.env.example` sí se incluyen para reproducir la instalación.

La publicación de una demo en Internet se prepara por separado: requiere ejecutar PHP y conectar MySQL. Consultar primero los pendientes de [la revisión técnica](docs/REVISION.md).

No se ha desplegado una demo pública de esta copia.
