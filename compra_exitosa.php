<?php
session_start();
include 'db.php';

if (!isset($_GET['id_compra']) || !isset($_SESSION['usuario_id'])) {
    header('Location: carrito.php');
    exit;
}

$id_compra = $_GET['id_compra'];
$id_usuario = $_SESSION['usuario_id'];

// Obtener los datos del cliente
$sqlUsuario = "SELECT nombre1, apellido1, identificacion, email, direccion FROM usuarios WHERE id = ?";
$stmtUsuario = $conn->prepare($sqlUsuario);
$stmtUsuario->bind_param("i", $id_usuario);
$stmtUsuario->execute();
$resultadoUsuario = $stmtUsuario->get_result();
$usuario = $resultadoUsuario->fetch_assoc();


// Obtener los detalles de la compra
$sqlDetallesCompra = "SELECT c.fecha_compra, dc.id_producto, p.nombre, dc.cantidad, p.precio
                      FROM compra c
                      INNER JOIN detallecompra dc ON c.id_compra = dc.id_compra
                      INNER JOIN productos p ON dc.id_producto = p.id_producto
                      WHERE c.id_compra = ? AND c.id_usuario = ?";
$stmtDetallesCompra = $conn->prepare($sqlDetallesCompra);
$stmtDetallesCompra->bind_param("ii", $id_compra, $id_usuario);
$stmtDetallesCompra->execute();
$resultadoDetallesCompra = $stmtDetallesCompra->get_result();

// Variable para almacenar la fecha de la compra
$fecha_compra = '';

if ($resultadoDetallesCompra->num_rows > 0) {
    $primerDetalle = $resultadoDetallesCompra->fetch_assoc();
    $fecha_compra = $primerDetalle['fecha_compra'];

    // Mueve el puntero del resultado hacia atrás para poder iterar nuevamente
    $resultadoDetallesCompra->data_seek(0);
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compra Exitosa</title>
    <link rel="stylesheet" href="Styles/compra_exitosa.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>

<body>
    <div class="container">

        <h1 class="text-center my-4">Detalles de la Compra</h1>
        <p class="text-center">Hola <?php echo htmlspecialchars($usuario['nombre1']); ?> <?php echo htmlspecialchars($usuario['apellido1']); ?>, gracias por comprar con nosotros.</p>
        <?php if ($resultadoDetallesCompra->num_rows > 0): ?>
            <div class="compra-detalle">    
                <?php while ($detalle = $resultadoDetallesCompra->fetch_assoc()): ?>    
                    <p>Producto: <?php echo htmlspecialchars($detalle['nombre']); ?></p>
                    <p>Cantidad: <?php echo htmlspecialchars($detalle['cantidad']); ?></p>
                    <p>Precio Unitario: $<?php echo htmlspecialchars($detalle['precio']); ?></p>
                    <p>Subtotal: $<?php echo htmlspecialchars(($detalle['precio'])*($detalle['cantidad'])); ?></p>
                    <hr>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p class="text-center">No se encontraron detalles para esta compra.</p>
        <?php endif; ?>
        

        <div class="datos-cliente">
            <h2>Datos del Cliente</h2>
            <p>Nombre: <?php echo htmlspecialchars($usuario['nombre1']); ?> <?php echo htmlspecialchars($usuario['apellido1']); ?></p>
            <p>Identificación: <?php echo htmlspecialchars($usuario['identificacion']); ?></p>
            <p>Correo: <?php echo htmlspecialchars($usuario['email']); ?></p>
            <p>Dirección: <?php echo htmlspecialchars($usuario['direccion']); ?></p>
            <p>Fecha de Compra: <?php echo htmlspecialchars($fecha_compra); ?></p>
        </div>
        <br>
        </div>

        <div class="text-center">
            <a href="compras.php" class="btn btn-warning">Ver Historial de Compras</a>
        </div>

    </div>

    <footer class="text-center mt-5">
        <p>©2023. All rights reserved.</p>
    </footer>
</body>
</html>

<?php
$stmtUsuario->close();
$stmtDetallesCompra->close();
$conn->close();
?>
