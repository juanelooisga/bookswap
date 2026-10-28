-- Migración principal de tablas para BookSwap
-- Ejecutar después de crear la base de datos `bookswap` y hacer:
--   USE bookswap;

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- Tabla de usuarios
CREATE TABLE IF NOT EXISTS usuarios (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    correo VARCHAR(150) NOT NULL UNIQUE,
    telefono VARCHAR(30) NULL,
    ubicacion VARCHAR(150) NULL,
    preferencias_literarias TEXT NULL,
    password VARCHAR(255) NOT NULL,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de libros
CREATE TABLE IF NOT EXISTS libros (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id BIGINT UNSIGNED NOT NULL,
    titulo VARCHAR(200) NOT NULL,
    autor VARCHAR(150) NOT NULL,
    genero VARCHAR(100) NULL,
    descripcion_estado TEXT NULL,
    ubicacion VARCHAR(150) NULL,
    disponible TINYINT(1) NOT NULL DEFAULT 1,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_libros_usuario
        FOREIGN KEY (usuario_id)
        REFERENCES usuarios(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de solicitudes de intercambio
CREATE TABLE IF NOT EXISTS solicitudes_intercambio (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_solicitante_id BIGINT UNSIGNED NOT NULL,
    usuario_receptor_id BIGINT UNSIGNED NOT NULL,
    libro_solicitado_id BIGINT UNSIGNED NOT NULL,
    estado ENUM('pendiente', 'aceptada', 'rechazada', 'cancelada', 'completada') NOT NULL DEFAULT 'pendiente',
    mensaje TEXT NULL,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_solicitudes_usuario_solicitante
        FOREIGN KEY (usuario_solicitante_id)
        REFERENCES usuarios(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT fk_solicitudes_usuario_receptor
        FOREIGN KEY (usuario_receptor_id)
        REFERENCES usuarios(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT fk_solicitudes_libro
        FOREIGN KEY (libro_solicitado_id)
        REFERENCES libros(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de mensajes entre usuarios en el contexto de una solicitud
CREATE TABLE IF NOT EXISTS mensajes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    solicitud_id BIGINT UNSIGNED NOT NULL,
    usuario_emisor_id BIGINT UNSIGNED NOT NULL,
    usuario_receptor_id BIGINT UNSIGNED NOT NULL,
    mensaje TEXT NOT NULL,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_mensajes_solicitud
        FOREIGN KEY (solicitud_id)
        REFERENCES solicitudes_intercambio(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT fk_mensajes_usuario_emisor
        FOREIGN KEY (usuario_emisor_id)
        REFERENCES usuarios(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT fk_mensajes_usuario_receptor
        FOREIGN KEY (usuario_receptor_id)
        REFERENCES usuarios(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de calificaciones entre usuarios
CREATE TABLE IF NOT EXISTS calificaciones (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_calificado_id BIGINT UNSIGNED NOT NULL,
    usuario_calificador_id BIGINT UNSIGNED NOT NULL,
    solicitud_id BIGINT UNSIGNED NOT NULL,
    puntuacion TINYINT UNSIGNED NOT NULL,
    comentario TEXT NULL,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT chk_calificaciones_puntuacion
        CHECK (puntuacion BETWEEN 1 AND 5),
    CONSTRAINT fk_calificaciones_usuario_calificado
        FOREIGN KEY (usuario_calificado_id)
        REFERENCES usuarios(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT fk_calificaciones_usuario_calificador
        FOREIGN KEY (usuario_calificador_id)
        REFERENCES usuarios(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT fk_calificaciones_solicitud
        FOREIGN KEY (solicitud_id)
        REFERENCES solicitudes_intercambio(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de notificaciones
CREATE TABLE IF NOT EXISTS notificaciones (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id BIGINT UNSIGNED NOT NULL,
    tipo VARCHAR(50) NOT NULL,
    mensaje TEXT NOT NULL,
    leida TINYINT(1) NOT NULL DEFAULT 0,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_notificaciones_usuario
        FOREIGN KEY (usuario_id)
        REFERENCES usuarios(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de historial de intercambios
CREATE TABLE IF NOT EXISTS historial_intercambios (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    solicitud_id BIGINT UNSIGNED NOT NULL,
    usuario_1_id BIGINT UNSIGNED NOT NULL,
    usuario_2_id BIGINT UNSIGNED NOT NULL,
    descripcion TEXT NULL,
    estado_final ENUM('exitoso', 'fallido', 'cancelado') NOT NULL,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_historial_solicitud
        FOREIGN KEY (solicitud_id)
        REFERENCES solicitudes_intercambio(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT fk_historial_usuario1
        FOREIGN KEY (usuario_1_id)
        REFERENCES usuarios(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT fk_historial_usuario2
        FOREIGN KEY (usuario_2_id)
        REFERENCES usuarios(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

