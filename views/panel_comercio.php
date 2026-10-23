<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] != 2) {
    header("Location: login.html");
    exit();
}
require_once "../config/conexion.php";
$usuario_id = $_SESSION['usuario_id'];

// Obtener los datos reales de este comercio
$buscar_c = $conexion->query("SELECT * FROM comercios WHERE usuario_id = $usuario_id");
$datos_comercio = $buscar_c->fetch_assoc();
$comercio_id = $datos_comercio['id'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Kero 🍊 - Panel Comercio</title>
    <!-- Conexión a tu hoja de estilos externa -->
    <!-- <link rel="stylesheet" href="../css/comercio.css"> -->
</head>
<body>

    <header class="cabecera-comercio">
        <h2>🏪 Tu Comercio: <?php echo htmlspecialchars($datos_comercio['nombre_local']); ?></h2>
        <p class="zona-comercio"><strong>Zona:</strong> <?php echo htmlspecialchars($datos_comercio['zona_caracas']); ?></p>
    </header>
    
    <hr>

    <main class="contenido-panel-comercio">

        <!-- 📋 FORMULARIO 1: REGISTRAR PRODUCTO BASE -->
        <section class="seccion-formulario seccion-catalogo-nuevo">
            <h3>Cargar Producto Nuevo al Menú:</h3>
            <form action="../modules/procesar_producto_base.php" method="POST" enctype="multipart/form-data" class="form-producto-base">
                <p class="campo-form">
                    <label for="nombre">Nombre del Alimento:</label><br>
                    <input type="text" id="nombre" name="nombre" required placeholder="Ej. Cachito de Jamón" class="input-texto">
                </p>
                <p class="campo-form">
                    <label for="precio_real">Precio Real en Tienda ($):</label><br>
                    <input type="number" step="0.01" id="precio_real" name="precio_real" required placeholder="Ej. 3.00" oninput="calcularDescuentoPanel()" class="input-numero">
                </p>
                <p class="campo-form">
                    <label for="precio_kero_sugerido">Precio Kero Sugerido (-40%):</label><br>
                    <input type="number" step="0.01" id="precio_kero_sugerido" name="precio_kero_sugerido" required placeholder="Ej. 1.80" class="input-numero">
                    <br><small class="texto-ayuda">Calculado automáticamente al 40% de descuento. Puedes modificarlo libremente.</small>
                </p>
                <p class="campo-form">
                    <label for="descripcion">Descripción:</label><br>
                    <textarea id="descripcion" name="descripcion" class="textarea-descripcion"></textarea>
                </p>
                <p class="campo-form">
                    <label for="foto">Foto del Producto (Galería o Cámara):</label><br>
                    <input type="file" id="foto" name="foto" accept="image/*" class="input-archivo">
                </p>
                <p class="campo-boton">
                    <button type="submit" class="btn-guardar-catalogo">Guardar en Catálogo 💾</button>
                </p>
            </form>
        </section>

        <hr>

        <!-- 🍊 FORMULARIO 2: PUBLICAR EXCEDENTE -->
        <section class="seccion-formulario seccion-publicar-remate">
            <h3>Publicar Remate del Día</h3>
            <form action="../modules/procesar_producto.php" method="POST" class="form-excedente">
                <p class="campo-form">
                    <label for="producto_base_id">Selecciona el Alimento a salvar:</label><br>
                    <select id="producto_base_id" name="producto_base_id" required onchange="autocompletarPrecioHoy()" class="select-producto">
                        <option value="">-- Elige un producto --</option>
                        <?php
                        $productos_catalogo = $conexion->query("SELECT id, nombre, precio_kero_sugerido FROM productos_base WHERE comercio_id = $comercio_id AND deleted_at IS NULL");
                        while ($prod = $productos_catalogo->fetch_assoc()) {
                            echo "<option value='" . $prod['id'] . "' data-precio='" . $prod['precio_kero_sugerido'] . "'>" . htmlspecialchars($prod['nombre']) . " ($" . $prod['precio_kero_sugerido'] . ")</option>";
                        }
                        ?>
                    </select>
                </p>
                <p class="campo-form">
                    <label for="stock_disponible">Stock Disponible:</label><br>
                    <input type="number" id="stock_disponible" name="stock_disponible" min="1" required placeholder="Ej. 5" class="input-numero">
                </p>
                <p class="campo-form">
                    <label for="precio_hoy">Precio Especial Hoy ($):</label><br>
                    <input type="number" step="0.01" id="precio_hoy" name="precio_hoy" required placeholder="Se autocompleta solo" class="input-numero">
                    <br><small class="texto-ayuda">Cargado automáticamente desde tu catálogo base.</small>
                </p>
                <p class="campo-boton">
                    <button type="submit" class="btn-publicar-remate">Publicar Excedente 🍊</button>
                </p>
            </form>
        </section>

        <hr>

        <!-- 📊 TABLA A: GESTIÓN DE EXCEDENTES PUBLICADOS -->
        <section class="seccion-tabla seccion-excedentes-hoy">
            <h3>📊 Tus Publicaciones Diarias (Excedentes):</h3>
            <table class="tabla-datos tabla-excedentes">
                <thead>
                    <tr class="fila-cabecera">
                        <th>Foto</th>
                        <th>Alimento</th>
                        <th>Stock</th>
                        <th>Precio Hoy</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $fecha_hoy = date('Y-m-d');
                    $mis_publicaciones = $conexion->query("
                        SELECT pe.*, pb.nombre AS nombre_alimento, pb.foto_url
                        FROM publicaciones_excedentes pe
                        LEFT JOIN productos_base pb ON pe.producto_base_id = pb.id
                        WHERE pe.comercio_id = $comercio_id AND pe.fecha_publicacion = '$fecha_hoy'
                    ");
                    if ($mis_publicaciones->num_rows > 0) {
                        while ($pub = $mis_publicaciones->fetch_assoc()) {
                            $foto = !empty($pub['foto_url']) ? "../" . $pub['foto_url'] : "https://via.placeholder.com/60";
                            echo "<tr>";
                            echo "<td class='col-foto'><img src='" . htmlspecialchars($foto) . "' alt='Foto' class='foto-miniatura'></td>";
                            echo "<td class='col-nombre'>" . htmlspecialchars($pub['nombre_alimento'] ?? 'Producto no disponible') . "</td>";
                            echo "<td class='col-stock'>" . $pub['stock_disponible'] . "</td>";
                            echo "<td class='col-precio'>$" . number_format($pub['precio_hoy'], 2) . "</td>";
                            echo "<td class='col-acciones'>";
                            echo "<a href='../modules/gestion_acciones.php?id=" . $pub['id'] . "&tabla=excedente&accion=eliminar' class='enlace-eliminar' onclick='return confirm(\"¿Seguro que deseas borrar esta oferta del día?\")'>🗑️ Eliminar Oferta</a>";
                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5' class='sin-datos'>No hay mermas publicadas hoy.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </section>

        <br>

        <!-- 📋 TABLA B: GESTIÓN DEL CATÁLOGO BASE -->
        <section class="seccion-tabla seccion-catalogo-base">
            <h3>📋 Tu Catálogo Fijo (Productos Base):</h3>
            <table class="tabla-datos tabla-catalogo">
                <thead>
                    <tr class="fila-cabecera">
                        <th>Foto</th>
                        <th>Nombre</th>
                        <th>Precio Real</th>
                        <th>Precio Sugerido</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $mi_catalogo = $conexion->query("SELECT * FROM productos_base WHERE comercio_id = $comercio_id AND deleted_at IS NULL");
                    if ($mi_catalogo->num_rows > 0) {
                        while ($cat = $mi_catalogo->fetch_assoc()) {
                            $foto_cat = !empty($cat['foto_url']) ? "../" . $cat['foto_url'] : "https://via.placeholder.com/60";
                            echo "<tr>";
                            echo "<td class='col-foto'><img src='" . htmlspecialchars($foto_cat) . "' alt='Foto' class='foto-miniatura'></td>";
                            echo "<td class='col-nombre'>" . htmlspecialchars($cat['nombre']) . "</td>";
                            echo "<td class='col-precio'>$" . number_format($cat['precio_real'], 2) . "</td>";
                            echo "<td class='col-precio'>$" . number_format($cat['precio_kero_sugerido'], 2) . "</td>";
                            echo "<td class='col-acciones'>";
                            echo "<a href='editar_producto_base.php?id=" . $cat['id'] . "' class='enlace-editar'>✏️ Editar</a> | ";
                            echo "<a href='../modules/gestion_acciones.php?id=" . $cat['id'] . "&tabla=base&accion=borrar_logico' class='enlace-eliminar' onclick='return confirm(\"¿Seguro que deseas eliminar este producto de tu catálogo base?\")'>🗑️ Borrar</a>";
                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5' class='sin-datos'>Tu catálogo está vacío.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </section>

    </main>

    <hr>
    
    <footer class="pie-panel-comercio">
        <p><a href="../modules/cerrar_sesion.php" class="link-cerrar-sesion">Cerrar Sesión segura</a></p>
    </footer>

    <!-- JAVASCRIPT MÁGICO PARA EL PANEL DE COMERCIO -->
    <script>
    function calcularDescuentoPanel() {
        let inputReal = document.getElementById('precio_real');
        let inputKero = document.getElementById('precio_kero_sugerido');
        
        if (inputReal && inputKero) {
            let precioReal = parseFloat(inputReal.value);
            if (!isNaN(precioReal) && precioReal > 0) {
                let descuento = precioReal * 0.60;
                inputKero.value = descuento.toFixed(2);
            } else {
                inputKero.value = '';
            }
        }
    }

    function autocompletarPrecioHoy() {
        let selectProducto = document.getElementById('producto_base_id');
        let inputPrecioHoy = document.getElementById('precio_hoy');
        let opcionSeleccionada = selectProducto.options[selectProducto.selectedIndex];
        let precioGuardado = opcionSeleccionada.getAttribute('data-precio');
        
        if (precioGuardado) {
            inputPrecioHoy.value = precioGuardado;
        } else {
            inputPrecioHoy.value = '';
        }
    }
    </script> 
</body>
</html>
<?php $conexion->close(); ?>