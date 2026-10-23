<?php
session_start();
session_unset(); // Borra las variables de sesión
session_destroy(); // Destruye la sesión en el servidor
header("Location: ../views/login.html"); // Lo manda de vuelta al login limpio
exit();
?>