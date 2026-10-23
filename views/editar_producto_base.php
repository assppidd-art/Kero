<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] != 2) {
    header("Location: login.html");
    exit();
}
require_once "../config/conexion.php";

$usuario_id = $_SESSION['usuario_id'];

// Obtener el ID del comercio logueado
$buscar_c = $conexion->query("SELECT id FROM comercios WHERE usuario_id = $usuario_id");
$datos_comercio = $buscar_c->fetch_assoc();
$comercio_id = $datos_comercio['id'];

// Verificar que venga el ID del producto a editar
if (!isset($_GET['id'])) {
    header("Location: panel_comercio.php");
    exit();
}

$producto_id = intval($_GET['id']);

// Buscar los datos actuales de este producto
$stmt = $conexion->prepare("SELECT * FROM productos_base WHERE id = ? AND comercio_id = ? AND deleted_at IS NULL");
$stmt->bind_param("ii", $producto_id, $comercio_id);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    echo "❌ Producto no encontrado o no pertenece a tu comercio.";
    echo "<br><a href='panel_comercio.php'>Volver al Panel</a>";
    exit();
}

$prod = $resultado->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Kero - Editar Producto</title>
</head>
<body>

    <h2> Editar Producto: <?php echo htmlspecialchars($prod['nombre']); ?></h2>
    <a href="panel_comercio.php"> Cancelar y Volver</a>
    <hr>

    <form action="../modules/procesar_producto_base.php" method="POST">
        <!-- Campo Oculto con el ID del producto -->
        <input type="hidden" name="producto_id" value="<?php echo $prod['id']; ?>">

        <p>
            <label for="nombre">Nombre del Producto:</label><br>
            <input type="text" id="nombre" name="nombre" required value="<?php echo htmlspecialchars($prod['nombre']); ?>">
        </p>
        <p>
            <label for="precio_real">Precio Real en Tienda ($):</label><br>
            <input type="number" step="0.01" id="precio_real" name="precio_real" required value="<?php echo $prod['precio_real']; ?>" oninput="calcularDescuentoPanel()">
        </p>
        <p>
            <label for="precio_kero_sugerido">Precio Kero Sugerido -40%:</label><br>
            <input type="number" step="0.01" id="precio_kero_sugerido" name="precio_kero_sugerido" required value="<?php echo $prod['precio_kero_sugerido']; ?>">
        </p>
        <p>
            <label for="descripcion">Descripción:</label><br>
            <textarea id="descripcion" name="descripcion"><?php echo htmlspecialchars($prod['descripcion'] ?? ''); ?></textarea>
        </p>
        <p>
            <label for="foto_url">Foto:</label><br>
            <input type="text" id="foto_url" name="foto_url" value="<?php echo htmlspecialchars($prod['foto_url'] ?? ''); ?>">
        </p>
        
        <p><button type="submit">Actualizar Cambios</button></p>
    </form>

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
    </script>
</body>
</html>
<?php $conexion->close(); ?>