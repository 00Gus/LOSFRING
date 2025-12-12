# 📘 Guía de Instalación de XAMPP y Configuración de MiniSmart

## 🔽 Paso 1: Descargar XAMPP

1. Ve al sitio oficial: **https://www.apachefriends.org/**
2. Haz clic en **"Download"** (botón grande verde)
3. Selecciona la versión para **Windows** (PHP 8.x recomendado)
4. El archivo descargado será algo como: `xampp-windows-x64-8.x.x-installer.exe` (~150 MB)

## 💾 Paso 2: Instalar XAMPP

1. **Ejecuta el instalador** como Administrador (clic derecho → "Ejecutar como administrador")
2. Si aparece una advertencia de Windows Defender, haz clic en **"Más información"** → **"Ejecutar de todas formas"**
3. En el asistente de instalación:
   - **Selecciona los componentes:**
     - ✅ Apache
     - ✅ MySQL
     - ✅ PHP
     - ✅ phpMyAdmin
     - (Puedes desmarcar FileZilla, Mercury, Tomcat si no los necesitas)
   - **Elige la carpeta de instalación:** Por defecto es `C:\xampp` (recomendado)
   - Haz clic en **"Next"** y luego **"Install"**
4. Espera a que termine la instalación (puede tardar unos minutos)
5. Al finalizar, haz clic en **"Finish"**

## 🚀 Paso 3: Iniciar XAMPP

1. Abre el **Panel de Control de XAMPP**:
   - Busca "XAMPP Control Panel" en el menú de inicio de Windows
   - O ve a `C:\xampp\xampp-control.exe`
2. En el panel verás módulos como Apache, MySQL, etc.
3. Haz clic en **"Start"** junto a:
   - ✅ **Apache** (debe ponerse verde)
   - ✅ **MySQL** (debe ponerse verde)
4. Si aparece un aviso de Windows Firewall, haz clic en **"Permitir acceso"**

### ⚠️ Solución de problemas comunes:

**Problema: Puerto 80 ocupado (Apache no inicia)**
- Solución 1: Cierra otras aplicaciones que usen el puerto 80 (Skype, IIS, etc.)
- Solución 2: Cambia el puerto de Apache:
  - Haz clic en **"Config"** junto a Apache → **"httpd.conf"**
  - Busca `Listen 80` y cámbialo a `Listen 8080`
  - Guarda y reinicia Apache
  - Luego accede a: `http://localhost:8080`

**Problema: Puerto 3306 ocupado (MySQL no inicia)**
- Cierra otras instancias de MySQL que puedan estar corriendo
- O cambia el puerto en la configuración de MySQL

## ✅ Paso 4: Verificar que XAMPP funciona

1. Abre tu navegador web
2. Ve a: **http://localhost**
3. Deberías ver la página de bienvenida de XAMPP con el logo y menú
4. Si cambiaste el puerto a 8080, usa: **http://localhost:8080**

## 📁 Paso 5: Copiar el proyecto MiniSmart a XAMPP

1. **Copia toda la carpeta del proyecto** `Proyecto de bases` a:
   ```
   C:\xampp\htdocs\minismart
   ```
   (Crea la carpeta `minismart` dentro de `htdocs` si no existe)

2. La estructura debería quedar así:
   ```
   C:\xampp\htdocs\minismart\
   ├── index.php
   ├── config\
   ├── auth\
   ├── productos\
   └── ... (todos los archivos del proyecto)
   ```

## 🗄️ Paso 6: Crear la base de datos

1. Abre **phpMyAdmin** en tu navegador:
   - Ve a: **http://localhost/phpmyadmin**
   - (O si cambiaste el puerto: **http://localhost:8080/phpmyadmin**)

2. En el panel izquierdo, haz clic en **"Nueva"** o **"New"** para crear una base de datos

3. Configura:
   - **Nombre de la base de datos:** `minismart_db`
   - **Cotejamiento:** `utf8mb4_unicode_ci`
   - Haz clic en **"Crear"** o **"Create"**

4. Selecciona la base de datos `minismart_db` en el panel izquierdo

5. Ve a la pestaña **"SQL"** (arriba)

6. Abre el archivo `database/minismart_schema.sql` del proyecto con un editor de texto (Notepad, VS Code, etc.)

7. **Copia TODO el contenido** del archivo SQL

8. **Pega el contenido** en el área de texto de phpMyAdmin

9. Haz clic en **"Continuar"** o **"Go"** (botón abajo a la derecha)

10. Deberías ver mensajes de éxito como "Tabla creada correctamente" para cada tabla

11. Verifica que se crearon las tablas:
    - En el panel izquierdo, expande `minismart_db`
    - Deberías ver: `usuarios`, `categorias`, `productos`, `proveedores`, `compras`, `detalle_compra`, `ventas`, `detalle_venta`

## ⚙️ Paso 7: Verificar configuración de conexión

El archivo `config/db.php` ya está configurado para XAMPP por defecto:
- **Host:** `localhost`
- **Usuario:** `root`
- **Contraseña:** (vacía, que es lo normal en XAMPP)
- **Base de datos:** `minismart_db`

Si tu XAMPP tiene una contraseña diferente para MySQL, edita `config/db.php` y cambia:
```php
$DB_PASS = getenv('DB_PASS') ?: 'tu_contraseña_aqui';
```

## 🌐 Paso 8: Acceder a MiniSmart

1. Abre tu navegador web
2. Ve a: **http://localhost/minismart**
   - (O si cambiaste el puerto de Apache: **http://localhost:8080/minismart**)

3. Deberías ser redirigido automáticamente a la página de login:
   - **http://localhost/minismart/auth/login.php**

4. **Credenciales de acceso:**
   - **Usuario:** `admin`
   - **Contraseña:** `admin123`

5. ¡Listo! Ya puedes usar MiniSmart 🎉

## 🔍 Verificar que todo funciona

Después de iniciar sesión, deberías poder:
- ✅ Ver el dashboard principal
- ✅ Navegar por el menú (Productos, Categorías, Proveedores, etc.)
- ✅ Crear un producto de prueba
- ✅ Ver el inventario

## 📝 Notas importantes

- **Mantén XAMPP abierto:** El Panel de Control de XAMPP debe estar abierto y Apache/MySQL deben estar en verde mientras uses la aplicación
- **Rutas relativas:** El proyecto usa rutas que empiezan con `/` (absolutas), así que debería funcionar bien desde `http://localhost/minismart`
- **Permisos:** Si tienes problemas al guardar archivos, verifica los permisos de la carpeta `htdocs`

## 🆘 Si algo no funciona

1. **Error de conexión a la base de datos:**
   - Verifica que MySQL esté corriendo (verde en XAMPP)
   - Revisa que la base de datos `minismart_db` exista en phpMyAdmin
   - Verifica las credenciales en `config/db.php`

2. **Error 404 (página no encontrada):**
   - Verifica que copiaste todos los archivos a `C:\xampp\htdocs\minismart`
   - Asegúrate de que Apache esté corriendo
   - Prueba acceder a: `http://localhost/minismart/index.php` directamente

3. **Página en blanco:**
   - Revisa los logs de errores de PHP en: `C:\xampp\php\logs\php_error_log`
   - O habilita la visualización de errores temporalmente en `config/db.php` agregando al inicio:
     ```php
     error_reporting(E_ALL);
     ini_set('display_errors', 1);
     ```

¡Éxito con tu instalación! 🚀

