<?php
session_start();
require_once "../config/conexion.php";

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] != 2) {
    die("❌ Error: Acción no autorizada.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $usuario_id = $_SESSION['usuario_id']; 
    $nombre = trim($_POST['nombre']);
    $precio_real = floatval($_POST['precio_real']);
    $precio_kero_sugerido = floatval($_POST['precio_kero_sugerido']);
    $descripcion = !empty($_POST['descripcion']) ? trim($_POST['descripcion']) : null;
    $foto_url = null; // Por defecto arrancamos en null

    // Obtener el id del comercio de la tabla comercios
    $buscar_comercio = $conexion->prepare("SELECT id FROM comercios WHERE usuario_id = ?");
    $buscar_comercio->bind_param("i", $usuario_id);
    $buscar_comercio->execute();
    $resultado = $buscar_comercio->get_result();
    $fila_comercio = $resultado->fetch_assoc();
    $comercio_id = $fila_comercio['id'];
    $buscar_comercio->close();

    // 📸 PROCESAMIENTO DE LA FOTO DESDE LA GALERÍA O CÁMARA
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $nombre_tmp = $_FILES['foto']['tmp_name'];
        $nombre_original = basename($_FILES['foto']['name']);
        $ext = strtolower(pathinfo($nombre_original, PATHINFO_EXTENSION));

        // Validamos que sea una extensión de imagen válida
        $extensiones_permitidas = ['jpg', 'jpeg', 'png', 'webp'];
        
        if (in_array($ext, $extensiones_permitidas)) {
            // Nombre único: fecha/hora + hash para evitar nombres duplicados
            $nombre_unico = time() . "_" . uniqid() . "." . $ext;
            
            // Subimos un nivel para llegar a la carpeta /uploads
            $directorio_destino = "../uploads/";

            if (!is_dir($directorio_destino)) {
                mkdir($directorio_destino, 0777, true);
            }

            if (move_uploaded_file($nombre_tmp, $directorio_destino . $nombre_unico)) {
                // Esta es la ruta que se guardará en la columna foto_url de MySQL
                $foto_url = "uploads/" . $nombre_unico;
            }
        }
    }

    // 🔄 SI RECIBIMOS UN ID, ES UNA ACTUALIZACIÓN (UPDATE)
    if (isset($_POST['producto_id']) && !empty($_POST['producto_id'])) {
        $producto_id = intval($_POST['producto_id']);

        // Si no subieron una foto nueva durante la edición, conservamos la que ya tenía
        if (empty($foto_url) && isset($_POST['foto_actual'])) {
            $foto_url = $_POST['foto_actual'];
        }

        $actualizar = $conexion->prepare("UPDATE productos_base SET nombre = ?, precio_real = ?, precio_kero_sugerido = ?, descripcion = ?, foto_url = ? WHERE id = ? AND comercio_id = ?");
        $actualizar->bind_param("sddssii", $nombre, $precio_real, $precio_kero_sugerido, $descripcion, $foto_url, $producto_id, $comercio_id);

        if ($actualizar->execute()) {
            header("Location: ../views/panel_comercio.php?mensaje=actualizado");
            exit();
        } else {
            echo "❌ Error al actualizar en productos_base: " . $conexion->error;
        }
        $actualizar->close();

    } else {
        // 📋 SI NO VIENE ID, ES UN REGISTRO NUEVO (INSERT)
        $insertar = $conexion->prepare("INSERT INTO productos_base (comercio_id, nombre, precio_real, precio_kero_sugerido, descripcion, foto_url) VALUES (?, ?, ?, ?, ?, ?)");
        $insertar->bind_param("isddss", $comercio_id, $nombre, $precio_real, $precio_kero_sugerido, $descripcion, $foto_url);

        if ($insertar->execute()) {
            header("Location: ../views/panel_comercio.php?mensaje=guardado");
            exit();
        } else {
            echo "❌ Error al guardar en productos_base: " . $conexion->error;
        }
        $insertar->close();
    }
}
$conexion->close();
?>