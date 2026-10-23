<?php
// Indicamos al navegador que responderemos en formato JSON
header('Content-Type: application/json; charset=utf-8');

require_once "../config/conexion.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. Capturar datos de la cuenta
    $nombre_usuario = trim($_POST['nombre_usuario'] ?? '');
    $correo         = trim($_POST['correo'] ?? '');
    $password_plana = $_POST['password'] ?? '';
    $password_hash  = password_hash($password_plana, PASSWORD_DEFAULT);
    $rol_id         = 2; // Comercio

    // 2. Capturar datos del establecimiento
    $nombre_local       = trim($_POST['nombre_local'] ?? '');
    $categoria          = $_POST['categoria'] ?? '';
    $zona_caracas       = $_POST['zona_caracas'] ?? '';
    $hora_limite_retiro = $_POST['hora_limite_retiro'] ?? '';
    
    // Capturar y formatear teléfono (+58)
    $telefono_crudo     = trim($_POST['telefono'] ?? '');
    $telefono_venezuela = "+58" . substr($telefono_crudo, 1);

    // Unificar teléfono en datos de pago móvil
    $pago_movil_usuario = trim($_POST['datos_pago_movil'] ?? '');
    $datos_pago_movil   = "Teléfono de Contacto: " . $telefono_venezuela . " | " . $pago_movil_usuario;
    
    // Campos de verificación legal
    $rif               = !empty($_POST['rif']) ? trim($_POST['rif']) : null;
    $permiso_sanitario = !empty($_POST['permiso_sanitario']) ? trim($_POST['permiso_sanitario']) : null;
    $verificado        = (!empty($permiso_sanitario)) ? 1 : 0;

    // 1. Validación de contraseña corta
    if (strlen($password_plana) < 6) {
        echo json_encode([
            'status' => 'error',
            'message' => 'La contraseña debe tener al menos 6 caracteres.'
        ]);
        exit;
    }

    // 2. Verificar duplicado de correo
    $verificar_email = $conexion->prepare("SELECT id FROM usuarios WHERE email = ?"); 
    $verificar_email->bind_param("s", $correo);
    $verificar_email->execute();
    $verificar_email->store_result();

    if ($verificar_email->num_rows > 0) {
        echo json_encode([
            'status' => 'error',
            'message' => 'El correo electrónico comercial ya está registrado. Intenta con otro correo.'
        ]);
        $verificar_email->close();
        $conexion->close();
        exit;
    }
    $verificar_email->close();

    // 3. Transacción SQL para guardar usuario y comercio
    $conexion->begin_transaction();

    try {
        // PASO A: Insertar en la tabla usuarios
        $ins_usuario = $conexion->prepare("INSERT INTO usuarios (nombre, email, password_hash, rol_id, verificado) VALUES (?, ?, ?, ?, ?)");
        $ins_usuario->bind_param("sssii", $nombre_usuario, $correo, $password_hash, $rol_id, $verificado);
        $ins_usuario->execute();
        
        $nuevo_usuario_id = $conexion->insert_id;
        $ins_usuario->close();

        // PASO B: Insertar en la tabla comercios
        $ins_comercio = $conexion->prepare("
            INSERT INTO comercios (usuario_id, nombre_local, categoria, bcv_status, hora_limite_retiro, zona_caracas, datos_pago_movil) 
            VALUES (?, ?, ?, TRUE, ?, ?, ?)
        ");
        $ins_comercio->bind_param("isssss", $nuevo_usuario_id, $nombre_local, $categoria, $hora_limite_retiro, $zona_caracas, $datos_pago_movil);
        $ins_comercio->execute();
        $ins_comercio->close();

        $conexion->commit();

        $mensaje_badge = ($verificado == 1) 
            ? "✔️ Local verificado automáticamente." 
            : "⚠️ Registrado como Emprendimiento sin verificación.";

        echo json_encode([
            'status' => 'success',
            'message' => '¡Comercio "' . $nombre_local . '" registrado con éxito! ' . $mensaje_badge
        ]);

    } catch (Exception $e) {
        $conexion->rollback();
        echo json_encode([
            'status' => 'error',
            'message' => 'Error crítico durante el registro: ' . $e->getMessage()
        ]);
    }
}

$conexion->close();
?>