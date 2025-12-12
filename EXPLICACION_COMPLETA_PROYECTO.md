# 📚 Explicación Completa del Sistema LOS FRING (MiniSmart)

## 🎯 Índice
1. [Arquitectura General del Sistema](#arquitectura-general)
2. [Flujo de Inicio: index.php](#flujo-de-inicio)
3. [Sistema de Autenticación](#sistema-de-autenticación)
4. [Conexión a Base de Datos](#conexión-a-base-de-datos)
5. [Estructura de la Base de Datos](#estructura-de-la-base-de-datos)
6. [Módulos Principales](#módulos-principales)
7. [Flujo de Datos: Compras y Ventas](#flujo-de-datos)
8. [Sistema de Inventario](#sistema-de-inventario)
9. [Reportes y Predicción](#reportes-y-predicción)
10. [Seguridad y Validaciones](#seguridad-y-validaciones)

---

## 🏗️ Arquitectura General del Sistema

### Tecnologías Utilizadas
- **Backend:** PHP 8.2 (sin frameworks, estilo estructurado)
- **Base de Datos:** MySQL 8.0
- **Frontend:** HTML5, CSS3, Bootstrap 5.3 (CDN)
- **Gráficas:** Chart.js 4.4 (CDN)
- **Contenedores:** Docker (PHP+Apache y MySQL)

### Estructura de Carpetas
```
Proyecto de bases/
├── index.php              # Punto de entrada principal
├── config/
│   └── db.php            # Conexión PDO a MySQL
├── helpers/
│   └── auth.php          # Funciones de autenticación
├── includes/
│   ├── header.php        # Header HTML común
│   ├── navbar.php        # Barra de navegación
│   └── footer.php        # Footer con scripts
├── auth/
│   ├── login.php         # Formulario de login
│   └── logout.php        # Cerrar sesión
├── productos/            # CRUD de productos
├── categorias/           # CRUD de categorías
├── proveedores/          # CRUD de proveedores
├── compras/              # Módulo de compras
├── ventas/               # Módulo de ventas
├── inventario/           # Estado de inventario
├── reportes/             # Reportes de ventas
├── prediccion/           # Predicción de consumo
└── usuarios/             # Administración de usuarios
```

---

## 🚀 Flujo de Inicio: index.php

### ¿Qué pasa cuando accedes a la página?

```php
// 1. Inicia la sesión PHP
session_start();

// 2. Verifica si hay un usuario logueado
if (!isset($_SESSION['id_usuario'])) {
    // Si NO hay sesión → redirige a login
    header('Location: /auth/login.php');
    exit;
}

// 3. Si SÍ hay sesión → conecta a la base de datos
require_once __DIR__ . '/config/db.php';
$pdo = getPDO();

// 4. Consulta datos para el dashboard
$totalProductos = $pdo->query('SELECT COUNT(*) AS c FROM productos')->fetch()['c'] ?? 0;
$totalVentasHoy = $pdo->prepare('SELECT IFNULL(SUM(total),0) AS t FROM ventas WHERE DATE(fecha) = CURDATE()');
$totalVentasHoy->execute();
$ventasHoy = $totalVentasHoy->fetch()['t'] ?? 0;

// 5. Incluye el header y navbar
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';

// 6. Muestra el dashboard con los datos consultados
```

### Flujo Visual:
```
Usuario accede → index.php
    ↓
¿Hay sesión activa?
    ├─ NO → Redirige a /auth/login.php
    └─ SÍ → Consulta BD → Muestra Dashboard
```

---

## 🔐 Sistema de Autenticación

### 1. Login (auth/login.php)

**Proceso paso a paso:**

```php
// 1. Si el usuario ya está logueado, lo redirige al dashboard
if (isset($_SESSION['id_usuario'])) {
    header('Location: /index.php');
    exit;
}

// 2. Si viene un POST (formulario enviado)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // 3. Busca el usuario en la base de datos usando PDO preparado
    $stmt = $pdo->prepare('SELECT * FROM usuarios WHERE usuario = :usuario LIMIT 1');
    $stmt->execute([':usuario' => $usuario]);
    $row = $stmt->fetch();
    
    // 4. Verifica la contraseña usando password_verify()
    if ($row && password_verify($password, $row['password'])) {
        // 5. Si es correcta, guarda datos en la sesión
        $_SESSION['id_usuario'] = $row['id_usuario'];
        $_SESSION['usuario'] = $row['usuario'];
        $_SESSION['rol'] = $row['rol'];
        
        // 6. Redirige al dashboard
        header('Location: /index.php');
        exit;
    } else {
        $error = 'Usuario o contraseña incorrectos.';
    }
}
```

**Seguridad:**
- ✅ Usa **sentencias preparadas** (previene inyección SQL)
- ✅ Contraseñas **hasheadas** con `password_hash()` / `password_verify()`
- ✅ No almacena contraseñas en texto plano

### 2. Protección de Páginas (helpers/auth.php)

```php
// Función para verificar que el usuario esté logueado
function require_login(): void
{
    if (!isset($_SESSION['id_usuario'])) {
        header('Location: /auth/login.php');
        exit;
    }
}

// Función para verificar que sea ADMIN
function require_admin(): void
{
    require_login(); // Primero verifica login
    if (($_SESSION['rol'] ?? '') !== 'ADMIN') {
        http_response_code(403);
        echo 'No tienes permisos para acceder a esta sección.';
        exit;
    }
}
```

**Uso en las páginas:**
```php
require_once __DIR__ . '/../helpers/auth.php';
require_login();        // Cualquier usuario logueado puede entrar
// o
require_admin();        // Solo ADMIN puede entrar
```

---

## 💾 Conexión a Base de Datos

### Archivo: config/db.php

```php
// Configuración de conexión (usa variables de entorno o valores por defecto)
$DB_HOST = getenv('DB_HOST') ?: 'localhost';
$DB_NAME = getenv('DB_NAME') ?: 'minismart_db';
$DB_USER = getenv('DB_USER') ?: 'root';
$DB_PASS = getenv('DB_PASS') ?: '';
$DB_CHARSET = 'utf8mb4';

// Función que retorna una instancia PDO (singleton pattern)
function getPDO()
{
    static $pdo = null; // Variable estática para reutilizar la conexión
    
    if ($pdo === null) {
        // Construye el DSN (Data Source Name)
        $dsn = "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=$DB_CHARSET";
        
        // Opciones de PDO
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,  // Lanza excepciones
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,        // Retorna arrays asociativos
            PDO::ATTR_EMULATE_PREPARES   => false,                   // Usa preparación nativa
        ];
        
        // Crea la conexión
        $pdo = new PDO($dsn, $DB_USER, $DB_PASS, $options);
    }
    
    return $pdo;
}
```

**Características importantes:**
- ✅ **Singleton:** Solo crea una conexión, la reutiliza
- ✅ **Preparación nativa:** Previene inyección SQL
- ✅ **UTF-8:** Soporta caracteres especiales
- ✅ **Manejo de errores:** Lanza excepciones para debugging

---

## 🗄️ Estructura de la Base de Datos

### Tablas Principales

#### 1. **usuarios**
```sql
CREATE TABLE usuarios (
  id_usuario INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  usuario VARCHAR(50) NOT NULL UNIQUE,      -- Nombre de usuario único
  password VARCHAR(255) NOT NULL,          -- Hash de contraseña
  rol ENUM('ADMIN','CAJERO') NOT NULL DEFAULT 'CAJERO',
  creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```
**Propósito:** Almacena usuarios del sistema con sus roles.

#### 2. **categorias**
```sql
CREATE TABLE categorias (
  id_categoria INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL
);
```
**Propósito:** Catálogo de categorías de productos (Bebidas, Lacteos, etc.).

#### 3. **productos**
```sql
CREATE TABLE productos (
  id_producto INT AUTO_INCREMENT PRIMARY KEY,
  codigo_barras VARCHAR(50) DEFAULT NULL,
  nombre VARCHAR(150) NOT NULL,
  id_categoria INT DEFAULT NULL,           -- FK a categorias
  precio_compra DECIMAL(10,2) NOT NULL DEFAULT 0,
  precio_venta DECIMAL(10,2) NOT NULL DEFAULT 0,
  stock_actual INT NOT NULL DEFAULT 0,     -- Stock actual (se actualiza con compras/ventas)
  stock_minimo INT NOT NULL DEFAULT 0,     -- Nivel mínimo de stock
  unidad VARCHAR(20) NOT NULL DEFAULT 'pz',
  fecha_caducidad DATE DEFAULT NULL,
  CONSTRAINT fk_productos_categoria
    FOREIGN KEY (id_categoria) REFERENCES categorias(id_categoria)
    ON UPDATE CASCADE ON DELETE SET NULL
);
```
**Propósito:** Almacena información de productos e inventario.

**Relaciones:**
- `id_categoria` → `categorias.id_categoria` (FK)

#### 4. **proveedores**
```sql
CREATE TABLE proveedores (
  id_proveedor INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(150) NOT NULL,
  telefono VARCHAR(30) DEFAULT NULL,
  email VARCHAR(100) DEFAULT NULL,
  direccion VARCHAR(255) DEFAULT NULL
);
```
**Propósito:** Información de proveedores.

#### 5. **compras** (Encabezado)
```sql
CREATE TABLE compras (
  id_compra INT AUTO_INCREMENT PRIMARY KEY,
  fecha DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  id_proveedor INT NOT NULL,               -- FK a proveedores
  id_usuario INT NOT NULL,                 -- FK a usuarios (quien registró)
  total DECIMAL(10,2) NOT NULL DEFAULT 0,
  CONSTRAINT fk_compras_proveedor
    FOREIGN KEY (id_proveedor) REFERENCES proveedores(id_proveedor),
  CONSTRAINT fk_compras_usuario
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
);
```
**Propósito:** Encabezado de compras (una compra puede tener varios productos).

#### 6. **detalle_compra**
```sql
CREATE TABLE detalle_compra (
  id_detalle_compra INT AUTO_INCREMENT PRIMARY KEY,
  id_compra INT NOT NULL,                  -- FK a compras
  id_producto INT NOT NULL,                -- FK a productos
  cantidad INT NOT NULL,
  precio_compra DECIMAL(10,2) NOT NULL,
  CONSTRAINT fk_detalle_compra_compra
    FOREIGN KEY (id_compra) REFERENCES compras(id_compra)
    ON DELETE CASCADE,                     -- Si se elimina compra, se eliminan detalles
  CONSTRAINT fk_detalle_compra_producto
    FOREIGN KEY (id_producto) REFERENCES productos(id_producto)
);
```
**Propósito:** Detalle de productos en cada compra (relación 1:N).

**Relaciones:**
- `id_compra` → `compras.id_compra` (FK, CASCADE)
- `id_producto` → `productos.id_producto` (FK)

#### 7. **ventas** (Encabezado)
```sql
CREATE TABLE ventas (
  id_venta INT AUTO_INCREMENT PRIMARY KEY,
  fecha DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  id_usuario INT NOT NULL,                 -- FK a usuarios (quien vendió)
  total DECIMAL(10,2) NOT NULL DEFAULT 0,
  forma_pago VARCHAR(30) NOT NULL DEFAULT 'EFECTIVO',
  CONSTRAINT fk_ventas_usuario
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
);
```
**Propósito:** Encabezado de ventas.

#### 8. **detalle_venta**
```sql
CREATE TABLE detalle_venta (
  id_detalle_venta INT AUTO_INCREMENT PRIMARY KEY,
  id_venta INT NOT NULL,                   -- FK a ventas
  id_producto INT NOT NULL,                -- FK a productos
  cantidad INT NOT NULL,
  precio_venta DECIMAL(10,2) NOT NULL,
  CONSTRAINT fk_detalle_venta_venta
    FOREIGN KEY (id_venta) REFERENCES ventas(id_venta)
    ON DELETE CASCADE,
  CONSTRAINT fk_detalle_venta_producto
    FOREIGN KEY (id_producto) REFERENCES productos(id_producto)
);
```
**Propósito:** Detalle de productos en cada venta.

### Diagrama de Relaciones

```
usuarios (1) ──┐
               ├── (N) compras
               └── (N) ventas

proveedores (1) ── (N) compras

categorias (1) ── (N) productos

productos (1) ──┐
                ├── (N) detalle_compra
                └── (N) detalle_venta

compras (1) ── (N) detalle_compra
ventas (1) ── (N) detalle_venta
```

---

## 📦 Módulos Principales

### 1. Módulo de Productos

**Archivos:**
- `productos/listar.php` - Lista todos los productos
- `productos/crear.php` - Formulario para crear producto
- `productos/editar.php` - Formulario para editar producto

**Operaciones CRUD:**

**CREATE (Crear):**
```php
// 1. Recibe datos del formulario POST
$nombre = $_POST['nombre'];
$precio_compra = $_POST['precio_compra'];
$precio_venta = $_POST['precio_venta'];
$stock_actual = $_POST['stock_actual'];
$stock_minimo = $_POST['stock_minimo'];

// 2. Prepara la consulta INSERT usando PDO preparado
$stmt = $pdo->prepare('
    INSERT INTO productos (nombre, precio_compra, precio_venta, stock_actual, stock_minimo, id_categoria)
    VALUES (:nombre, :precio_compra, :precio_venta, :stock_actual, :stock_minimo, :id_categoria)
');

// 3. Ejecuta con los parámetros
$stmt->execute([
    ':nombre' => $nombre,
    ':precio_compra' => $precio_compra,
    // ... más parámetros
]);
```

**READ (Leer/Listar):**
```php
// Consulta todos los productos con JOIN a categorías
$productos = $pdo->query('
    SELECT p.*, c.nombre AS categoria_nombre
    FROM productos p
    LEFT JOIN categorias c ON p.id_categoria = c.id_categoria
    ORDER BY p.nombre
')->fetchAll();

// Muestra en tabla HTML
foreach ($productos as $producto) {
    echo $producto['nombre'];
    echo $producto['stock_actual'];
    // ...
}
```

**UPDATE (Actualizar):**
```php
// Similar a CREATE pero con UPDATE
$stmt = $pdo->prepare('
    UPDATE productos
    SET nombre = :nombre, precio_compra = :precio_compra
    WHERE id_producto = :id
');
$stmt->execute([...]);
```

**DELETE (Eliminar):**
```php
$stmt = $pdo->prepare('DELETE FROM productos WHERE id_producto = :id');
$stmt->execute([':id' => $id_producto]);
```

---

## 🔄 Flujo de Datos: Compras y Ventas

### Proceso de COMPRA (Entrada de Inventario)

**Archivo: `compras/guardar.php`**

**Paso a paso:**

```php
// 1. Verifica autenticación y permisos
require_admin(); // Solo ADMIN puede comprar

// 2. Recibe datos del formulario
$id_proveedor = $_POST['id_proveedor'];
$items = $_POST['items']; // Array de productos: [{id_producto, cantidad, precio_compra}, ...]
$id_usuario = $_SESSION['id_usuario']; // Usuario que registra la compra

// 3. INICIA TRANSACCIÓN (importante para integridad)
$pdo->beginTransaction();

try {
    // 4. Calcula el total de la compra
    $total = 0;
    foreach ($items as $item) {
        $total += $item['cantidad'] * $item['precio_compra'];
    }
    
    // 5. INSERTA el encabezado de compra
    $stmt = $pdo->prepare('
        INSERT INTO compras (fecha, id_proveedor, id_usuario, total)
        VALUES (NOW(), :id_proveedor, :id_usuario, :total)
    ');
    $stmt->execute([...]);
    $id_compra = $pdo->lastInsertId(); // Obtiene el ID generado
    
    // 6. Para cada producto en la compra:
    foreach ($items as $item) {
        // 6a. INSERTA el detalle de compra
        $stmtDet = $pdo->prepare('
            INSERT INTO detalle_compra (id_compra, id_producto, cantidad, precio_compra)
            VALUES (:id_compra, :id_producto, :cantidad, :precio_compra)
        ');
        $stmtDet->execute([...]);
        
        // 6b. ACTUALIZA el stock del producto (AUMENTA)
        $stmtUpdStock = $pdo->prepare('
            UPDATE productos
            SET stock_actual = stock_actual + :cantidad,
                precio_compra = :precio_compra
            WHERE id_producto = :id_producto
        ');
        $stmtUpdStock->execute([
            ':cantidad' => $item['cantidad'],
            ':precio_compra' => $item['precio_compra'],
            ':id_producto' => $item['id_producto']
        ]);
    }
    
    // 7. CONFIRMA la transacción (commit)
    $pdo->commit();
    header('Location: listar.php'); // Redirige a lista de compras
    
} catch (Exception $e) {
    // 8. Si hay error, REVIERTE todo (rollback)
    $pdo->rollBack();
    die('Error: ' . $e->getMessage());
}
```

**Flujo Visual:**
```
Usuario llena formulario de compra
    ↓
compras/guardar.php recibe POST
    ↓
BEGIN TRANSACTION
    ↓
INSERT INTO compras (encabezado)
    ↓
Para cada producto:
    ├─ INSERT INTO detalle_compra
    └─ UPDATE productos SET stock_actual = stock_actual + cantidad
    ↓
COMMIT (confirma todo)
    ↓
Redirige a lista de compras
```

**¿Por qué usar TRANSACCIONES?**
- ✅ Si falla algo, **revierte todo** (rollback)
- ✅ Evita que se guarde compra sin actualizar stock
- ✅ Mantiene **integridad de datos**

### Proceso de VENTA (Salida de Inventario)

**Archivo: `ventas/guardar.php`**

**Similar a compras pero:**
- ✅ Cualquier usuario logueado puede vender (require_login)
- ✅ **DESCUENTA** stock: `stock_actual = stock_actual - cantidad`
- ✅ Actualiza `precio_venta` en productos
- ✅ Guarda `forma_pago` (EFECTIVO, TARJETA, TRANSFERENCIA)

```php
// Diferencia clave en UPDATE de stock:
UPDATE productos
SET stock_actual = stock_actual - :cantidad,  // ← RESTA en lugar de SUMA
    precio_venta = :precio_venta
WHERE id_producto = :id_producto
```

---

## 📊 Sistema de Inventario

### Cálculo de Stock Actual

**El stock se calcula dinámicamente:**

```sql
-- Stock inicial al crear producto
INSERT INTO productos (stock_actual) VALUES (50);

-- Al hacer una COMPRA (aumenta)
UPDATE productos SET stock_actual = stock_actual + 20 WHERE id_producto = 1;
-- Resultado: stock_actual = 70

-- Al hacer una VENTA (disminuye)
UPDATE productos SET stock_actual = stock_actual - 5 WHERE id_producto = 1;
-- Resultado: stock_actual = 65
```

**El stock NO se calcula sumando/restando de las tablas de compras/ventas.**
**Se actualiza directamente en `productos.stock_actual`** para mejor rendimiento.

### Alertas de Stock Bajo

**Archivo: `inventario/estado.php`**

```php
// Consulta productos con su estado de stock
$productos = $pdo->query('
    SELECT p.*, c.nombre AS categoria_nombre
    FROM productos p
    LEFT JOIN categorias c ON p.id_categoria = c.id_categoria
    ORDER BY p.nombre
')->fetchAll();

// En el HTML, compara stock_actual con stock_minimo
foreach ($productos as $p) {
    if ($p['stock_actual'] < $p['stock_minimo']) {
        echo '<span class="badge bg-danger">Stock bajo</span>';
    } else {
        echo '<span class="badge bg-success">OK</span>';
    }
}
```

---

## 📈 Reportes y Predicción

### Reportes de Ventas

**Archivo: `reportes/ventas.php`**

**1. Ventas por día:**
```php
// Consulta ventas agrupadas por día
$ventasPorDia = $pdo->prepare('
    SELECT DATE(fecha) AS dia, SUM(total) AS total_dia
    FROM ventas
    WHERE fecha BETWEEN :desde AND :hasta
    GROUP BY DATE(fecha)
    ORDER BY dia
');
$ventasPorDia->execute([':desde' => $desde, ':hasta' => $hasta]);
$datos = $ventasPorDia->fetchAll();

// Pasa datos a JavaScript para Chart.js
echo '<script>const ventasData = ' . json_encode($datos) . ';</script>';
```

**2. Top productos más vendidos:**
```php
// JOIN entre detalle_venta y productos
$topProductos = $pdo->prepare('
    SELECT p.nombre, SUM(dv.cantidad) AS total_vendido
    FROM detalle_venta dv
    INNER JOIN productos p ON dv.id_producto = p.id_producto
    INNER JOIN ventas v ON dv.id_venta = v.id_venta
    WHERE v.fecha BETWEEN :desde AND :hasta
    GROUP BY p.id_producto, p.nombre
    ORDER BY total_vendido DESC
    LIMIT 10
');
```

### Predicción de Consumo

**Archivo: `prediccion/producto.php`**

**Algoritmo simple:**

```php
// 1. Obtiene historial de ventas del producto por mes
$historial = $pdo->prepare('
    SELECT 
        YEAR(v.fecha) AS anio,
        MONTH(v.fecha) AS mes,
        SUM(dv.cantidad) AS cantidad_vendida
    FROM detalle_venta dv
    INNER JOIN ventas v ON dv.id_venta = v.id_venta
    WHERE dv.id_producto = :id_producto
    GROUP BY YEAR(v.fecha), MONTH(v.fecha)
    ORDER BY anio DESC, mes DESC
    LIMIT 12
');

// 2. Toma los últimos 3 meses
$ultimos3Meses = array_slice($historial, 0, 3);

// 3. Calcula promedio simple
$suma = 0;
foreach ($ultimos3Meses as $mes) {
    $suma += $mes['cantidad_vendida'];
}
$prediccion = $suma / count($ultimos3Meses);

// 4. Calcula sugerencia de compra
$stock_actual = $producto['stock_actual'];
if ($prediccion > $stock_actual) {
    $sugerencia = ceil($prediccion - $stock_actual);
} else {
    $sugerencia = 0; // No necesita compra
}
```

**Mensaje mostrado:**
> "Basado en los últimos 3 meses, se estima que el próximo mes se venderán ~X unidades de este producto."
> 
> "Sugerencia de compra: Y unidades"

---

## 🔒 Seguridad y Validaciones

### 1. Prevención de Inyección SQL

**✅ SIEMPRE usar sentencias preparadas:**

```php
// ❌ MAL (vulnerable a inyección SQL)
$query = "SELECT * FROM usuarios WHERE usuario = '$usuario'";

// ✅ BIEN (seguro)
$stmt = $pdo->prepare('SELECT * FROM usuarios WHERE usuario = :usuario');
$stmt->execute([':usuario' => $usuario]);
```

### 2. Validación de Entrada

```php
// Validar que los datos sean del tipo correcto
$id_producto = (int)($_POST['id_producto'] ?? 0);
$cantidad = (int)($item['cantidad'] ?? 0);
$precio = (float)($item['precio'] ?? 0);

// Validar que no estén vacíos
if ($id_producto <= 0 || $cantidad <= 0) {
    die('Datos inválidos');
}
```

### 3. Protección de Sesiones

```php
// Verificar que el usuario esté logueado
if (!isset($_SESSION['id_usuario'])) {
    header('Location: /auth/login.php');
    exit;
}

// Verificar rol
if ($_SESSION['rol'] !== 'ADMIN') {
    die('Sin permisos');
}
```

### 4. Contraseñas Hasheadas

```php
// Al crear usuario
$hash = password_hash($password, PASSWORD_BCRYPT);
// Guarda: $hash (nunca la contraseña en texto plano)

// Al verificar login
if (password_verify($password_ingresada, $hash_guardado)) {
    // Login correcto
}
```

---

## 🎨 Frontend: JavaScript y Bootstrap

### Interactividad con JavaScript

**Ejemplo: Relleno automático de precio en ventas**

```javascript
// Cuando cambia el select de producto
tabla.addEventListener('change', function (e) {
    if (e.target.classList.contains('select-producto')) {
        const fila = e.target.closest('tr');
        const inputPrecio = fila.querySelector('.precio');
        const opcionSeleccionada = e.target.options[e.target.selectedIndex];
        
        // Obtiene el precio del atributo data-precio
        const precio = opcionSeleccionada.getAttribute('data-precio');
        if (precio) {
            inputPrecio.value = precio;
            recalcular(); // Recalcula subtotales
        }
    }
});
```

### Gráficas con Chart.js

```javascript
// Crea gráfica de barras con datos de ventas
const ctx = document.getElementById('graficaVentas').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Día 1', 'Día 2', 'Día 3'],
        datasets: [{
            label: 'Ventas',
            data: [1000, 1500, 1200]
        }]
    }
});
```

---

## 🔄 Resumen del Flujo Completo

### Flujo de una VENTA completa:

```
1. Usuario accede a /ventas/nueva.php
    ↓
2. Sistema verifica login (require_login)
    ↓
3. Muestra formulario con productos disponibles
    ↓
4. Usuario selecciona productos (JavaScript rellena precios)
    ↓
5. Usuario hace clic en "Guardar venta"
    ↓
6. POST a /ventas/guardar.php
    ↓
7. BEGIN TRANSACTION
    ↓
8. INSERT INTO ventas (encabezado)
    ↓
9. Para cada producto:
    ├─ INSERT INTO detalle_venta
    └─ UPDATE productos SET stock_actual = stock_actual - cantidad
    ↓
10. COMMIT
    ↓
11. Redirige a /ventas/listar.php
```

### Flujo de una COMPRA completa:

```
1. ADMIN accede a /compras/nueva.php
    ↓
2. Sistema verifica que sea ADMIN (require_admin)
    ↓
3. Muestra formulario con proveedores y productos
    ↓
4. ADMIN selecciona proveedor y productos
    ↓
5. POST a /compras/guardar.php
    ↓
6. BEGIN TRANSACTION
    ↓
7. INSERT INTO compras (encabezado)
    ↓
8. Para cada producto:
    ├─ INSERT INTO detalle_compra
    └─ UPDATE productos SET stock_actual = stock_actual + cantidad
    ↓
9. COMMIT
    ↓
10. Redirige a /compras/listar.php
```

---

## 📝 Puntos Clave para la Exposición

### 1. Arquitectura
- ✅ PHP estructurado sin frameworks pesados
- ✅ Separación de responsabilidades (config, helpers, includes)
- ✅ PDO para acceso a base de datos

### 2. Base de Datos
- ✅ Diseño relacional con claves foráneas
- ✅ Integridad referencial (CASCADE, RESTRICT)
- ✅ Índices para optimizar consultas

### 3. Seguridad
- ✅ Sentencias preparadas (previene SQL injection)
- ✅ Contraseñas hasheadas
- ✅ Control de sesiones y roles

### 4. Funcionalidades
- ✅ CRUD completo de productos, categorías, proveedores
- ✅ Sistema de compras y ventas con transacciones
- ✅ Inventario dinámico (stock se actualiza automáticamente)
- ✅ Reportes con gráficas
- ✅ Predicción simple de consumo

### 5. Tecnologías
- ✅ PHP 8.2, MySQL 8.0
- ✅ Bootstrap para UI responsiva
- ✅ Chart.js para visualización
- ✅ Docker para despliegue

---

## 🎯 Conclusión

El sistema **LOS FRING** es un sistema completo de inventario y ventas que:

1. **Gestiona productos** con categorías y proveedores
2. **Registra compras** (entradas) y **ventas** (salidas)
3. **Mantiene inventario actualizado** automáticamente
4. **Genera reportes** y **predicciones** de consumo
5. **Controla acceso** con roles (ADMIN/CAJERO)
6. **Es seguro** usando mejores prácticas de seguridad

Todo funciona de forma integrada: cuando se registra una compra, el stock aumenta; cuando se registra una venta, el stock disminuye. Los reportes se generan consultando el historial de ventas, y la predicción usa algoritmos simples basados en promedios.

---

**¡Éxito en tu exposición! 🚀**

