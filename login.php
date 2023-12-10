<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="Styles/login.css">
</head>
<body>
    <div class="login-container">
        
        <img src="Imagenes/logo.png" alt="Logo" class="logo">
        <h2>Iniciar Sesión</h2>
        <?php
        // Inicia sesión PHP para usar las variables de sesión
        session_start();
        // Verifica si hay un mensaje de error y lo muestra
        if (isset($_SESSION['login_error'])) {
            echo '<p class="error">' . $_SESSION['login_error'] . '</p>';
            // Luego borra el mensaje de error de la sesión
            unset($_SESSION['login_error']);
        }
        ?>
        <form action="authenticate.php" method="post">
            <input type="email" name="email" placeholder="Correo Electrónico" required>
            <input type="password" name="password" placeholder="Contraseña" required>
            <button type="submit">Iniciar Sesión</button>
        </form>
        <div class="register-link">
            ¿No tienes cuenta? <a href="registrarse.php">Crear cuenta</a>
        </div>
    </div>
</body>
</html>
