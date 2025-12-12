<?php
// compras/nueva.php
// Formulario para registrar una nueva compra (entradas a inventario).

require_once __DIR__ . '/../helpers/auth.php';
require_admin(); // Suponemos que solo ADMIN registra compras
require_once __DIR__ . '/../config/db.php';

$pdo = getPDO();

$proveedores = $pdo->query('SELECT * FROM proveedores ORDER BY nombre')->fetchAll();
$productos = $pdo->query('SELECT id_producto, nombre, precio_compra FROM productos ORDER BY nombre')->fetchAll();

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<div class="container">
    <h1 class="h4 mb-3">Nueva compra</h1>

    <form method="post" action="guardar.php">
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="id_proveedor" class="form-label">Proveedor</label>
                <select name="id_proveedor" id="id_proveedor" class="form-select" required>
                    <option value="">-- Selecciona proveedor --</option>
                    <?php foreach ($proveedores as $prov): ?>
                        <option value="<?php echo (int)$prov['id_proveedor']; ?>">
                            <?php echo htmlspecialchars($prov['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Fecha</label>
                <input type="text" class="form-control" value="<?php echo date('Y-m-d H:i'); ?>" disabled>
            </div>
        </div>

        <h5>Detalle de productos</h5>
        <p class="text-muted">Agrega uno o más productos con cantidad y precio de compra.</p>

        <table class="table table-sm align-middle" id="tabla-detalle-compra">
            <thead>
            <tr>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio compra</th>
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
                            <option value="<?php echo (int)$p['id_producto']; ?>" data-precio="<?php echo number_format((float)$p['precio_compra'], 2, '.', ''); ?>">
                                <?php echo htmlspecialchars($p['nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td><input type="number" name="items[0][cantidad]" class="form-control cantidad" value="1" min="1" required></td>
                <td><input type="number" step="0.01" name="items[0][precio_compra]" class="form-control precio" value="0" required></td>
                <td><span class="subtotal">0.00</span></td>
                <td><button type="button" class="btn btn-danger btn-sm btn-eliminar-fila">&times;</button></td>
            </tr>
            </tbody>
        </table>

        <div class="mb-3">
            <button type="button" class="btn btn-secondary btn-sm" id="btn-agregar-fila">Agregar producto</button>
        </div>

        <div class="text-end mb-3">
            <h5>Total: $<span id="total-compra">0.00</span></h5>
        </div>

        <button type="submit" class="btn btn-primary">Guardar compra</button>
        <a href="listar.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<script>
    // JS simple para manejar filas de detalle y totales
    (function () {
        const tabla = document.getElementById('tabla-detalle-compra').querySelector('tbody');
        const btnAgregar = document.getElementById('btn-agregar-fila');
        const totalSpan = document.getElementById('total-compra');
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

        // Rellena precio al cambiar de producto
        function actualizarPrecio(selectProducto) {
            const fila = selectProducto.closest('tr');
            const inputPrecio = fila.querySelector('.precio');
            const opcion = selectProducto.options[selectProducto.selectedIndex];
            if (opcion && opcion.value !== '') {
                const precio = opcion.getAttribute('data-precio');
                if (precio) {
                    inputPrecio.value = precio;
                    recalcular();
                }
            } else {
                inputPrecio.value = '0';
                recalcular();
            }
        }

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

        // inicial
        recalcular();
    })();
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>







