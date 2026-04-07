-- =====================================================
-- FUNDACITE CARABOBO - Sistema de Gestión de Actividades
-- Base de Datos MySQL
-- =====================================================

CREATE DATABASE IF NOT EXISTS fundacite_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE fundacite_db;

-- Tabla de roles
CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    descripcion TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO roles (nombre, descripcion) VALUES
('admin', 'Administrador del sistema con control total'),
('operativo', 'Usuario que puede crear y editar actividades propias'),
('visualizacion', 'Usuario de solo lectura');

-- Tabla de usuarios
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) DEFAULT NULL,
    rol_id INT NOT NULL DEFAULT 3,
    primer_login TINYINT(1) DEFAULT 1,
    activo TINYINT(1) DEFAULT 1,
    foto VARCHAR(255) DEFAULT NULL,
    created_by INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (rol_id) REFERENCES roles(id),
    FOREIGN KEY (created_by) REFERENCES usuarios(id) ON DELETE SET NULL
);

-- Admin por defecto (primer login sin contraseña)
INSERT INTO usuarios (nombre, apellido, email, password, rol_id, primer_login) VALUES
('Administrador', 'Sistema', 'admin@fundacite.gob.ve', NULL, 1, 1);

-- Tabla de actividades
CREATE TABLE actividades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(200) NOT NULL,
    descripcion TEXT,
    requisitos TEXT,
    fecha_inicio DATE NOT NULL,
    fecha_limite DATETIME NOT NULL,
    estado ENUM('pendiente','realizada','no_realizada','por_aprobar') DEFAULT 'pendiente',
    aprobada TINYINT(1) DEFAULT 0,
    creado_por INT NOT NULL,
    aprobado_por INT DEFAULT NULL,
    fecha_aprobacion TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (creado_por) REFERENCES usuarios(id),
    FOREIGN KEY (aprobado_por) REFERENCES usuarios(id) ON DELETE SET NULL
);

-- Tabla de notificaciones
CREATE TABLE notificaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    titulo VARCHAR(200) NOT NULL,
    mensaje TEXT NOT NULL,
    tipo ENUM('info','warning','danger','success') DEFAULT 'info',
    leida TINYINT(1) DEFAULT 0,
    url VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- Tabla de configuraciones del sistema
CREATE TABLE configuraciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    clave VARCHAR(100) NOT NULL UNIQUE,
    valor TEXT,
    descripcion VARCHAR(255),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO configuraciones (clave, valor, descripcion) VALUES
('logo', '', 'Logo institucional'),
('fondo_login', '', 'Imagen de fondo del login'),
('nombre_sistema', 'FUNDACITE Carabobo', 'Nombre del sistema'),
('color_primario', '#1a3a5c', 'Color primario del sistema'),
('smtp_host', 'smtp.gmail.com', 'Servidor SMTP'),
('smtp_port', '587', 'Puerto SMTP'),
('smtp_user', '', 'Usuario SMTP'),
('smtp_pass', '', 'Contraseña SMTP'),
('smtp_from', 'noreply@fundacite.gob.ve', 'Email remitente');

-- Índices para rendimiento
CREATE INDEX idx_actividades_estado ON actividades(estado);
CREATE INDEX idx_actividades_fecha ON actividades(fecha_limite);
CREATE INDEX idx_notificaciones_usuario ON notificaciones(usuario_id, leida);
