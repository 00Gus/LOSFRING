-- Script de datos de prueba para MiniSmart
-- Ejecutar después de crear las tablas principales
-- Usar solo para desarrollo/pruebas

USE minismart_db;

-- Insertar categorías de ejemplo
INSERT INTO categorias (nombre) VALUES
('Bebidas'),
('Lacteos'),
('Abarrotes'),
('Limpieza'),
('Dulces')
ON DUPLICATE KEY UPDATE nombre = nombre;

-- Insertar proveedores de ejemplo
INSERT INTO proveedores (nombre, telefono, email, direccion) VALUES
('Distribuidora ABC', '555-1234', 'abc@example.com', 'Av. Principal 123'),
('Suministros XYZ', '555-5678', 'xyz@example.com', 'Calle Secundaria 456'),
('Mayoreo Express', '555-9012', 'express@example.com', 'Boulevard Norte 789')
ON DUPLICATE KEY UPDATE nombre = nombre;

-- Insertar productos de ejemplo
INSERT INTO productos (codigo_barras, nombre, id_categoria, precio_compra, precio_venta, stock_actual, stock_minimo, unidad) VALUES
('7501234567890', 'Coca Cola 600ml', 1, 10.00, 15.00, 50, 10, 'pz'),
('7501234567891', 'Pepsi 600ml', 1, 9.50, 14.50, 30, 10, 'pz'),
('7501234567892', 'Leche Entera 1L', 2, 18.00, 22.00, 25, 5, 'pz'),
('7501234567893', 'Leche Deslactosada 1L', 2, 19.00, 23.00, 15, 5, 'pz'),
('7501234567894', 'Arroz 1kg', 3, 25.00, 30.00, 40, 10, 'pz'),
('7501234567895', 'Frijol 1kg', 3, 35.00, 42.00, 20, 5, 'pz'),
('7501234567896', 'Detergente 1kg', 4, 45.00, 55.00, 15, 5, 'pz'),
('7501234567897', 'Jabón de barra', 4, 8.00, 12.00, 60, 15, 'pz'),
('7501234567898', 'Chocolate 50g', 5, 12.00, 18.00, 35, 10, 'pz'),
('7501234567899', 'Galletas 200g', 5, 15.00, 22.00, 28, 8, 'pz')
ON DUPLICATE KEY UPDATE nombre = nombre;

-- Nota: Para probar compras y ventas, usa la interfaz web del sistema
-- Los datos aquí son solo productos base para empezar a trabajar

