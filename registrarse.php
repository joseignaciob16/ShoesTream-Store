<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Cuenta</title>
    <link rel="stylesheet" href="Styles/registro.css">
</head>

<body>
    <div class="register-container">
        <img src="Imagenes/logo.png" alt="Logo" class="logo">
        <h2>Crear Cuenta</h2>
        <form action="register_process.php" method="post">
            <input type="text" name="identification" placeholder="Identificación" required>
            <input type="text" name="name1" placeholder="Primer Nombre" required>
            <input type="text" name="name2" placeholder="Segundo Nombre">
            <input type="text" name="name3" placeholder="Primer Apellido" required>
            <input type="text" name="name4" placeholder="Segundo Apellido" required>
            <input type="email" name="email" placeholder="Correo Electrónico" required>
            <input type="password" name="password" placeholder="Contraseña" required>
            <input type="text" name="address" placeholder="Dirección" required>
            <button type="submit">Registrar</button>
        </form>
        <div class="iniciar-link">
            ¿Ya tienes cuenta? <a href="login.php">Iniciar Sesión</a>
        </div>
    </div>
</body>

</html>