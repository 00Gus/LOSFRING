<?php
// ventas/nueva.php
// Pantalla tipo caja para registrar una venta (salida de inventario).

require_once __DIR__ . '/../helpers/auth.php';
require_login(); // ADMIN y CAJERO pueden vender
require_once __DIR__ . '/../config/db.php';

$pdo = getPDO();

// Obtenemos productos para un select simple (se podría mejorar con búsqueda dinámica)
$productos = $pdo->query('SELECT id_producto, nombre, codigo_barras, precio_venta FROM productos ORDER BY nombre')->fetchAll();

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<div class="container">
    <h1 class="h4 mb-3">Nueva venta</h1>

    <form method="post" action="guardar.php">
        <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label">Fecha</label>
                <input type="text" class="form-control" value="<?php echo date('Y-m-d H:i'); ?>" disabled>
            </div>
            <div class="col-md-4">
                <label for="forma_pago" class="form-label">Forma de pago</label>
                <select name="forma_pago" id="forma_pago" class="form-select">
                    <option value="EFECTIVO">Efectivo</option>
                    <option value="TARJETA">Tarjeta</option>
                    <option value="TRANSFERENCIA">Transferencia</option>
                </select>
            </div>
        </div>

        <h5>Productos</h5>
        <p class="text-muted">
            Puedes buscar en el listado por nombre o código de barras (Ctrl+F en el navegador) y ajustar cantidades.
        </p>

        <table class="table table-sm align-middle" id="tabla-detalle-venta">
            <thead>
            <tr>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio venta</th>
                <th>Subtotal</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>
                    <select name="items[0][id_producto]" class="form-select select-producto" required>
                        <option value="">-- Producto --</option>
                        <?php foreach ($productos as $p): ?>
                            <option value="<?php echo (int)$p['id_producto']; ?>" data-precio="<?php echo number_format((float)$p['precio_venta'], 2, '.', ''); ?>">
                                <?php
                                $etiqueta = ($p['codigo_barras'] ? $p['codigo_barras'] . ' - ' : '') . $p['nombre'];
                                echo htmlspecialchars($etiqueta);
                                ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td><input type="number" name="items[0][cantidad]" class="form-control cantidad" value="1" min="1" required></td>
                <td><input type="number" step="0.01" name="items[0][precio_venta]" class="form-control precio" value="0" required></td>
                <td><span class="subtotal">0.00</span></td>
                <td><button type="button" class="btn btn-danger btn-sm btn-eliminar-fila">&times;</button></td>
            </tr>
            </tbody>
        </table>

        <div class="mb-3">
            <button type="button" class="btn btn-secondary btn-sm" id="btn-agregar-fila">Agregar producto</button>
        </div>

        <div class="text-end mb-3">
            <h5>Total: $<span id="total-venta">0.00</span></h5>
        </div>

        <button type="submit" class="btn btn-primary">Guardar venta</button>
        <a href="listar.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<script>
    // JS simple para manejar filas de detalle y totales en ventas
    (function () {
        const tabla = document.getElementById('tabla-detalle-venta').querySelector('tbody');
        const btnAgregar = document.getElementById('btn-agregar-fila');
        const totalSpan = document.getElementById('total-venta');
        let indice = 1;

        function recalcular() {
            let total = 0;
            tabla.querySelectorAll('tr').forEach(tr => {
                const cantidad = parseFloat(tr.querySelector('.cantidad').value) || 0;
                const precio = parseFloat(tr.querySelector('.precio').value) || 0;
                const sub = cantidad * precio;
                tr.querySelector('.subtotal').textContent = sub.toFixed(2);
                total += sub;
            });
            totalSpan.textContent = total.toFixed(2);
        }

        // Función para rellenar el precio cuando se selecciona un producto
        function actualizarPrecio(selectProducto) {
            const fila = selectProducto.closest('tr');
            const inputPrecio = fila.querySelector('.precio');
            const opcionSeleccionada = selectProducto.options[selectProducto.selectedIndex];
            
            if (opcionSeleccionada.value !== '') {
                const precio = opcionSeleccionada.getAttribute('data-precio');
                if (precio) {
                    inputPrecio.value = precio;
                    recalcular();
                }
            } else {
                inputPrecio.value = '0';
                recalcular();
            }
        }

        // Event listener para cuando cambia el select de producto
        tabla.addEventListener('change', function (e) {
            if (e.target.classList.contains('select-producto')) {
                actualizarPrecio(e.target);
            }
        });

        tabla.addEventListener('input', function (e) {
            if (e.target.classList.contains('cantidad') || e.target.classList.contains('precio')) {
                recalcular();
            }
        });

        tabla.addEventListener('click', function (e) {
            if (e.target.classList.contains('btn-eliminar-fila')) {
                const filas = tabla.querySelectorAll('tr');
                if (filas.length > 1) {
                    e.target.closest('tr').remove();
                    recalcular();
                }
            }
        });

        btnAgregar.addEventListener('click', function () {
            const nueva = tabla.querySelector('tr').cloneNode(true);
            nueva.querySelectorAll('select, input').forEach(el => {
                if (el.name.includes('items')) {
                    el.name = el.name.replace(/\[\d+]/, '[' + indice + ']');
                }
                if (el.classList.contains('cantidad')) el.value = 1;
                if (el.classList.contains('precio')) el.value = 0;
                if (el.classList.contains('select-producto')) el.selectedIndex = 0;
            });
            nueva.querySelector('.subtotal').textContent = '0.00';
            tabla.appendChild(nueva);
            indice++;
        });

        recalcular();
    })();
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>




