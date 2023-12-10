<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require 'db.php'; // Asegúrate de que este archivo contenga tus datos de conexión a la base de datos

    // Obtener los datos del formulario
    $identification = $_POST['identification'];
    $name1 = $_POST['name1'];
    $name2 = $_POST['name2'];
    $name3 = $_POST['name3'];
    $name4 = $_POST['name4'];
    $email = $_POST['email'];
    $password = $_POST['password']; // Considera hashear la contraseña con password_hash()
    $address = $_POST['address'];

    // Preparar y ejecutar la consulta para insertar el nuevo usuario
    $stmt = $conn->prepare("INSERT INTO usuarios (identificacion, nombre1, nombre2, apellido1, apellido2, email, password, direccion) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssss", $identification, $name1, $name2, $name3, $name4, $email, $password, $address); // Asegúrate de que los "s" coincidan con los tipos de tus columnas
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        // Redirigir al usuario a la página de inicio de sesión o a una página de éxito
        header("Location: login.php");
        exit();
    } else {
        // Manejar el error, por ejemplo, un usuario ya existente
        echo "Error: " . $stmt->error;
        // En un caso real, podrías redirigir al usuario de vuelta al formulario de registro con un mensaje de error
    }

    $stmt->close();
    $conn->close();
} else {
    // Redirigir al formulario de registro si el script se accede directamente
    header("Location: registrarse.php");
    exit();
}
?>
