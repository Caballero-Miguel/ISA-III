CREATE DATABASE IF NOT EXISTS isa3_testing
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE isa3_testing;

CREATE TABLE usuarios (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(120) NOT NULL,
    correo VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol ENUM('admin', 'tester') NOT NULL DEFAULT 'tester',
    fecha_creacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

CREATE TABLE casos_prueba (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(10) NOT NULL UNIQUE,
    modulo VARCHAR(150) NOT NULL,
    tecnica ENUM('caja_negra', 'caja_blanca') NOT NULL,
    subtecnica VARCHAR(100) NOT NULL,
    objetivo TEXT NOT NULL,
    precondiciones TEXT NULL,
    datos_entrada TEXT NULL,
    pasos_ejecucion TEXT NOT NULL,
    resultado_esperado TEXT NOT NULL,
    resultado_obtenido TEXT NULL,
    estado ENUM('exito', 'fallo') NOT NULL DEFAULT 'fallo',
    observaciones TEXT NULL,
    evidencia TEXT NULL,
    usuario_id INT UNSIGNED NOT NULL,
    fecha_creacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_casos_estado (estado),
    INDEX idx_casos_usuario (usuario_id),
    CONSTRAINT fk_casos_usuario
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;
