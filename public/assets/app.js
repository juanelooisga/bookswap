const API = "http://localhost:8080/api";

/* =========================
   FUNCIONES GENERALES
========================= */

function setHTML(id, html) {
  document.getElementById(id).innerHTML = html;
}

function limpiarTexto(valor) {
  return valor ?? "Sin dato";
}

function mostrarMensaje(id, mensaje) {
  setHTML(id, `<div class="item-card">${mensaje}</div>`);
}

async function postJson(url, data) {
  const res = await fetch(url, {
    method: "POST",
    headers: {
      "Accept": "application/json",
      "Content-Type": "application/json"
    },
    body: JSON.stringify(data)
  });

  return await res.json();
}

async function postForm(url, data) {
  const body = new URLSearchParams(data);

  const res = await fetch(url, {
    method: "POST",
    headers: {
      "Accept": "application/json",
      "Content-Type": "application/x-www-form-urlencoded"
    },
    body
  });

  return await res.json();
}

/* =========================
   RENDERIZAR RESULTADOS
========================= */

function renderLibros(data) {
  const books = data?.data?.books || [];

  if (books.length === 0) {
    return `<p>No se encontraron libros disponibles.</p>`;
  }

  return books.map(book => `
    <div class="item-card">
      <h3>${limpiarTexto(book.titulo)}</h3>
      <p><strong>Autor:</strong> ${limpiarTexto(book.autor)}</p>
      <p><strong>Género:</strong> ${limpiarTexto(book.genero)}</p>
      <p><strong>Estado:</strong> ${limpiarTexto(book.descripcion_estado)}</p>
      <p><strong>Ubicación:</strong> ${limpiarTexto(book.ubicacion)}</p>
      <p><strong>Disponible:</strong> ${book.disponible == 1 ? "Sí" : "No"}</p>
      <p><strong>ID del libro:</strong> ${book.id}</p>
      <p><strong>ID del dueño:</strong> ${book.usuario_id}</p>
    </div>
  `).join("");
}

function renderSolicitudes(data) {
  const exchanges = data?.data?.exchanges || [];

  if (exchanges.length === 0) {
    return `<p>No hay solicitudes registradas.</p>`;
  }

  return exchanges.map(exchange => `
    <div class="item-card">
      <h3>Solicitud #${exchange.id}</h3>
      <p><strong>Usuario solicitante:</strong> ${exchange.usuario_solicitante_id}</p>
      <p><strong>Usuario receptor:</strong> ${exchange.usuario_receptor_id}</p>
      <p><strong>Libro solicitado:</strong> ${exchange.libro_solicitado_id}</p>
      <p><strong>Estado:</strong> ${limpiarTexto(exchange.estado)}</p>
      <p><strong>Mensaje:</strong> ${limpiarTexto(exchange.mensaje)}</p>
      <p><strong>Fecha:</strong> ${limpiarTexto(exchange.creado_en)}</p>
    </div>
  `).join("");
}

function renderMensajes(data) {
  const messages = data?.data?.messages || [];

  if (messages.length === 0) {
    return `<p>No hay mensajes registrados para esta solicitud.</p>`;
  }

  return messages.map(msg => `
    <div class="item-card">
      <h3>Mensaje #${msg.id}</h3>
      <p><strong>Solicitud:</strong> ${msg.solicitud_id}</p>
      <p><strong>Usuario emisor:</strong> ${msg.usuario_emisor_id}</p>
      <p><strong>Usuario receptor:</strong> ${msg.usuario_receptor_id}</p>
      <p><strong>Mensaje:</strong> ${limpiarTexto(msg.mensaje)}</p>
      <p><strong>Fecha:</strong> ${limpiarTexto(msg.creado_en)}</p>
    </div>
  `).join("");
}

function renderHistorial(data) {
  const history = data?.data?.history || [];

  if (history.length === 0) {
    return `<p>No hay historial registrado.</p>`;
  }

  return history.map(item => `
    <div class="item-card">
      <h3>Historial #${item.id}</h3>
      <p><strong>Solicitud:</strong> ${item.solicitud_id}</p>
      <p><strong>Usuario 1:</strong> ${item.usuario_1_id}</p>
      <p><strong>Usuario 2:</strong> ${item.usuario_2_id}</p>
      <p><strong>Descripción:</strong> ${limpiarTexto(item.descripcion)}</p>
      <p><strong>Estado final:</strong> ${limpiarTexto(item.estado_final)}</p>
      <p><strong>Fecha:</strong> ${limpiarTexto(item.creado_en)}</p>
    </div>
  `).join("");
}

function renderCalificaciones(data) {
  const ratings = data?.data?.ratings || [];

  if (ratings.length === 0) {
    return `<p>No hay calificaciones registradas para este usuario.</p>`;
  }

  return ratings.map(rating => `
    <div class="item-card">
      <h3>Calificación #${rating.id}</h3>
      <p><strong>Usuario calificado:</strong> ${rating.usuario_calificado_id}</p>
      <p><strong>Usuario calificador:</strong> ${rating.usuario_calificador_id}</p>
      <p><strong>Solicitud:</strong> ${rating.solicitud_id}</p>
      <p><strong>Puntuación:</strong> ${rating.puntuacion}/5</p>
      <p><strong>Comentario:</strong> ${limpiarTexto(rating.comentario)}</p>
      <p><strong>Fecha:</strong> ${limpiarTexto(rating.creado_en)}</p>
    </div>
  `).join("");
}

function renderNotificaciones(data) {
  const notifications = data?.data?.notifications || [];

  if (notifications.length === 0) {
    return `<p>No hay notificaciones para este usuario.</p>`;
  }

  return notifications.map(noti => `
    <div class="item-card">
      <h3>Notificación #${noti.id}</h3>
      <p><strong>Usuario:</strong> ${noti.usuario_id}</p>
      <p><strong>Tipo:</strong> ${limpiarTexto(noti.tipo)}</p>
      <p><strong>Mensaje:</strong> ${limpiarTexto(noti.mensaje)}</p>
      <p><strong>Leída:</strong> ${noti.leida == 1 ? "Sí" : "No"}</p>
      <p><strong>Fecha:</strong> ${limpiarTexto(noti.creado_en)}</p>
    </div>
  `).join("");
}

/* =========================
   1. CREAR PERFIL
========================= */

async function crearUsuario() {
  const data = await postJson(`${API}/users`, {
    nombre: document.getElementById("user_nombre").value,
    correo: document.getElementById("user_correo").value,
    telefono: document.getElementById("user_telefono").value,
    ubicacion: document.getElementById("user_ubicacion").value,
    preferencias_literarias: document.getElementById("user_preferencias").value,
    password: document.getElementById("user_password").value
  });

  if (data.success) {
    mostrarMensaje("resultado_usuarios", "Usuario registrado correctamente.");
  } else {
    mostrarMensaje("resultado_usuarios", data.message || "No se pudo registrar el usuario.");
    console.log(data);
  }
}

/* =========================
   2. AÑADIR LIBROS
========================= */

async function crearLibro() {
  const data = await postForm(`${API}/books`, {
    usuario_id: document.getElementById("book_usuario_id").value,
    titulo: document.getElementById("book_titulo").value,
    autor: document.getElementById("book_autor").value,
    genero: document.getElementById("book_genero").value,
    descripcion_estado: document.getElementById("book_estado").value,
    ubicacion: document.getElementById("book_ubicacion").value
  });

  if (data.success) {
    mostrarMensaje("resultado_registro_libro", "Libro registrado correctamente.");
  } else {
    mostrarMensaje("resultado_registro_libro", data.message || "No se pudo registrar el libro.");
    console.log(data);
  }
}

/* =========================
   3. BUSCAR LIBROS
========================= */

async function buscarLibros() {
  const q = document.getElementById("buscar_texto").value;

  const res = await fetch(`${API}/books/search?q=${encodeURIComponent(q)}`, {
    headers: {
      "Accept": "application/json"
    }
  });

  const data = await res.json();
  setHTML("resultado_libros", renderLibros(data));
}

/* =========================
   4. CREAR SOLICITUD
========================= */

async function crearSolicitud() {
  const data = await postForm(`${API}/exchanges`, {
    usuario_solicitante_id: document.getElementById("exchange_solicitante").value,
    usuario_receptor_id: document.getElementById("exchange_receptor").value,
    libro_solicitado_id: document.getElementById("exchange_libro").value,
    mensaje: document.getElementById("exchange_mensaje").value
  });

  if (data.success) {
    mostrarMensaje("resultado_crear_solicitud", "Solicitud creada correctamente.");
  } else {
    mostrarMensaje("resultado_crear_solicitud", data.message || "No se pudo crear la solicitud.");
    console.log(data);
  }
}

/* =========================
   5. CALIFICAR Y COMENTAR
========================= */

async function crearCalificacion() {
  const data = await postForm(`${API}/ratings`, {
    usuario_calificado_id: document.getElementById("rating_calificado").value,
    usuario_calificador_id: document.getElementById("rating_calificador").value,
    solicitud_id: document.getElementById("rating_solicitud").value,
    puntuacion: document.getElementById("rating_puntuacion").value,
    comentario: document.getElementById("rating_comentario").value
  });

  if (data.success) {
    mostrarMensaje("resultado_calificaciones", "Calificación registrada correctamente.");
  } else {
    mostrarMensaje("resultado_calificaciones", data.message || "No se pudo registrar la calificación.");
    console.log(data);
  }
}

async function consultarCalificaciones() {
  const usuarioId = document.getElementById("rating_usuario_consulta").value;

  if (!usuarioId) {
    mostrarMensaje("resultado_calificaciones", "Escribe el ID del usuario para consultar sus calificaciones.");
    return;
  }

  const res = await fetch(`${API}/users/${usuarioId}/ratings`, {
    headers: {
      "Accept": "application/json"
    }
  });

  const data = await res.json();
  setHTML("resultado_calificaciones", renderCalificaciones(data));
}

/* =========================
   6. NOTIFICACIONES
========================= */

async function consultarNotificaciones() {
  const usuarioId = document.getElementById("notificaciones_usuario_id").value;

  if (!usuarioId) {
    mostrarMensaje("resultado_notificaciones", "Escribe el ID del usuario para consultar sus notificaciones.");
    return;
  }

  const res = await fetch(`${API}/users/${usuarioId}/notifications`, {
    headers: {
      "Accept": "application/json"
    }
  });

  const data = await res.json();
  setHTML("resultado_notificaciones", renderNotificaciones(data));
}

/* =========================
   7. ACEPTAR O RECHAZAR
========================= */

async function listarSolicitudes() {
  const res = await fetch(`${API}/exchanges`, {
    headers: {
      "Accept": "application/json"
    }
  });

  const data = await res.json();
  setHTML("resultado_solicitudes", renderSolicitudes(data));
}

async function aceptarSolicitud() {
  const solicitudId = document.getElementById("solicitud_accion_id").value;

  if (!solicitudId) {
    mostrarMensaje("resultado_solicitudes", "Escribe el ID de la solicitud que deseas aceptar.");
    return;
  }

  const res = await fetch(`${API}/exchanges/${solicitudId}/accept`, {
    method: "PUT",
    headers: {
      "Accept": "application/json"
    }
  });

  const data = await res.json();

  if (data.success) {
    mostrarMensaje("resultado_solicitudes", `Solicitud ${solicitudId} aceptada correctamente.`);
  } else {
    mostrarMensaje("resultado_solicitudes", data.message || "No se pudo aceptar la solicitud.");
  }
}

async function rechazarSolicitud() {
  const solicitudId = document.getElementById("solicitud_accion_id").value;

  if (!solicitudId) {
    mostrarMensaje("resultado_solicitudes", "Escribe el ID de la solicitud que deseas rechazar.");
    return;
  }

  const res = await fetch(`${API}/exchanges/${solicitudId}/reject`, {
    method: "PUT",
    headers: {
      "Accept": "application/json"
    }
  });

  const data = await res.json();

  if (data.success) {
    mostrarMensaje("resultado_solicitudes", `Solicitud ${solicitudId} rechazada correctamente.`);
  } else {
    mostrarMensaje("resultado_solicitudes", data.message || "No se pudo rechazar la solicitud.");
  }
}

/* =========================
   8. MENSAJES
========================= */

async function enviarMensaje() {
  const data = await postForm(`${API}/messages`, {
    solicitud_id: document.getElementById("msg_solicitud").value,
    usuario_emisor_id: document.getElementById("msg_emisor").value,
    usuario_receptor_id: document.getElementById("msg_receptor").value,
    mensaje: document.getElementById("msg_texto").value
  });

  if (data.success) {
    mostrarMensaje("resultado_mensajes", "Mensaje enviado correctamente.");
  } else {
    mostrarMensaje("resultado_mensajes", data.message || "No se pudo enviar el mensaje.");
    console.log(data);
  }
}

async function consultarMensajes() {
  const solicitudId = document.getElementById("msg_solicitud").value;

  if (!solicitudId) {
    mostrarMensaje("resultado_mensajes", "Escribe el ID de la solicitud para consultar sus mensajes.");
    return;
  }

  const res = await fetch(`${API}/exchanges/${solicitudId}/messages`, {
    headers: {
      "Accept": "application/json"
    }
  });

  const data = await res.json();
  setHTML("resultado_mensajes", renderMensajes(data));
}

/* =========================
   9. HISTORIAL
========================= */

async function consultarHistorial() {
  const res = await fetch(`${API}/history`, {
    headers: {
      "Accept": "application/json"
    }
  });

  const data = await res.json();
  setHTML("resultado_historial", renderHistorial(data));
}

/* =========================
   10. ELIMINAR LIBRO
========================= */

async function eliminarLibroDisponible() {
  const bookId = document.getElementById("delete_book_id").value;

  if (!bookId) {
    mostrarMensaje("resultado_eliminar_libro", "Escribe el ID del libro que deseas marcar como no disponible.");
    return;
  }

  const res = await fetch(`${API}/books/${bookId}`, {
    method: "DELETE",
    headers: {
      "Accept": "application/json"
    }
  });

  const data = await res.json();

  if (data.success) {
    mostrarMensaje("resultado_eliminar_libro", `Libro ${bookId} marcado como no disponible.`);
  } else {
    mostrarMensaje("resultado_eliminar_libro", data.message || "No se pudo marcar el libro como no disponible.");
  }
}