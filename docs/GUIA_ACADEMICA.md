# BookSwap - Aplicación de Intercambio de Libros

## Integrantes

- Juan Isaías García
- Edgar Jaziel Onchi Andrade

## Descripción del proyecto

BookSwap es una aplicación web para intercambio de libros entre usuarios. La idea principal del proyecto es que una persona pueda crear su perfil, registrar libros que tiene disponibles, buscar libros de otros usuarios y crear solicitudes de intercambio.

La aplicación también permite enviar mensajes, recibir notificaciones, aceptar o rechazar solicitudes, consultar historial de intercambios, calificar usuarios y quitar libros de la lista de disponibles cuando ya fueron intercambiados o ya no se quieren ofrecer.

El proyecto fue desarrollado para la materia de Tópicos de Desarrollo Web, aplicando la metodología Spec Driven Development (SDD) con apoyo de Cursor y OpenSpec.

## Objetivo del proyecto

Desarrollar una aplicación funcional de intercambio de libros que permita a los usuarios publicar libros disponibles, buscar libros de otros usuarios y gestionar solicitudes de intercambio de manera sencilla.

## Funcionalidades principales

La aplicación cumple con las 10 historias de usuario solicitadas para el proyecto:

1. Crear perfil de usuario con datos de contacto y preferencias literarias.
2. Añadir libros al perfil del usuario.
3. Buscar libros disponibles por título, autor, género o ubicación.
4. Iniciar solicitudes de intercambio con otros usuarios.
5. Calificar y comentar usuarios después de un intercambio.
6. Recibir notificaciones de solicitudes o cambios de estado.
7. Aceptar o rechazar solicitudes de intercambio.
8. Enviar mensajes entre usuarios para negociar detalles.
9. Consultar historial de intercambios.
10. Eliminar libros de la lista de disponibles cambiando su estado a no disponible.

## Tecnologías utilizadas

El proyecto utiliza las siguientes tecnologías:

- PHP 8
- MySQL
- XAMPP
- phpMyAdmin
- Composer
- PDO
- HTML
- CSS
- JavaScript
- Cursor
- OpenSpec

## Estructura general del proyecto

La estructura principal del proyecto es la siguiente:

    bookswap/
    ├── public/
    │   ├── index.php
    │   ├── frontend.html
    │   └── assets/
    │       ├── app.js
    │       └── styles.css
    ├── src/
    │   ├── Controllers/
    │   ├── Models/
    │   ├── Repositories/
    │   ├── Http/
    │   └── Utils/
    ├── database/
    │   └── migrations/
    ├── vendor/
    ├── .env
    ├── composer.json
    └── README.md

## Base de datos

El proyecto usa una base de datos en MySQL llamada:

    bookswap

Las tablas principales son:

- usuarios
- libros
- solicitudes_intercambio
- mensajes
- notificaciones
- historial_intercambios
- calificaciones

## Configuración del archivo .env

El archivo .env debe estar en la raíz del proyecto y debe contener los datos de conexión a MySQL.

Ejemplo de configuración:

    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_NAME=bookswap
    DB_USER=root
    DB_PASS=

Si el usuario de MySQL tiene contraseña, se debe colocar en DB_PASS.

## Cómo ejecutar el proyecto desde cero

### 1. Abrir XAMPP

Abrir XAMPP Control Panel y activar:

- Apache
- MySQL

Ambos servicios deben aparecer en color verde.

### 2. Abrir el proyecto

Abrir Cursor y seleccionar la carpeta del proyecto:

    RUTA_LOCAL/bookswap

También se puede abrir desde PowerShell entrando a la carpeta:

    cd RUTA_LOCAL/bookswap

### 3. Ejecutar el servidor local de PHP

Dentro de la carpeta del proyecto, ejecutar:

    php -S localhost:8080 -t public

Debe aparecer un mensaje parecido a:

    PHP Development Server started
    Listening on http://localhost:8080

No se debe cerrar esa terminal mientras se esté usando la aplicación.

### 4. Abrir la interfaz web

En el navegador abrir:

    http://localhost:8080/frontend.html

Desde esa pantalla se pueden probar las funciones principales de BookSwap.

### 5. Abrir phpMyAdmin

En otra pestaña del navegador abrir:

    http://localhost/phpmyadmin

Después entrar a la base de datos:

    bookswap

Desde phpMyAdmin se puede verificar que los datos se guarden correctamente en las tablas.

## Cómo probar las 10 funciones desde la interfaz

### 1. Crear perfil

En la sección "1. Crear perfil", llenar los datos del usuario:

- Nombre
- Correo
- Teléfono
- Ubicación
- Preferencias literarias
- Contraseña

Después dar clic en "Registrar usuario".

Verificación:

    phpMyAdmin > bookswap > usuarios

Ahí debe aparecer el usuario registrado.

### 2. Añadir libro

En la sección "2. Añadir libro", llenar:

- ID del usuario
- Título
- Autor
- Género
- Descripción del estado
- Ubicación

Después dar clic en "Registrar libro".

Verificación:

    phpMyAdmin > bookswap > libros

El libro debe aparecer relacionado con el usuario mediante el campo usuario_id.

### 3. Buscar libros

En la sección "3. Buscar libros", escribir una palabra de búsqueda, por ejemplo:

    Aguascalientes

También se puede buscar por título, autor o género.

Después dar clic en "Buscar".

La interfaz debe mostrar los libros disponibles que coincidan con la búsqueda.

### 4. Crear solicitud de intercambio

En la sección "4. Crear solicitud de intercambio", llenar:

- ID usuario solicitante
- ID usuario receptor
- ID libro solicitado
- Mensaje inicial

Después dar clic en "Crear solicitud".

Verificación:

    phpMyAdmin > bookswap > solicitudes_intercambio

La solicitud debe aparecer con estado pendiente.

### 5. Calificar y comentar

En la sección "5. Calificar y comentar", llenar:

- ID usuario calificado
- ID usuario calificador
- ID solicitud
- Puntuación
- Comentario

Después dar clic en "Registrar calificación".

Para consultar las calificaciones, escribir el ID del usuario y dar clic en "Ver calificaciones".

Verificación:

    phpMyAdmin > bookswap > calificaciones

### 6. Notificaciones

En la sección "6. Notificaciones", escribir el ID del usuario y dar clic en "Ver notificaciones".

El sistema muestra notificaciones cuando un usuario recibe una solicitud de intercambio o cuando una solicitud es aceptada o rechazada.

Verificación:

    phpMyAdmin > bookswap > notificaciones

### 7. Aceptar o rechazar solicitudes

En la sección "7. Aceptar o rechazar solicitudes", primero dar clic en "Ver solicitudes".

Después escribir el ID de una solicitud pendiente y dar clic en:

- Aceptar solicitud
- Rechazar solicitud

Verificación:

    phpMyAdmin > bookswap > solicitudes_intercambio

El estado debe cambiar a aceptada o rechazada.

### 8. Enviar mensajes

En la sección "8. Enviar mensajes", llenar:

- ID solicitud
- ID emisor
- ID receptor
- Mensaje

Después dar clic en "Enviar mensaje".

Para consultar los mensajes, escribir el ID de la solicitud y dar clic en "Consultar mensajes".

Verificación:

    phpMyAdmin > bookswap > mensajes

### 9. Historial

En la sección "9. Historial", dar clic en "Ver historial".

El historial muestra los intercambios que fueron aceptados.

Verificación:

    phpMyAdmin > bookswap > historial_intercambios

Las solicitudes pendientes, aceptadas o rechazadas también pueden verse en:

    phpMyAdmin > bookswap > solicitudes_intercambio

### 10. Eliminar libro de disponibles

En la sección "10. Eliminar libro de disponibles", escribir el ID del libro y dar clic en "Marcar como no disponible".

Esto no borra el libro de la base de datos. Solo cambia el campo disponible de 1 a 0.

Verificación:

    phpMyAdmin > bookswap > libros

El libro debe aparecer con:

    disponible = 0

## Explicación breve del funcionamiento

La aplicación funciona de la siguiente manera:

1. El usuario interactúa con la interfaz web desde el navegador.
2. La interfaz usa JavaScript para enviar solicitudes a la API.
3. La API está desarrollada en PHP.
4. Los controladores reciben las solicitudes y validan la información.
5. Los repositorios se conectan con MySQL usando PDO.
6. La información se guarda o consulta desde la base de datos bookswap.

## Arquitectura utilizada

BookSwap utiliza una arquitectura monolítica con separación tipo MVC.

Esto significa que todo el proyecto está dentro de una misma aplicación, pero organizado por carpetas y responsabilidades.

- public/frontend.html contiene la interfaz web.
- public/assets/styles.css contiene los estilos.
- public/assets/app.js conecta la interfaz con la API.
- public/index.php es el punto de entrada de la API.
- src/Controllers contiene los controladores.
- src/Models contiene los modelos.
- src/Repositories contiene las consultas y acceso a base de datos.
- MySQL almacena la información.

Esta arquitectura se eligió porque es sencilla de entender, fácil de ejecutar de manera local y adecuada para un proyecto escolar.

## Uso de Spec Driven Development

El proyecto fue desarrollado aplicando Spec Driven Development, o SDD.

Primero se definieron las funciones que debía cumplir BookSwap con base en las historias de usuario. Después se dividió el desarrollo en fases, como usuarios, libros, solicitudes, mensajes, notificaciones, historial, calificaciones e interfaz web.

Cursor y OpenSpec se usaron como apoyo para generar código, pero el equipo revisó, probó y corrigió las funciones hasta que cumplieran con lo solicitado.

## Estado del proyecto

El proyecto se encuentra funcional para demostración escolar.

Permite probar desde la interfaz las funciones principales solicitadas para la aplicación de intercambio de libros.

## Recomendaciones para ejecutar correctamente

Antes de usar el proyecto, revisar lo siguiente:

- XAMPP debe estar abierto.
- Apache debe estar activo.
- MySQL debe estar activo.
- La base de datos bookswap debe existir.
- El archivo .env debe estar configurado.
- El servidor debe estar corriendo con php -S localhost:8080 -t public.
- La interfaz debe abrirse desde http://localhost:8080/frontend.html.
- Para verificar los registros, usar phpMyAdmin.

## Autores

Proyecto realizado por:

- Juan Isaías García
- Edgar Jaziel Onchi Andrade
