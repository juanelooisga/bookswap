# Revisión previa a GitHub y a una demo

Revisión del ZIP recibido el 28 de octubre de 2026. Esta copia conserva las reglas de negocio del original. Se prepara el contenido para publicación y se actualizan la documentación y los metadatos de autoría.

## Verificado en el código

- PHP con namespaces, autoload PSR-4, controladores y repositorios.
- MySQL mediante PDO, consultas preparadas y siete tablas relacionadas.
- Registro de contraseñas mediante `password_hash`, sin devolver el hash en las consultas de usuario.
- Interfaz HTML/CSS/JavaScript nativo, sin Vue.
- Esquema SQL sin instrucciones INSERT con registros de usuarios.
- Archivo `.env` presente en el ZIP original y excluido de esta copia.
- Dependencias `vendor/` presentes en el ZIP original y excluidas de la publicación.
- Creador y desarrollador único: Juan Isais Garcia.

## Pendientes para una demo pública

1. `public/assets/app.js` fija la API a `http://localhost:8080/api`. Hay que hacerla configurable o relativa al origen de la demo.
2. Hay formularios que aceptan IDs, pero no un flujo de inicio de sesión ni comprobaciones de autorización. El registro usa un hash de contraseña, lo que no autentica las demás operaciones.
3. Los resultados se insertan con `innerHTML` y `limpiarTexto` no escapa contenido. Entradas guardadas en la base podrían interpretarse como HTML. Se debe corregir antes de una demo abierta.
4. La creación de solicitudes comprueba que usuarios y libro existan, pero no comprueba que el receptor sea el propietario del libro.
5. El envío de mensajes comprueba existencia de usuarios y solicitud, pero no que sean sus participantes.
6. Las calificaciones comprueban IDs y puntuación, pero no participación ni estado del intercambio. No deben describirse como limitadas a intercambios completados.
7. Aceptar una solicitud genera historial exitoso, pero no marca el libro como no disponible ni completa el intercambio. La baja del libro es una operación separada.
8. Algunas operaciones escriben varios registros sin una transacción de base de datos. Conviene definir el comportamiento esperado ante un fallo parcial y solicitudes simultáneas.
9. Algunos controladores devuelven detalles de excepciones. Antes de una publicación abierta se deben revisar los errores expuestos.
10. La prueba de estado existente omite MySQL en CLI. No constituye una prueba del flujo completo.

Estos puntos se documentan para priorizar ajustes posteriores; no se cambian las reglas académicas del proyecto durante esta preparación.

## Qué se puede subir

Código, esquema SQL, pruebas, Composer y su lock, configuración de ejemplo y documentación. No se incluye una exportación de la base con registros reales ni credenciales locales.

## Validación pendiente de base de datos

El ZIP no contiene el contenido de tu base MySQL local. No se han importado migraciones ni realizado escrituras en tus bases existentes. Para probar los flujos se utilizará una base aislada con datos ficticios.

## Comprobaciones ejecutadas

- Sintaxis correcta en los 32 archivos PHP del proyecto.
- Sintaxis correcta en `public/assets/app.js`.
- `composer validate --no-check-publish --no-interaction`: válido.
- Prueba original de PHPUnit ejecutada en una copia temporal: 1 prueba, 5 aserciones, sin fallos.
- La preparación inicial conservó los 39 archivos de aplicación y esquema. Posteriormente se añadió el metadato de autor a la interfaz, sin modificar su funcionamiento.
- No se incluye `.env`, `vendor/`, caché de PHPUnit ni configuración del editor en el paquete público.
- Repositorio preparado para publicación en GitHub; no se ha desplegado una demo de esta aplicación.
