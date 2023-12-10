<?php
session_start(); // Iniciar la sesión

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    // Si el usuario no está logueado, redirigir a la página de login
    header('Location: login.php');
    exit;
}

include 'db.php'; 
$id_usuario = $_SESSION['usuario_id'];

// Obtener las compras del usuario
$sqlCompras = "SELECT id_compra, fecha_compra
               FROM compra
               WHERE id_usuario = ?
               ORDER BY fecha_compra DESC";
$stmtCompras = $conn->prepare($sqlCompras);
$stmtCompras->bind_param("i", $id_usuario);
$stmtCompras->execute();
$resultadoCompras = $stmtCompras->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Compras</title>
    <link rel="stylesheet" href="Styles/compras.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
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
            <a href="whitelist.php">
                <img src="Imagenes/whitlist.png" alt="Wishlist" class="icon">
            </a>
            <a href="carrito.php">
                <img src="Imagenes/carrito.png" alt="Carrito" class="icon">
            </a>
            <a href="logout.php">
                <img src="Imagenes/salir.png" alt="Salir" class="icon">
            </a>
        </div>
    </header>
    <main>
        <h1>Historial de Compras</h1>
        <?php if ($resultadoCompras->num_rows > 0): ?>
            <div class="compras-container">
                <?php while ($compra = $resultadoCompras->fetch_assoc()): ?>
                    <div class="compra">
                        <h3>ID compra: <?php echo htmlspecialchars($compra['id_compra']); ?></h3>
                        <p>Fecha: <?php echo htmlspecialchars($compra['fecha_compra']); ?></p>
                        <div class="detalles-compra">
                            <?php
                            // Obtener los detalles de cada compra
                            $sqlDetallesCompra = "SELECT dc.id_producto, p.nombre, dc.cantidad, p.precio
                                                  FROM detallecompra dc
                                                  INNER JOIN productos p ON dc.id_producto = p.id_producto
                                                  WHERE dc.id_compra = ?";
                            $stmtDetallesCompra = $conn->prepare($sqlDetallesCompra);
                            $stmtDetallesCompra->bind_param("i", $compra['id_compra']);
                            $stmtDetallesCompra->execute();
                            $resultadoDetallesCompra = $stmtDetallesCompra->get_result();
                            
                            // Mostrar detalles de los productos comprados
                            while ($detalle = $resultadoDetallesCompra->fetch_assoc()) {
                                echo "<p>Producto: " . htmlspecialchars($detalle['nombre']) .
                                     " - Cantidad: " . htmlspecialchars($detalle['cantidad']) .
                                     " - Precio Unitario: $" . htmlspecialchars($detalle['precio']) . "</p>";
                            }
                            $stmtDetallesCompra->close();
                            ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p>No has realizado ninguna compra.</p>
        <?php endif; ?>

        <br>
        <div class="text-center">
            <a href="tienda.php" class="btn btn-primary">Volver a el Comercio</a>
        </div>
    </main>
    <footer class="mt-5 text-center">
        <p><small>©2023. All rights reserved.</small></p>
    </footer>
</body>
</html>