# Demo pública de BookSwap

Creador y desarrollador único: Juan Isais Garcia.

Esta rama `codex/public-demo` conserva PHP, PDO y MySQL. La versión académica y sus capturas permanecen en `main`.

## Arquitectura

- Render Free: contenedor PHP 8.3 con Apache, interfaz y API en el mismo dominio.
- Aiven Free: servidor MySQL real, separado del servidor web.
- PDO se conecta con TLS y verificación del certificado CA de MySQL.
- GitHub contiene código y configuración de ejemplo, nunca contraseñas ni `.env`.

Referencias: https://render.com/docs/docker y https://aiven.io/docs/products/mysql/concepts/mysql-free-tier.

## Preparación de la base

1. Entrar en https://console.aiven.io/ y crear un servicio MySQL en el plan Free. Seleccionar explícitamente Free, no una prueba de un plan de pago.
2. Crear una base vacía y dedicada llamada `bookswap_demo`.
3. Obtener host, puerto, usuario, contraseña y certificado CA desde el panel del servicio.
4. Guardar la contraseña exclusivamente como variable secreta en Render. El certificado CA se agrega como Secret File con nombre `mysql-ca.pem`.

Al iniciarse, la demo importa las siete tablas y agrega dos lectores y dos libros ficticios únicamente cuando no existen usuarios. No borra ni restablece registros. La inicialización se rechaza si el nombre de la base no comienza con `bookswap_demo` o si DEMO_MODE no está activado.

## Configuración de Render

Crear un Web Service desde `juanelooisga/bookswap`, rama `codex/public-demo`, con runtime Docker y plan Free. El directorio raíz queda vacío y el Dockerfile está en la raíz. El archivo `render.yaml` contiene los mismos valores para un Blueprint.

| Variable | Valor |
| --- | --- |
| APP_ENV | production |
| APP_DEBUG | false |
| DEMO_MODE | true |
| DEMO_INITIALIZE | true |
| DB_HOST | Host proporcionado por Aiven |
| DB_PORT | Puerto proporcionado por Aiven |
| DB_NAME | bookswap_demo |
| DB_USER | Usuario de MySQL |
| DB_PASS | Contraseña de MySQL, secreta |
| DB_SSL | true |
| DB_SSL_CA | /etc/secrets/mysql-ca.pem |

Health check: `/api/health`. El sitio se abre desde `/` o `/frontend.html`. La API utiliza el mismo origen y deja de depender del puerto 8080 del equipo del visitante.

## Alcance y comportamiento de la demo

Es una demostración compartida con datos ficticios y formularios que representan a los usuarios mediante IDs. No ofrece inicio de sesión ni identifica al visitante: validar que los IDs pertenecen a una solicitud no equivale a autenticar quién hace la petición. No introducir datos personales ni reutilizar contraseñas. No se presenta como un servicio de producción.

La rama incorpora escape del texto mostrado, validación del propietario del libro, participantes de mensajes y calificaciones, estado de solicitud para calificar y transacciones para aceptar o rechazar junto con sus registros asociados. Los errores internos se ocultan con APP_DEBUG=false.

La aceptación conserva el comportamiento académico: registra la aceptación y su historial, pero la baja del libro continúa siendo una acción separada. No representa la confirmación de una entrega física.

La base es externa: reiniciar Render no elimina los registros MySQL. Aiven administra el almacenamiento y tiene las limitaciones de su plan gratuito. Usar una base dedicada exclusivamente a esta demo.

## Verificación

Construir con `docker build -t bookswap-demo:local .`. El contenedor escucha en PORT (10000 por defecto). Para pruebas locales con una base desechable puede configurarse APP_ENV=local y DB_SSL=false; producción exige TLS.

La inicialización debe probarse dos veces: la segunda no debe duplicar los datos de ejemplo. También se comprueban registro de usuarios y libros, solicitudes, mensajes, aceptación/rechazo, historial, calificaciones y baja lógica, junto con casos inválidos.

## Resultado de las pruebas del 28 de octubre de 2026

- Imagen PHP/Apache construida correctamente desde el Dockerfile.
- Ejecución contra MySQL Community 8.4 en un contenedor aislado, independiente de XAMPP.
- Flujo de usuarios, libros, solicitudes, mensajes, aceptación/rechazo, historial, calificaciones y baja lógica: correcto.
- Rechazo del receptor incorrecto, mensajes/calificaciones de terceros y calificaciones de solicitudes pendientes o rechazadas: correcto.
- Dos aceptaciones simultáneas: una respuesta correcta y una HTTP 409; solo un registro de historial.
- Petición demasiado grande: HTTP 413.
- Dos pruebas de JavaScript: API del mismo origen y escape de contenido HTML.
- Prueba PHP original: 1 prueba, 5 aserciones, sin fallos.
- Inicialización repetida: sin duplicar usuarios ni libros.
- Configuración production sin TLS: rechazada.
- Imagen sin `.env`, `.git` ni PHPUnit: comprobado.

Pendiente: conectar y verificar la base remota con su certificado CA y comprobar el enlace público de Render. No se afirma que esa conexión externa ya esté validada.

Para repetir pruebas de interfaz: `node --test tests/frontend.test.cjs`.
Para el flujo HTTP: definir `BOOKSWAP_TEST_BASE_URL` con la URL de una instancia local desechable y ejecutar `python tests/demo_http_smoke.py`. La prueba rechaza hosts externos y agrega registros ficticios; no utilizar una instancia con datos importantes.
