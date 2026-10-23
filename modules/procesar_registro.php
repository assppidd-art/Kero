<?php
// Ocultar errores directos de PHP para no romper la respuesta JSON
error_reporting(0);
ini_set('display_errors', 0);

// Indicamos al navegador que responderemos en formato JSON
header('Content-Type: application/json; charset=utf-8');

// Cargar la conexión
require_once "../config/conexion.php";

// Validar que la conexión a MySQL exista y no tenga errores
if (!isset($conexion) || $conexion->connect_error) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Error de conexión a la Base de Datos: ' . ($conexion->connect_error ?? 'No se pudo establecer la conexión.')
    ]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    $nombre_crudo   = trim($_POST['nombre'] ?? '');
    $correo         = trim($_POST['correo'] ?? '');
    $password_plana = $_POST['password'] ?? '';
    $telefono_crudo = trim($_POST['telefono'] ?? '');
    
    // 1. Validar campos obligatorios
    if (empty($nombre_crudo) || empty($correo) || empty($password_plana)) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Por favor, llena todos los campos requeridos.'
        ]);
        exit;
    }

    // 2. Validación de contraseña corta
    if (strlen($password_plana) < 6) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'La contraseña debe tener al menos 6 caracteres.'
        ]);
        exit;
    }

    $password_hash  = password_hash($password_plana, PASSWORD_DEFAULT);
    $rol_id         = 1; // Rol 1 = Comelón (Cliente)

    // Formatear teléfono con +58
    $telefono_venezuela = !empty($telefono_crudo) ? "+58" . substr($telefono_crudo, 1) : "";
    $nombre_completo    = !empty($telefono_venezuela) ? $nombre_crudo . " (" . $telefono_venezuela . ")" : $nombre_crudo;

    // 3. Verificar si el correo ya existe
    $verificar_email = $conexion->prepare("SELECT id FROM usuarios WHERE email = ?");
    if (!$verificar_email) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Error en la Base de Datos: ' . $conexion->error
        ]);
        exit;
    }

    $verificar_email->bind_param("s", $correo);
    $verificar_email->execute();
    $verificar_email->store_result();

    if ($verificar_email->num_rows > 0) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Este correo electrónico ya está registrado. Intenta con otro o inicia sesión.'
        ]);
        $verificar_email->close();
        $conexion->close();
        exit;
    }
    $verificar_email->close();

    // 4. Insertar el nuevo Comelón (Corregido con los 5 parámetros exactos)
    $verificado = 0;
    $ins_usuario = $conexion->prepare("INSERT INTO usuarios (nombre, email, password_hash, rol_id, verificado) VALUES (?, ?, ?, ?, ?)");
    
    if (!$ins_usuario) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Error al preparar la inserción: ' . $conexion->error
        ]);
        exit;
    }

    $ins_usuario->bind_param("sssii", $nombre_completo, $correo, $password_hash, $rol_id, $verificado);

    if ($ins_usuario->execute()) {
        echo json_encode([
            'status'  => 'success',
            'message' => '¡Registro exitoso! Bienvenido a Kero, ' . $nombre_crudo . '. Redirigiendo...'
        ]);
    } else {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Error al registrar al comelón: ' . $ins_usuario->error
        ]);
    }

    $ins_usuario->close();
} else {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Método de solicitud no permitido.'
    ]);
}

$conexion->close();
?>