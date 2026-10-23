<?php
$host       = "localhost"; 
$usuario    = "root";         
$password   = "";            
$base_datos = "kero1_db"; 


$conexion = new mysqli($host, $usuario, $password, $base_datos);


if ($conexion->connect_error) {
    
    if (!headers_sent()) {
        header('Content-Type: application/json; charset=utf-8');
    }
    echo json_encode([
        'status'  => 'error',
        'message' => 'Error de conexión a la Base de Datos (' . $conexion->connect_errno . '): ' . $conexion->connect_error
    ]);
    exit();
}

$conexion->set_charset("utf8mb4");
?>