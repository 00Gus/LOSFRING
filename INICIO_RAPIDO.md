# 🚀 Inicio Rápido de LOS FRING

## Para detener el sistema (cuando termines de trabajar)

Abre una terminal en la carpeta del proyecto y ejecuta:

```bash
docker compose down
```

Esto detiene los contenedores pero **NO elimina los datos** (tus productos, ventas, etc. se guardan).

## Para volver a iniciar el sistema (cuando quieras trabajar de nuevo)

1. Abre una terminal (PowerShell o CMD) en la carpeta del proyecto:
   ```
   C:\Users\gusta\OneDrive\Escritorio\Proyecto de bases
   ```

2. Ejecuta:
   ```bash
   docker compose up -d
   ```

3. Espera unos segundos (10-20 segundos) para que MySQL termine de iniciar

4. Abre tu navegador en: **http://localhost:8080**

5. Inicia sesión con:
   - Usuario: `admin`
   - Contraseña: `admin123`

## ⚠️ Importante

- **Los datos se guardan automáticamente** en un volumen de Docker, así que no se perderán al detener los contenedores
- Si quieres **eliminar todo** (resetear la base de datos), usa: `docker compose down -v`
- Asegúrate de que **Docker Desktop esté ejecutándose** antes de levantar los contenedores

## Comandos útiles

```bash
# Ver estado de los contenedores
docker compose ps

# Ver logs (útil si algo no funciona)
docker compose logs -f

# Reiniciar los contenedores
docker compose restart

# Detener sin eliminar datos
docker compose down

# Detener y eliminar TODO (resetea la BD)
docker compose down -v
```

## 📝 Notas

- La primera vez que levantes los contenedores puede tardar más (descarga imágenes)
- Los datos están seguros en el volumen `proyectodebases_db_data`
- Si cambias código PHP, solo necesitas refrescar la página (el código se monta como volumen)

