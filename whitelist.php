<?php
// Iniciar la sesión
session_start();

// Asegúrate de que el usuario está logueado antes de proceder
if (!isset($_SESSION['usuario_id'])) {
    // Si el usuario no está logueado, redirigir a la página de login
    header('Location: login.php');
    exit;
}

// Incluir la conexión a la base de datos
include 'db.php';

// Obtener el ID del usuario de la sesión
$id_usuario = $_SESSION['usuario_id'];

// Preparar la consulta SQL para obtener los productos de la lista de deseos
$sql = "SELECT p.* FROM productos p 
        JOIN productolistadeseos pd ON p.id_producto = pd.id_producto
        JOIN listadeseos l ON l.id_listadeseos = pd.id_listadeseos
        WHERE l.id_usuario = ?";

// Preparar, ejecutar la consulta y obtener los resultados
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $id_usuario);
    if ($stmt->execute()) {
        $resultado = $stmt->get_result();
    } else {
        // Manejo de error de ejecución
        error_log("Error al ejecutar la consulta: " . $stmt->error);
        // Puedes optar por mostrar un mensaje de error al usuario también
    }
} else {
    // Manejo de error de preparación
    error_log("Error al preparar la consulta: " . $conn->error);
    // Puedes optar por mostrar un mensaje de error al usuario también
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Deseos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="Styles/whitlist.css">
    <script src="scripts.js"></script>
</head>

<body>
<header>
        <div class="header-left">
            <a href="tienda.php">
                <img src="Imagenes/logo.png" alt="Logo de ShoeStream" class="logo">        
            </a>
            <span class="site-name">ShoeStream Store</span>
        </div>

        <div class="header-right">
            <a href="compras.php">
                <img src="Imagenes/pedidos.png" alt="historial" class="icon">
            </a>
            <a href="carrito.php">
                <img src="Imagenes/carrito.png" alt="carrito" class="icon">
            </a>
            <a href="logout.php">
                <img src="Imagenes/salir.png" alt="Salir" class="icon">
            </a>
        </div>
    </header>
    <main>  
        <h1>Lista de deseos</h1>
        <div class="row">
            <?php if (isset($resultado)): ?>
                <?php while ($fila = $resultado->fetch_assoc()): ?>
                    <div class="col-md-4">
                        <div class="product-item">
                            <img src="Imagenes/<?php echo htmlspecialchars($fila['imagen']); ?>" alt="<?php echo htmlspecialchars($fila['nombre']); ?>" class="product-image">
                            <h2 class="product-name"><?php echo htmlspecialchars($fila['nombre']); ?></h2>
                            <p class="product-description"><?php echo htmlspecialchars($fila['descripcion']); ?></p>
                            <p class="product-price">Precio: $<?php echo htmlspecialchars($fila['precio']); ?></p>
                            <div class="product-actions">
                            <img src="Imagenes/trash.png" alt="Eliminar" onclick="eliminarDeListaDeseos(<?php echo $fila['id_producto']; ?>)" style="cursor: pointer;" />
                            <img src="Imagenes/anadir-al-carrito.png" alt="Agregar al Carrito" onclick="agregarALcarrito(<?php echo $fila['id_producto']; ?>)" style="cursor: pointer;" />
                      </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No tienes productos en tu lista de deseos.</p>
            <?php endif; ?>
        </div>
    </main>

    <footer class="mt-5 text-center">
    <p><small>©2023. All rights reserved.</small></p>
    </footer>
</body>
</html>
