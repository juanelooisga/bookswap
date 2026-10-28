# Pruebas locales con MySQL

Fecha: 28 de octubre de 2026.
Proyecto creado y desarrollado por Juan Isais Garcia.

## Entorno

PHP 8.0.30, servidor local en `http://localhost:8080` y MySQL/MariaDB de XAMPP. Se creó una base nueva con prefijo `bookswap_portfolio_test_20261028_` y se importó el esquema de siete tablas. La configuración local se mantiene en `.env`, excluido de Git. Todos los registros de estas pruebas son ficticios.

## Resultados mediante HTTP

- Conexión real a la base desde `/api/health`: correcta.
- Registro y consulta de dos perfiles: correcto; las respuestas no incluyen el hash de contraseña.
- Correo duplicado: rechazado con HTTP 409.
- Alta y búsqueda de libros: correctas.
- Creación de solicitud y notificación al receptor: correctas.
- Envío y consulta de mensajes: correctos.
- Aceptación, historial y notificación al solicitante: correctos.
- Segundo intento de aceptar la misma solicitud: rechazado con HTTP 409.
- Alta y consulta de calificaciones: correctas.
- Puntuación fuera del rango 1-5: rechazada con HTTP 422.
- Baja lógica del libro y exclusión de búsquedas: correctas.
- Rechazo de otra solicitud pendiente: correcto.
- Entrega de `frontend.html`: correcta.

Las pruebas verifican estos casos concretos con MySQL real. No sustituyen la revisión de permisos, contenido HTML y reglas de participantes documentada en [REVISION.md](REVISION.md), ni representan una validación de una demo desplegada en Internet.
