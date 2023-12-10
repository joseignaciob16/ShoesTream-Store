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

// Consulta para obtener los productos en el carrito del usuario
$sql = "SELECT p.id_producto, p.nombre, p.descripcion, p.precio, dc.cantidad, p.cantidad_disponible, p.imagen
        FROM detallecarrito dc
        INNER JOIN carrito c ON dc.id_carrito = c.id_carrito
        INNER JOIN productos p ON dc.id_producto = p.id_producto
        WHERE c.id_usuario = ?"; 
        
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$resultado = $stmt->get_result();

$productos_carrito = [];
if ($resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {
        $productos_carrito[] = $fila;
    }
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Compras</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="Styles/carro.css">
    <script src="scripts.js"></script>
    <script>
        // Función para actualizar el total del carrito
        function actualizarTotal() {
    var total = 0;
    var precios = document.getElementsByClassName('precio');
    var cantidades = document.getElementsByClassName('cantidad');
    var cantidadesDisponibles = document.getElementsByClassName('cantidad-disponible');

    for (var i = 0; i < precios.length; i++) {
        var cantidad = parseInt(cantidades[i].value);
        var cantidadDisponible = parseInt(cantidadesDisponibles[i].value);

        if (cantidad > cantidadDisponible) {
            alert("La cantidad supera el stock disponible.");
            cantidades[i].value = cantidadDisponible; // opcional, ajusta la cantidad al máximo disponible
            return; // Interrumpe la función para evitar actualizar el total
        }

        total += parseFloat(precios[i].innerText) * cantidad;
    }

    document.getElementById('total').innerText = 'Total a Pagar: $' + total.toFixed(2);
}


        // Añade aquí más funciones de JavaScript si son necesarias
    </script>
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
            <a href="whitelist.php">
                <img src="Imagenes/whitlist.png" alt="Wishlist" class="icon">
            </a>
            <a href="logout.php">
                <img src="Imagenes/salir.png" alt="Salir" class="icon">
            </a>
        </div>
    </header>
    <main>  
        <h1>Tu Carrito de Compras</h1>
        <div class="product-row">
            <?php if (count($productos_carrito) > 0): ?>
                <?php foreach ($productos_carrito as $producto): ?>
                    <div class="product-column col-md-4">
                        <div class="product-item">
                            <img src="Imagenes/<?php echo htmlspecialchars($producto['imagen']); ?>" alt="<?php echo htmlspecialchars($producto['nombre']); ?>" class="carrito-product-image">
                            <h2 class="carrito-product-name"><?php echo htmlspecialchars($producto['nombre']); ?></h2>
                            <p class="carrito-product-description"><?php echo htmlspecialchars($producto['descripcion']); ?></p>
                            <input type="number" class="cantidad" value="<?php echo htmlspecialchars($producto['cantidad']); ?>" oninput="actualizarTotal()" min="1" max="<?php echo htmlspecialchars($producto['cantidad_disponible']); ?>">
                            <input type="hidden" class="cantidad-disponible" value="<?php echo htmlspecialchars($producto['cantidad_disponible']); ?>">
                            <p class="carrito-product-price">Precio: $<span class="precio"><?php echo htmlspecialchars($producto['precio']); ?></span></p>
                            <img src="Imagenes/trash.png" alt="Eliminar del Carrito" onclick="eliminarDelCarrito(<?php echo $producto['id_producto']; ?>)" style="cursor: pointer;" />
                        </div>
                    </div>
                <?php endforeach; ?>           
            <?php else: ?>
                <p>Tu carrito está vacío.</p>
            <?php endif; ?>
        </div>
        <div id="total" class="total">
            Total a Pagar: 
        </div>
        <br>
        <button class="btn btn-primary btn-lg" onclick="realizarCompra()">Checkout</button>

<script>
    function realizarCompra() {
        // Mostrar una alerta de confirmación
        var confirmacion = confirm("¿Estás seguro de que quieres realizar la compra?");
        if (confirmacion) {
            // Si el usuario confirma, envía una solicitud al servidor
            window.location.href = 'procesar_compra.php';
        }
    }
</script>

    </main>
    
    <footer class="mt-5 text-center">
        <p><small>©2023.  All rights reserved.</small></p>
    </footer>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        actualizarTotal();
    });
</script>
</body>
</html>
