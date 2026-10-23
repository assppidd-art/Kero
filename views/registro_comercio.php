<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kero - Registro de Comercio</title>
    
    <!-- Tipografía Fredoka -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="../img/logo-icono.png">
    
    <!-- Hoja de Estilos del Comercio -->
    <link rel="stylesheet" href="../css/comercio.css">
</head>
<body>

    <header class="cabecera-registro">
        <h2>Registro de Nuevo Comercio</h2>
        <p>Crea tu cuenta comercial para reducir mermas y multiplicar tus ingresos hoy mismo.</p>
    </header>

    <main class="contenido-registro">
        <form action="../modules/procesar_registro_comercio.php" method="POST" class="form-registro-comercio">
            
            <!-- DATOS DE USUARIO -->
            <fieldset class="grupo-formulario">
                <legend><h3>1. Datos de la Cuenta de Acceso</h3></legend>
                
                <p class="campo-form">
                    <label for="nombre_usuario">Nombre del Administrador / Dueño:</label>
                    <input type="text" id="nombre_usuario" name="nombre_usuario" required placeholder="Ej. Juan Pérez" class="input-texto">
                </p>
                <p class="campo-form">
                    <label for="correo">Correo Electrónico del Local:</label>
                    <input type="email" id="correo" name="correo" required placeholder="Ej. contacto@tupanaderia.com" class="input-email">
                </p>
                <p class="campo-form">
                    <label for="telefono">Número de Celular (WhatsApp / Pago Móvil):</label>
                    <input type="tel" id="telefono" name="telefono" required 
                           pattern="^(0414|0424|0412|0416|0426)[0-9]{7}$" 
                           placeholder="Ej. 04141234567" class="input-tel">
                    <small class="texto-ayuda">Ingresa 11 dígitos (0414, 0424, 0412, etc.)</small>
                </p>
                <p class="campo-form">
                    <label for="password">Contraseña:</label>
                    <input type="password" id="password" name="password" required placeholder="Mínimo 6 caracteres" class="input-password">
                </p>
            </fieldset>

            <!-- DATOS DEL ESTABLECIMIENTO -->
            <fieldset class="grupo-formulario">
                <legend><h3>2. Datos del Establecimiento</h3></legend>
                
                <p class="campo-form">
                    <label for="nombre_local">Nombre Comercial del Local:</label>
                    <input type="text" id="nombre_local" name="nombre_local" required placeholder="Ej. Panadería Mansión de la Dona" class="input-texto">
                </p>
                <p class="campo-form">
                    <label for="categoria">Categoría del Comercio:</label>
                    <select id="categoria" name="categoria" required class="select-categoria">
                        <option value="">-- Selecciona una categoría --</option>
                        <option value="Panadería / Pastelería">Panadería / Pastelería</option>
                        <option value="Restaurante / Almuerzos">Restaurante / Almuerzos</option>
                        <option value="Frutería / Verdulería">Frutería / Verdulería</option>
                        <option value="Cafetería">Cafetería</option>
                        <option value="Mini Market / Bodega">Mini Market / Bodega</option>
                    </select>
                </p>
                <p class="campo-form">
                    <label for="zona_caracas">Zona de Caracas para retiro:</label>
                    <select id="zona_caracas" name="zona_caracas" required class="select-zona">
                        <option value="">-- Selecciona la zona de retiro --</option>
                        <option value="Chacao">Chacao</option>
                        <option value="Altamira">Altamira</option>
                        <option value="Los Dos Caminos">Los Dos Caminos</option>
                        <option value="Las Mercedes">Las Mercedes</option>
                        <option value="La Candelaria">La Candelaria</option>
                        <option value="El Hatillo">El Hatillo</option>
                        <option value="Plaza Venezuela">Plaza Venezuela</option>
                    </select>
                </p>
                <p class="campo-form">
                    <label for="hora_limite_retiro">Hora Límite para retirar (Hora de cierre):</label>
                    <input type="time" id="hora_limite_retiro" name="hora_limite_retiro" required class="input-time">
                </p>
                <p class="campo-form">
                    <label for="datos_pago_movil">Datos para Pago Móvil (Cobro directo):</label>
                    <textarea id="datos_pago_movil" name="datos_pago_movil" required rows="2" placeholder="Ej. Banesco - RIF J-12345678-9 - Tel: 0414-1234567" class="textarea-pago"></textarea>
                </p>
            </fieldset>

            <!-- VERIFICACIÓN LEGAL -->
            <fieldset class="grupo-formulario">
                <legend><h3>3. Verificación Legal (Opcional)</h3></legend>
                
                <p class="campo-form">
                    <label for="rif">RIF (Opcional para emprendedores desde casa):</label>
                    <input type="text" id="rif" name="rif" placeholder="Ej. J-123456789" class="input-texto">
                </p>
                <p class="campo-form">
                    <label for="permiso_sanitario">Número de Permiso Sanitario / Certificado:</label>
                    <input type="text" id="permiso_sanitario" name="permiso_sanitario" placeholder="Ej. SACS-2026-XYZ" class="input-texto">
                    
                    <div class="caja-informativa-verificacion">
                        <small class="texto-ayuda">
                            * Si introduces un certificado válido quedas como local <strong class="resaltado-verificado">✓ Verificado</strong>.<br>
                            * Si lo dejas en blanco, podrás vender igual como <strong class="resaltado-independiente">Sin Verificación</strong>.
                        </small>
                    </div>
                </p>
            </fieldset>

            <p class="campo-boton">
                <button type="submit" class="btn-registro-comercio">Registrar Comercio y Crear Cuenta</button>
            </p>
        </form>
    </main>

    <footer class="pie-registro">
        <p><a href="login.html" class="enlace-login">¿Ya tienes cuenta? Inicia sesión aquí</a></p>
    </footer>

    <!-- VENTANA MODAL DE NOTIFICACIÓN (Oculta hasta que hay un aviso) -->
  <div id="modal-notificacion" class="modal-overlay">
    <div class="modal-caja">
        <div id="modal-icono" class="modal-icono">⚠️</div>
        <h3 id="modal-titulo">¡Atención!</h3>
        <p id="modal-mensaje">Mensaje...</p>
        <button type="button" id="btn-cerrar-modal" class="btn-modal">Entendido</button>
    </div>
  </div>

  <script src="../js/alerta.js"></script>

</body>
</html>