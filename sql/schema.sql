-- Crear la base de datos
CREATE DATABASE IF NOT EXISTS login_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- Seleccionar la base de datos
USE login_app;
-- Crear tabla de usuarios
CREATE TABLE IF NOT EXISTS usuarios (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nombre VARCHAR(60) NOT NULL,
  apellido VARCHAR(60) NOT NULL,
  correo VARCHAR(120) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_usuarios_correo (correo)
) ENGINE = InnoDB DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- Crear usuario exclusivo para la aplicación
CREATE USER IF NOT EXISTS 'login_app_user' @'localhost' IDENTIFIED BY 'Cambiar_Esta_Clave_123!';
-- Permisos limitados para la aplicación
GRANT SELECT,
  INSERT ON login_app.usuarios TO 'login_app_user' @'localhost';
-- Actualizar privilegios
FLUSH PRIVILEGES;