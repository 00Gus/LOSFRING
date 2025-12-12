# 👤 Guía de Usuarios y Acceso a MiniSmart

## 🔐 Usuario por Defecto

Al instalar el sistema, existe un usuario administrador inicial:

- **Usuario:** `admin`
- **Contraseña:** `admin123`
- **Rol:** ADMIN

## 🚪 Cómo Iniciar Sesión

1. Abre tu navegador en: **http://localhost:8080** (o la URL donde tengas el sistema)
2. Serás redirigido automáticamente a la página de login: `/auth/login.php`
3. Ingresa:
   - **Usuario:** (el nombre de usuario, ej: `admin`)
   - **Contraseña:** (la contraseña del usuario)
4. Haz clic en **"Entrar"**
5. Si las credenciales son correctas, serás redirigido al dashboard principal

## 👥 Tipos de Usuarios (Roles)

El sistema tiene **2 tipos de usuarios** con diferentes permisos:

### 🔴 ADMIN (Administrador)
**Acceso completo a todo el sistema:**

✅ Puede hacer:
- Ver y administrar **Productos** (crear, editar, eliminar)
- Ver y administrar **Categorías**
- Ver y administrar **Proveedores**
- Ver y administrar **Usuarios** (crear, editar, eliminar otros usuarios)
- Realizar **Compras** (entradas de inventario)
- Realizar **Ventas** (punto de venta)
- Ver **Inventario** y alertas de stock
- Ver **Reportes** de ventas
- Ver **Predicción** de consumo

### 🟡 CAJERO
**Acceso limitado, enfocado en operaciones diarias:**

✅ Puede hacer:
- Realizar **Ventas** (punto de venta)
- Ver **Inventario** básico (estado de stock)
- Ver **Reportes** de ventas (solo lectura)
- Ver **Predicción** de consumo (solo lectura)

❌ **NO puede:**
- Administrar productos, categorías o proveedores
- Crear o editar usuarios
- Realizar compras (solo ADMIN puede registrar compras)

## ➕ Cómo Crear Nuevos Usuarios

**Solo los usuarios ADMIN pueden crear nuevos usuarios:**

1. Inicia sesión con un usuario **ADMIN**
2. Ve al menú **"Administración"** → **"Usuarios"**
3. En el formulario de la izquierda, completa:
   - **Nombre:** Nombre completo del usuario (ej: "Juan Pérez")
   - **Usuario:** Nombre de usuario para iniciar sesión (ej: "juan", "cajero1")
   - **Rol:** Selecciona **ADMIN** o **CAJERO**
   - **Contraseña:** Contraseña para el nuevo usuario
4. Haz clic en **"Crear"**
5. El nuevo usuario ya puede iniciar sesión con sus credenciales

## ✏️ Cómo Editar un Usuario

1. Como **ADMIN**, ve a **Usuarios**
2. En la tabla de la derecha, haz clic en **"Editar"** del usuario que quieres modificar
3. Modifica los campos que necesites:
   - Puedes cambiar nombre, usuario y rol
   - Para cambiar la contraseña, escribe una nueva (si dejas en blanco, no se cambia)
4. Haz clic en **"Actualizar"**

## 🗑️ Cómo Eliminar un Usuario

1. Como **ADMIN**, ve a **Usuarios**
2. En la tabla, haz clic en **"Eliminar"** del usuario que quieres eliminar
3. Confirma la eliminación
4. **Nota:** No puedes eliminarte a ti mismo

## 🔒 Seguridad

- Las contraseñas se guardan **hasheadas** (encriptadas) en la base de datos
- No se pueden ver las contraseñas originales
- Si un usuario olvida su contraseña, un ADMIN debe editarla desde **Usuarios**

## 📋 Resumen de Accesos por Rol

| Funcionalidad | ADMIN | CAJERO |
|--------------|-------|--------|
| Dashboard | ✅ | ✅ |
| Inventario | ✅ | ✅ |
| Ventas | ✅ | ✅ |
| Compras | ✅ | ❌ |
| Productos | ✅ | ❌ |
| Categorías | ✅ | ❌ |
| Proveedores | ✅ | ❌ |
| Usuarios | ✅ | ❌ |
| Reportes | ✅ | ✅ |
| Predicción | ✅ | ✅ |

## 💡 Ejemplo de Uso

**Escenario típico:**
- **1 usuario ADMIN:** El dueño/gerente que administra todo
- **2-3 usuarios CAJERO:** Los empleados que solo realizan ventas

**Flujo:**
1. El ADMIN crea los productos, categorías y proveedores
2. El ADMIN registra las compras (entradas de inventario)
3. Los CAJEROS realizan las ventas durante el día
4. El ADMIN revisa reportes y predicciones para tomar decisiones

## 🆘 Si Olvidaste tu Contraseña

Si eres ADMIN y olvidaste tu contraseña:
1. Necesitas acceso directo a la base de datos
2. O que otro ADMIN te cambie la contraseña

Si eres CAJERO:
- Un ADMIN debe cambiar tu contraseña desde **Usuarios**

---

**Nota:** Es recomendable cambiar la contraseña del usuario `admin` después de la primera instalación por seguridad.

