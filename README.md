# BookSwap

> Rama de demo `codex/public-demo`: preparación de PHP y MySQL para publicación. La versión académica está en `main`. Consulta [la guía de despliegue](docs/DEMO_PUBLICA.md).

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

## Capturas del sistema

Capturas de la ejecución local con MySQL y datos ficticios.

### Registro de un libro

Formulario de alta de un libro y confirmación de registro correcto.

![Registro de un libro en BookSwap](screenshots/registro-libro.png)

### Búsqueda de libros

Búsqueda por título y resultado con autor, género y estado físico del libro.

![Búsqueda de El principito](screenshots/busqueda-libros.png)

### Disponibilidad y propietario

Detalle del resultado con ubicación, disponibilidad e identificadores del libro y su propietario, utilizados en la solicitud de intercambio.

![Disponibilidad y propietario del libro](screenshots/detalle-libro.png)

### Solicitud de intercambio

Captura de los usuarios participantes, el libro solicitado y el mensaje inicial.

![Formulario de solicitud de intercambio](screenshots/solicitud-formulario.png)

Confirmación del registro de la solicitud.

![Solicitud creada correctamente](screenshots/solicitud-creada.png)

### Consulta de solicitudes pendientes

Listado de la solicitud de ejemplo con sus participantes y el libro asociado.

![Participantes y libro de la solicitud](screenshots/solicitud-listado.png)

Detalle del estado pendiente y del mensaje inicial.

![Estado pendiente de la solicitud](screenshots/solicitud-pendiente.png)

### Mensajes entre participantes

Formulario de mensaje asociado a la solicitud de intercambio.

![Formulario para enviar un mensaje](screenshots/mensaje-formulario.png)

Confirmación del envío del mensaje del solicitante al propietario del libro.

![Mensaje enviado correctamente](screenshots/mensaje-enviado.png)

Respuesta enviada por el propietario al solicitante dentro de la misma solicitud.

![Respuesta del propietario del libro](screenshots/mensaje-respuesta.png)

### Conversación guardada

Consulta de los mensajes asociados a la solicitud de intercambio, enviados en ambas direcciones.

![Mensaje del solicitante guardado](screenshots/conversacion-solicitante.png)

![Respuesta del propietario guardada](screenshots/conversacion-propietario.png)

### Aceptación de la solicitud

Confirmación de la aceptación y consulta de la solicitud con su nuevo estado.

![Confirmación de aceptación](screenshots/solicitud-aceptacion-confirmada.png)

![Solicitud con estado aceptada](screenshots/solicitud-aceptada.png)

<details>
<summary>Ver detalle adicional de la solicitud aceptada</summary>

![Detalle de la solicitud aceptada](screenshots/solicitud-aceptada-detalle.png)

</details>

### Historial del intercambio

Registro generado al aceptar la solicitud, con sus participantes y el estado que almacena esta versión del sistema.

![Registro en el historial](screenshots/historial-intercambio.png)

![Detalle del registro del historial](screenshots/historial-intercambio-detalle.png)

### Calificaciones de usuarios

Registro de una calificación asociada a la solicitud de intercambio.

![Calificación registrada correctamente](screenshots/calificacion-registrada.png)

Consulta de la calificación con sus participantes y solicitud relacionada.

![Consulta de la calificación](screenshots/calificacion-consulta.png)

Puntuación de ejemplo y comentario almacenados.

![Puntuación y comentario de la calificación](screenshots/calificacion-detalle.png)

### Notificaciones

Notificación al solicitante cuando se acepta su solicitud de intercambio.

![Notificación de solicitud aceptada](screenshots/notificacion-solicitud-aceptada.png)

### Baja lógica de un libro

El libro se marca como no disponible sin eliminar su registro de MySQL.

![Confirmación de libro no disponible](screenshots/libro-no-disponible.png)

Al repetir la búsqueda, el libro deja de aparecer entre los disponibles.

![Búsqueda sin libros disponibles](screenshots/busqueda-sin-disponibles.png)

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
├── screenshots/            # Capturas del sistema con datos ficticios
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

En esta rama la interfaz utiliza la API del mismo dominio y puerto desde el que se abre. La versión académica de `main` conserva la dirección local original. Los comandos anteriores sirven la aplicación desde la raíz de ese servidor local; una instalación de Apache en una subcarpeta puede requerir ajustes adicionales de rutas.

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

La configuración de despliegue está en `render.yaml` y `Dockerfile`. La conexión a la base externa y el despliegue público están pendientes; ver [la guía de la demo](docs/DEMO_PUBLICA.md).
