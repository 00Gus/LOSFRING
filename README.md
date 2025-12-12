LOS FRING - Sistema de inventario y ventas para TI

Proyecto académico en PHP 8 + MySQL para gestionar inventario, compras, ventas y un módulo sencillo de predicción de consumo.

## 🚀 Tecnologías
- PHP 8.2 (Apache, estilo estructurado/MVC sencillo)
- MySQL 8.0
- HTML5, CSS3, Bootstrap 5.3 (CDN)
- Chart.js 4.4 (CDN)
- Docker y docker-compose

## 📁 Estructura del proyecto
```
Proyecto de bases/
├── index.php                 # Dashboard principal
├── config/
│   └── db.php               # Conexión PDO a MySQL
├── includes/
│   ├── header.php           # Header HTML con Bootstrap
│   ├── navbar.php           # Barra de navegación
│   └── footer.php           # Footer con scripts
├── helpers/
│   └── auth.php            # Funciones de autenticación
├── auth/
│   ├── login.php           # Página de login
│   └── logout.php          # Cerrar sesión
├── productos/              # CRUD de productos
├── categorias/             # CRUD de categorías
├── proveedores/            # CRUD de proveedores
├── compras/                # Módulo de compras (entradas)
├── ventas/                 # Módulo de ventas (punto de venta)
├── inventario/             # Estado de inventario y alertas
├── reportes/               # Reportes de ventas
├── prediccion/             # Predicción de consumo
├── usuarios/               # Administración de usuarios (solo ADMIN)
├── assets/
│   ├── css/styles.css      # Estilos personalizados
│   └── js/app.js           # Scripts JavaScript
├── database/
│   └── minismart_schema.sql # Script de creación de BD
├── Dockerfile              # Imagen PHP+Apache
└── docker-compose.yml      # Orquestación de contenedores
```

## 🐳 Cómo ejecutar con Docker

### Requisitos previos
1. Instalar [Docker Desktop para Windows](https://www.docker.com/products/docker-desktop/)
2. Asegurarse de que Docker Desktop esté ejecutándose

### Pasos
1. Abre una terminal en la carpeta del proyecto
2. Ejecuta:
   ```bash
   docker compose up --build -d
   ```
   (Si tienes una versión antigua de Docker, usa: `docker-compose up --build -d`)

3. Espera a que los contenedores se construyan y arranquen (puede tardar unos minutos la primera vez)

4. Accede a la aplicación en: **http://localhost:8080**

5. Credenciales de acceso:
   - **Usuario:** `admin`
   - **Contraseña:** `admin123`

### Comandos útiles de Docker
```bash
# Ver logs de los contenedores
docker compose logs -f

# Detener los contenedores
docker compose down

# Detener y eliminar volúmenes (resetea la BD)
docker compose down -v

# Reconstruir después de cambios
docker compose up --build
```

### Configuración de Docker
- **Aplicación PHP:** http://localhost:8080
- **MySQL:** localhost:3307 (usuario: `minismart_user`, password: `minismart_pass`, BD: `minismart_db`)
- La base de datos se inicializa automáticamente con el script `database/minismart_schema.sql`

## 💻 Cómo ejecutar con XAMPP (sin Docker)

### Requisitos previos
1. Instalar [XAMPP](https://www.apachefriends.org/) con PHP 8 y MySQL
2. Iniciar los servicios Apache y MySQL desde el panel de control de XAMPP

### Pasos
1. Copia toda la carpeta del proyecto a `C:\xampp\htdocs\minismart` (o la ruta que prefieras)

2. Abre phpMyAdmin (http://localhost/phpmyadmin) y:
   - Crea una base de datos llamada `minismart_db`
   - Selecciona la base de datos
   - Ve a la pestaña "SQL"
   - Copia y pega el contenido de `database/minismart_schema.sql`
   - Ejecuta el script

3. Edita `config/db.php` si es necesario (por defecto usa `localhost`, `root`, sin contraseña)

4. Accede a la aplicación en: **http://localhost/minismart**

5. Credenciales de acceso:
   - **Usuario:** `admin`
   - **Contraseña:** `admin123`

## 👤 Roles de usuario

### ADMIN
- Acceso completo a todos los módulos
- Puede administrar productos, categorías, proveedores, usuarios
- Puede realizar compras y ventas
- Acceso a reportes y predicción

### CAJERO
- Puede realizar ventas
- Puede ver inventario básico
- Acceso limitado a otras funciones

## 📊 Funcionalidades principales

### Módulos implementados
- ✅ **Autenticación:** Login/Logout con roles
- ✅ **Productos:** CRUD completo con categorías
- ✅ **Inventario:** Vista de stock con alertas de stock bajo
- ✅ **Compras:** Registro de compras a proveedores (aumenta stock)
- ✅ **Ventas:** Punto de venta sencillo (disminuye stock)
- ✅ **Reportes:** Ventas por día/mes, top productos (con gráficas Chart.js)
- ✅ **Predicción:** Cálculo de consumo estimado basado en últimos 3 meses
- ✅ **Usuarios:** Administración de usuarios (solo ADMIN)

### Características de seguridad
- Contraseñas hasheadas con `password_hash()` / `password_verify()`
- Consultas SQL preparadas (PDO) para prevenir inyección SQL
- Control de sesiones
- Protección de rutas según roles

## 🧪 Guía de Pruebas del Sistema

### Datos de Prueba (Opcional)

Si quieres cargar datos de ejemplo para probar más rápido:

```bash
# Desde Docker
docker compose exec db mysql -u minismart_user -pminismart_pass minismart_db < database/datos_prueba.sql
```

O desde phpMyAdmin: importa el archivo `database/datos_prueba.sql`

### Flujo de Prueba Recomendado

1. **Login y Dashboard**
   - Accede a http://localhost:8080
   - Inicia sesión con `admin` / `admin123`
   - Verifica que veas el dashboard con estadísticas

2. **Configuración Inicial**
   - **Categorías:** Crea algunas categorías (Bebidas, Lácteos, Abarrotes)
   - **Proveedores:** Crea al menos un proveedor con datos de contacto
   - **Productos:** Crea 3-5 productos con:
     - Precios de compra y venta
     - Stock inicial (ej: 50 unidades)
     - Stock mínimo (ej: 10 unidades)

3. **Probar Compras (Entradas)**
   - Ve a **Compras → Nueva compra**
   - Selecciona un proveedor
   - Agrega productos con cantidades (ej: 20 unidades de Coca Cola)
   - Guarda la compra
   - Verifica que el stock aumentó en **Inventario**

4. **Probar Ventas (Salidas)**
   - Ve a **Ventas → Nueva venta**
   - Selecciona forma de pago
   - Agrega productos al ticket (ej: 5 unidades de Coca Cola)
   - Guarda la venta
   - Verifica que el stock disminuyó

5. **Verificar Inventario**
   - Ve a **Inventario**
   - Revisa los indicadores:
     - ✅ Verde/OK: Stock suficiente
     - ⚠️ Rojo/Stock bajo: Necesita reabastecimiento

6. **Probar Reportes**
   - Ve a **Reportes → Ventas**
   - Selecciona un rango de fechas
   - Verifica:
     - Gráfica de ventas por día (Chart.js)
     - Tabla de top 10 productos más vendidos

7. **Probar Predicción**
   - Ve a **Predicción → Producto**
   - Selecciona un producto con historial de ventas
   - Verifica:
     - Gráfica de consumo mensual
     - Predicción basada en últimos 3 meses
     - Sugerencia de compra si es necesaria

8. **Probar Roles de Usuario**
   - Ve a **Usuarios** (solo ADMIN puede ver esto)
   - Crea un usuario con rol **CAJERO**
   - Cierra sesión y entra con el usuario CAJERO
   - Verifica que tenga acceso limitado (solo ventas e inventario básico)

### Casos de Prueba Específicos

- ✅ **Stock bajo:** Reduce el stock de un producto por debajo del mínimo y verifica la alerta
- ✅ **Múltiples compras/ventas:** Realiza varias transacciones y verifica que el stock se actualice correctamente
- ✅ **Reportes con datos:** Realiza varias ventas en diferentes fechas y verifica los reportes
- ✅ **Predicción con historial:** Realiza ventas del mismo producto durante varios meses y verifica la predicción

## 🔧 Solución de problemas

### Error de conexión a la base de datos
- Verifica que MySQL esté corriendo (XAMPP) o que el contenedor `minismart_db` esté activo (Docker)
- Revisa las credenciales en `config/db.php`

### Error 404 en rutas
- Asegúrate de que Apache esté configurado para permitir `.htaccess` o que las rutas estén correctas
- En Docker, verifica que el volumen esté montado correctamente

### Problemas con Docker
- Asegúrate de que Docker Desktop esté ejecutándose
- Verifica que los puertos 8080 y 3307 no estén en uso
- Revisa los logs: `docker compose logs`



