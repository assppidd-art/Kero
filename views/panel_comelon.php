<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] != 1) {
    header("Location: login.html");
    exit();
}
require_once "../config/conexion.php";

// Limpia el número de teléfono o contenido entre paréntesis del nombre
$nombre_bruto = $_SESSION['usuario_nombre'] ?? 'Comelón';
$nombre_limpio = trim(preg_replace('/\s*\(\+?\d+\)/', '', $nombre_bruto));

// Consulta avanzada conectando tu esquema completo
$consulta = $conexion->query("
    SELECT pe.*, 
           pb.nombre AS nombre_alimento, pb.precio_real, pb.foto_url, pb.descripcion,
           c.nombre_local, c.zona_caracas, c.hora_limite_retiro, c.categoria,
           u.verificado 
    FROM publicaciones_excedentes pe 
    JOIN productos_base pb ON pe.producto_base_id = pb.id
    JOIN comercios c ON pe.comercio_id = c.id 
    JOIN usuarios u ON c.usuario_id = u.id
    WHERE pe.stock_disponible > 0 AND pe.activo = 1 AND pb.deleted_at IS NULL
    ORDER BY pe.id DESC
");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Kero - Epa Comelón</title>
    <!-- Aquí conectarás tu CSS cuando lo crees, ej: -->
    <!-- <link rel="stylesheet" href="../css/comelon.css"> -->
</head>
<body>

    <header class="cabecera-panel">
        <h2>Rescate de Alimentos</h2>
        <p>¡Hola, <strong class="nombre-usuario"><?php echo htmlspecialchars($nombre_limpio); ?></strong>! Revisa lo que puedes salvar hoy en Caracas.</p>
    </header>
    
    <hr>
    
    <h3>Ofertas dinámicas disponibles justo ahora:</h3>

    <!-- Contenedor principal para la cuadrícula de ofertas -->
    <div class="contenedor-ofertas">
        <?php
        if ($consulta && $consulta->num_rows > 0) {
            while ($excedente = $consulta->fetch_assoc()) {
                
                // Definir clase según verificación
                $clase_verificacion = ($excedente['verificado'] == 1) ? "verificado" : "independiente";
                $texto_verificacion = ($excedente['verificado'] == 1) ? "Local Verificado" : "Emprendimiento Independiente";
                
                // LÓGICA DE RUTA DE FOTO
                if (!empty($excedente['foto_url'])) {
                    if (strpos($excedente['foto_url'], 'http') === 0) {
                        $foto = $excedente['foto_url'];
                    } else {
                        $foto = "../" . $excedente['foto_url'];
                    }
                } else {
                    $foto = 'https://via.placeholder.com/150?text=Kero+Naranja';
                }
                
                // Estructura de la tarjeta SIN CSS en línea
                echo "<article class='tarjeta-oferta'>";
                    echo "<img src='" . htmlspecialchars($foto) . "' alt='Foto de " . htmlspecialchars($excedente['nombre_alimento']) . "' class='foto-producto'>";
                    
                    echo "<div class='info-producto'>";
                        echo "<h4>" . htmlspecialchars($excedente['nombre_alimento']) . "</h4>"; 
                        echo "<p class='descripcion-producto'>" . htmlspecialchars($excedente['descripcion'] ?? '') . "</p>";
                        
                        echo "<p class='precios'>";
                            echo "<span class='precio-real'>Precio Real: <del>$" . number_format($excedente['precio_real'], 2) . "</del></span><br>";
                            echo "<span class='precio-rescate'>Precio de Rescate: $" . number_format($excedente['precio_hoy'], 2) . "</span>";
                        echo "</p>";
                        
                        echo "<p class='stock'><strong>Disponibles:</strong> " . $excedente['stock_disponible'] . " packs</p>";
                    echo "</div>"; // Fin info-producto
                    
                    echo "<div class='info-comercio'>";
                        echo "<p class='nombre-local'><strong>" . htmlspecialchars($excedente['nombre_local']) . "</strong> (" . htmlspecialchars($excedente['categoria']) . ")</p>";
                        echo "<p class='zona-local'><strong>Zona:</strong> " . htmlspecialchars($excedente['zona_caracas']) . "</p>";
                        echo "<p class='hora-retiro'><strong>Retirar antes de:</strong> " . htmlspecialchars($excedente['hora_limite_retiro']) . "</p>";
                        echo "<p class='badge-verificacion " . $clase_verificacion . "'><small>" . $texto_verificacion . "</small></p>";
                    echo "</div>"; // Fin info-comercio
                    
                    echo "<button type='button' class='btn-reservar'>Reservar este Pack</button>";
                echo "</article>"; // Fin tarjeta-oferta
            }
        } else {
            echo "<p class='sin-ofertas'>No hay ofertas dinámicas publicadas para esta zona hoy. ¡Vuelve más tarde!</p>";
        }
        
        if ($consulta) { $consulta->close(); }
        $conexion->close();
        ?>
    </div> <!-- Fin contenedor-ofertas -->

    <hr>
    
    <footer class="pie-panel">
        <p><a href="../modules/cerrar_sesion.php" class="link-cerrar-sesion">Cerrar Sesión segura</a></p>
    </footer>

</body>
</html>