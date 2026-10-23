<?php
session_start();
require_once "../config/conexion.php";

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] != 2) {
    die("❌ Acción no autorizada.");
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$tabla = isset($_GET['tabla']) ? $_GET['tabla'] : '';
$accion = isset($_GET['accion']) ? $_GET['accion'] : '';

// --- 1. GESTIÓN DE EXCEDENTES DIARIOS ---
if ($tabla === 'excedente') {
    if ($accion === 'desactivar') {
        $stmt = $conexion->prepare("UPDATE publicaciones_excedentes SET activo = 0 WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    } elseif ($accion === 'activar') {
        $stmt = $conexion->prepare("UPDATE publicaciones_excedentes SET activo = 1 WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    } elseif ($accion === 'eliminar') {
        // Borrado físico directo porque es una publicación del día
        $stmt = $conexion->prepare("DELETE FROM publicaciones_excedentes WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: ../views/panel_comercio.php");
    exit();
}

// --- 2. GESTIÓN DE PRODUCTOS BASE (CATÁLOGO) ---
if ($tabla === 'base') {
    if ($accion === 'borrar_logico') {
        // CUMPLIENDO CON TU COLUMNA DELETED_AT: Marcamos el tiempo actual en vez de borrar la fila
        $fecha_actual = date('Y-m-d H:i:s');
        $stmt = $conexion->prepare("UPDATE productos_base SET deleted_at = ? WHERE id = ?");
        $stmt->bind_param("si", $fecha_actual, $id);
        $stmt->execute();
        $stmt->close();

        header("Location: ../views/panel_comercio.php");
        exit();
    }
}

$conexion->close();
?>