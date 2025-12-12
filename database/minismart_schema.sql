-- Script de creación de base de datos para MiniSmart
-- Ejecutar en MySQL/MariaDB (por ejemplo, desde phpMyAdmin o la consola).
-- En Docker, la BD ya está creada por MYSQL_DATABASE, así que solo creamos las tablas.

-- CREATE DATABASE IF NOT EXISTS minismart_db
--   CHARACTER SET utf8mb4
--   COLLATE utf8mb4_unicode_ci;

-- USE minismart_db;

-- Tabla de usuarios del sistema
CREATE TABLE IF NOT EXISTS usuarios (
  id_usuario INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  usuario VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  rol ENUM('ADMIN','CAJERO') NOT NULL DEFAULT 'CAJERO',
  creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Tabla de categorías de productos
CREATE TABLE IF NOT EXISTS categorias (
  id_categoria INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL
) ENGINE=InnoDB;

-- Tabla de productos
CREATE TABLE IF NOT EXISTS productos (
  id_producto INT AUTO_INCREMENT PRIMARY KEY,
  codigo_barras VARCHAR(50) DEFAULT NULL,
  nombre VARCHAR(150) NOT NULL,
  id_categoria INT DEFAULT NULL,
  precio_compra DECIMAL(10,2) NOT NULL DEFAULT 0,
  precio_venta DECIMAL(10,2) NOT NULL DEFAULT 0,
  stock_actual INT NOT NULL DEFAULT 0,
  stock_minimo INT NOT NULL DEFAULT 0,
  unidad VARCHAR(20) NOT NULL DEFAULT 'pz',
  fecha_caducidad DATE DEFAULT NULL,
  CONSTRAINT fk_productos_categoria
    FOREIGN KEY (id_categoria) REFERENCES categorias(id_categoria)
    ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB;

-- Tabla de proveedores
CREATE TABLE IF NOT EXISTS proveedores (
  id_proveedor INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(150) NOT NULL,
  telefono VARCHAR(30) DEFAULT NULL,
  email VARCHAR(100) DEFAULT NULL,
  direccion VARCHAR(255) DEFAULT NULL
) ENGINE=InnoDB;

-- Tabla de compras (encabezado)
CREATE TABLE IF NOT EXISTS compras (
  id_compra INT AUTO_INCREMENT PRIMARY KEY,
  fecha DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  id_proveedor INT NOT NULL,
  id_usuario INT NOT NULL,
  total DECIMAL(10,2) NOT NULL DEFAULT 0,
  CONSTRAINT fk_compras_proveedor
    FOREIGN KEY (id_proveedor) REFERENCES proveedores(id_proveedor)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_compras_usuario
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

-- Tabla detalle de compras
CREATE TABLE IF NOT EXISTS detalle_compra (
  id_detalle_compra INT AUTO_INCREMENT PRIMARY KEY,
  id_compra INT NOT NULL,
  id_producto INT NOT NULL,
  cantidad INT NOT NULL,
  precio_compra DECIMAL(10,2) NOT NULL,
  CONSTRAINT fk_detalle_compra_compra
    FOREIGN KEY (id_compra) REFERENCES compras(id_compra)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fk_detalle_compra_producto
    FOREIGN KEY (id_producto) REFERENCES productos(id_producto)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

-- Tabla de ventas (encabezado)
CREATE TABLE IF NOT EXISTS ventas (
  id_venta INT AUTO_INCREMENT PRIMARY KEY,
  fecha DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  id_usuario INT NOT NULL,
  total DECIMAL(10,2) NOT NULL DEFAULT 0,
  forma_pago VARCHAR(30) NOT NULL DEFAULT 'EFECTIVO',
  CONSTRAINT fk_ventas_usuario
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

-- Tabla detalle de ventas
CREATE TABLE IF NOT EXISTS detalle_venta (
  id_detalle_venta INT AUTO_INCREMENT PRIMARY KEY,
  id_venta INT NOT NULL,
  id_producto INT NOT NULL,
  cantidad INT NOT NULL,
  precio_venta DECIMAL(10,2) NOT NULL,
  CONSTRAINT fk_detalle_venta_venta
    FOREIGN KEY (id_venta) REFERENCES ventas(id_venta)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fk_detalle_venta_producto
    FOREIGN KEY (id_producto) REFERENCES productos(id_producto)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

-- Índices útiles para reportes y búsqueda
CREATE INDEX idx_productos_nombre ON productos(nombre);
CREATE INDEX idx_productos_codigo_barras ON productos(codigo_barras);
CREATE INDEX idx_ventas_fecha ON ventas(fecha);
CREATE INDEX idx_compras_fecha ON compras(fecha);

-- Usuario administrador inicial (cambia la contraseña después de entrar)
-- La contraseña en texto plano es: admin123
INSERT INTO usuarios (nombre, usuario, password, rol)
VALUES (
  'Administrador',
  'admin',
  '$2y$10$dx1jHoH/7yB3lL7y5v0W2u6CbtK7tjfAfl6yXxv2MhtVi/F2OA8jS',
  'ADMIN'
)
ON DUPLICATE KEY UPDATE usuario = usuario;



