-- SIGERUTT - Esquema de base de datos (MySQL)
-- Nota: la gestión de usuarios (login/roles) ya NO vive aquí, se maneja
-- desde la API (Flask) de registro de usuarios. Esta base solo contiene
-- los catálogos operativos: rutas, paradas, vehículos, operadores y
-- asignaciones.

CREATE DATABASE IF NOT EXISTS sistema_rutas CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sistema_rutas;

CREATE TABLE IF NOT EXISTS rutas (
    id_ruta INT AUTO_INCREMENT PRIMARY KEY,
    nombre_ruta VARCHAR(150) NOT NULL,
    lat_origen DECIMAL(10,7) NOT NULL,
    lon_origen DECIMAL(10,7) NOT NULL,
    lat_destino DECIMAL(10,7) NOT NULL,
    lon_destino DECIMAL(10,7) NOT NULL,
    distancia_total DECIMAL(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS paradas (
    id_parada INT AUTO_INCREMENT PRIMARY KEY,
    id_ruta INT NOT NULL,
    orden TINYINT NOT NULL,
    latitud DECIMAL(10,7) NOT NULL,
    longitud DECIMAL(10,7) NOT NULL,
    CONSTRAINT fk_paradas_ruta FOREIGN KEY (id_ruta) REFERENCES rutas(id_ruta) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS operadores (
    id_operador INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    licencia VARCHAR(50) NOT NULL UNIQUE,
    telefono VARCHAR(20) NOT NULL,
    disponibilidad ENUM('disponible', 'no disponible') NOT NULL DEFAULT 'disponible'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS vehiculos (
    id_vehiculo INT AUTO_INCREMENT PRIMARY KEY,
    placa VARCHAR(9) NOT NULL UNIQUE,
    tipo VARCHAR(100) NOT NULL,
    capacidad VARCHAR(50) NOT NULL,
    estado ENUM('activo', 'inactivo') NOT NULL DEFAULT 'activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS asignaciones (
    id_asignacion INT AUTO_INCREMENT PRIMARY KEY,
    id_ruta INT NOT NULL,
    id_vehiculo INT NOT NULL,
    id_operador INT NOT NULL,
    fecha_asignacion DATE NOT NULL,
    CONSTRAINT fk_asignaciones_ruta FOREIGN KEY (id_ruta) REFERENCES rutas(id_ruta) ON DELETE CASCADE,
    CONSTRAINT fk_asignaciones_vehiculo FOREIGN KEY (id_vehiculo) REFERENCES vehiculos(id_vehiculo) ON DELETE CASCADE,
    CONSTRAINT fk_asignaciones_operador FOREIGN KEY (id_operador) REFERENCES operadores(id_operador) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
