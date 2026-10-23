<?php
session_start();
require_once "../config/conexion.php";

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] != 2) {
    die("❌ Error: Acción no autorizada.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $usuario_id = $_SESSION['usuario_id'];
    $producto_base_id = intval($_POST['producto_base_id']);
    $stock_disponible = intval($_POST['stock_disponible']);
    $precio_hoy = floatval($_POST['precio_hoy']);
    $fecha_publicacion = date('Y-m-d'); 

    // Buscar ID del comercio
    $buscar_comercio = $conexion->prepare("SELECT id FROM comercios WHERE usuario_id = ?");
    $buscar_comercio->bind_param("i", $usuario_id);
    $buscar_comercio->execute();
    $resultado = $buscar_comercio->get_result();
    $fila = $resultado->fetch_assoc();
    $comercio_id = $fila['id'];
    $buscar_comercio->close();

    // 🧠 REGLA DE NEGOCIO CONTROL DE DUPLICADOS:
    // Revisamos si este mismo producto base ya fue publicado hoy por esta empresa y sigue activo
    $verificar_duplicado = $conexion->prepare("SELECT id, stock_disponible FROM publicaciones_excedentes WHERE producto_base_id = ? AND comercio_id = ? AND fecha_publicacion = ? AND activo = 1");
    $verificar_duplicado->bind_param("iis", $producto_base_id, $comercio_id, $fecha_publicacion);
    $verificar_duplicado->execute();
    $res_duplicado = $verificar_duplicado->get_result();

    if ($res_duplicado->num_rows > 0) {
        // En vez de duplicar el producto en la cartelera, le damos una mejor solución al usuario:
        // Le advertimos que ya existe y bloqueamos la inserción repetida.
        die("<h2>⚠️ Error de Publicación</h2><p>Ya tienes una oferta activa el día de hoy para este mismo alimento. Si te quedaron más unidades, desactiva la oferta anterior desde tu tabla de gestión antes de publicar un lote nuevo con otro precio.</p><a href='../views/panel_comercio.php'>Regresar al panel</a>");
    }
    $verificar_duplicado->close();

    // Si pasa el filtro, insertamos en publicaciones_excedentes con activo = TRUE (1)
    $insertar = $conexion->prepare("INSERT INTO publicaciones_excedentes (producto_base_id, comercio_id, stock_disponible, precio_hoy, fecha_publicacion, activo) VALUES (?, ?, ?, ?, ?, 1)");
    $insertar->bind_param("iiids", $producto_base_id, $comercio_id, $stock_disponible, $precio_hoy, $fecha_publicacion);

    if ($insertar->execute()) {
        echo "<h2>🎉 ¡Lote dinámico publicado con éxito!</h2>";
        echo "<p>El lote ya se encuentra visible en la cartelera de los Comelones.</p>";
        echo "<a href='../views/panel_comercio.php'>Volver al Panel</a>";
    } else {
        echo "❌ Error al insertar la publicación: " . $conexion->error;
    }
    $insertar->close();
}
$conexion->close();
?>