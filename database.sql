CREATE DATABASE IF NOT EXISTS shieldlog
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE shieldlog;

CREATE TABLE IF NOT EXISTS usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  rol ENUM('usuario', 'empresa') NOT NULL DEFAULT 'usuario',
  fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS analisis_url (
  id INT AUTO_INCREMENT PRIMARY KEY,
  url TEXT NOT NULL,
  puntuacion INT NOT NULL,
  resultado ENUM('Seguro', 'Sospechoso', 'Peligroso') NOT NULL,
  detalles JSON NULL,
  fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS analisis_logs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT NOT NULL,
  archivo VARCHAR(255) NOT NULL,
  lineas_totales INT NOT NULL,
  lineas_sospechosas INT NOT NULL,
  nivel_riesgo ENUM('bajo', 'medio', 'alto') NOT NULL,
  amenazas JSON NULL,
  ejemplos JSON NULL,
  fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_analisis_logs_usuario
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
    ON DELETE CASCADE
);

INSERT INTO usuarios (email, password, rol)
VALUES
  ('empresa@demo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi', 'empresa'),
  ('usuario@demo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi', 'usuario');

-- Password de los usuarios demo: password
