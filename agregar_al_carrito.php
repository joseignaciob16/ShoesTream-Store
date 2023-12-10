<?php
session_start(); // Iniciar sesión para tener acceso a $_SESSION

include 'db.php';

if (!isset($_SESSION['usuario_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo 'No autorizado';
    exit;
}

$idUsuario = $_SESSION['usuario_id']; // ID del usuario en sesión
$idProducto = $_POST['idProducto'] ?? 0; // El ID del producto a agregar

// Primero, verifica si existe un carrito para el usuario
$sqlCarrito = "SELECT id_carrito FROM carrito WHERE id_usuario = ?";
$stmtCarrito = $conn->prepare($sqlCarrito);
$stmtCarrito->bind_param("i", $idUsuario);
$stmtCarrito->execute();
$resultadoCarrito = $stmtCarrito->get_result();
$idCarrito = 0;

if ($resultadoCarrito->num_rows > 0) {
    // Si existe un carrito, obtén el id_carrito
    $filaCarrito = $resultadoCarrito->fetch_assoc();
    $idCarrito = $filaCarrito['id_carrito'];
} else {
    // Si no existe, crea uno nuevo
    $sqlCrearCarrito = "INSERT INTO carrito (id_usuario, fechacreacion) VALUES (?, NOW())";
    $stmtCrearCarrito = $conn->prepare($sqlCrearCarrito);
    $stmtCrearCarrito->bind_param("i", $idUsuario);
    $stmtCrearCarrito->execute();
    $idCarrito = $stmtCrearCarrito->insert_id;
}

// Ahora, verifica si el producto ya está en el carrito
$sqlVerificar = "SELECT * FROM detallecarrito WHERE id_carrito = ? AND id_producto = ?";
$stmtVerificar = $conn->prepare($sqlVerificar);
$stmtVerificar->bind_param("ii", $idCarrito, $idProducto);
$stmtVerificar->execute();
$resultadoVerificar = $stmtVerificar->get_result();

// Si el producto ya está en el carrito, simplemente aumenta la cantidad
if ($resultadoVerificar->num_rows > 0) {
    $detalleCarrito = $resultadoVerificar->fetch_assoc();
    $cantidadActual = $detalleCarrito['cantidad'] + 1;
    $sqlActualizar = "UPDATE detallecarrito SET cantidad = ? WHERE id_carrito = ? AND id_producto = ?";
    $stmtActualizar = $conn->prepare($sqlActualizar);
    $stmtActualizar->bind_param("iii", $cantidadActual, $idCarrito, $idProducto);
    $stmtActualizar->execute();
    echo 'Cantidad actualizada en el carrito.';
} else {
    // Si el producto no está en el carrito, agrégalo
    $sqlAgregar = "INSERT INTO detallecarrito (id_carrito, id_producto, cantidad) VALUES (?, ?, 1)";
    $stmtAgregar = $conn->prepare($sqlAgregar);
    $stmtAgregar->bind_param("ii", $idCarrito, $idProducto);
    if ($stmtAgregar->execute()) {
        echo 'Producto agregado al carrito.';
    } else {
        echo 'Error al agregar el producto al carrito.';
    }
}

$conn->close();
?>
