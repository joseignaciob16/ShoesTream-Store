<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require 'db.php'; 

    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Compara la contraseña en texto plano
        if ($password === $user['password']) {
            // Autenticación exitosa, establecer las variables de sesión
            $_SESSION['usuario_id'] = $user['id'];  // Asegúrate de que 'id' es el campo correcto de tu base de datos
            $_SESSION['user_email'] = $email;
        
            // Redireccionar al usuario a la página que prefieras después del inicio de sesión exitoso
            header("Location: tienda.php");
            exit();
        } else {
            // Contraseña incorrecta, redirigir al usuario al formulario de inicio de sesión con un mensaje de error
            $_SESSION['login_error'] = 'La contraseña es incorrecta.';
            header("Location: login.php");
            exit();
        }
    } else {
        // No se encontró el usuario
        $_SESSION['login_error'] = 'No existe una cuenta con ese correo electrónico.';
        header("Location: login.php");
        exit();
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: login.php");
    exit();
}
?>
