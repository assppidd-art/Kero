<?php
// Ocultar errores directos para mantener la respuesta JSON limpia
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json; charset=utf-8');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "../config/conexion.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $correo         = trim($_POST['correo'] ?? '');
    $password_plana = $_POST['password'] ?? '';

    // 1. Validar campos vacíos
    if (empty($correo) || empty($password_plana)) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Por favor, llena todos los campos.'
        ]);
        exit;
    }

    // 2. Buscar al usuario en la BD
    $consulta = $conexion->prepare("SELECT id, nombre, password_hash, rol_id FROM usuarios WHERE email = ?");
    
    if (!$consulta) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Error en la consulta a la base de datos.'
        ]);
        exit;
    }

    $consulta->bind_param("s", $correo);
    $consulta->execute();
    $resultado = $consulta->get_result();

    if ($resultado && $resultado->num_rows === 1) {
        $usuario = $resultado->fetch_assoc();

        // 3. Verificar contraseña
        if (password_verify($password_plana, $usuario['password_hash'])) {
            
            // Asignar variables de sesión requeridas por los paneles
            $_SESSION['usuario_id']     = $usuario['id'];
            $_SESSION['usuario_nombre'] = $usuario['nombre'];
            $_SESSION['usuario_rol']    = $usuario['rol_id'];

            // RUTAS CORREGIDAS: Apuntan hacia la carpeta ../views/
            $redirigir = ($usuario['rol_id'] == 2) 
                ? "../views/panel_comercio.php" 
                : "../views/panel_comelon.php";

            echo json_encode([
                'status'   => 'success',
                'message'  => '¡Bienvenido de nuevo, ' . htmlspecialchars($usuario['nombre']) . '!',
                'redirect' => $redirigir
            ]);
            
            $consulta->close();
            $conexion->close();
            exit;
        } else {
            echo json_encode([
                'status'  => 'error',
                'message' => 'La contraseña es incorrecta. Intenta de nuevo.'
            ]);
            
            $consulta->close();
            $conexion->close();
            exit;
        }
    } else {
        echo json_encode([
            'status'  => 'error',
            'message' => 'No encontramos ninguna cuenta asociada a este correo.'
        ]);
        
        $consulta->close();
        $conexion->close();
        exit;
    }

} else {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Método de solicitud no permitido.'
    ]);
    exit;
}
?>