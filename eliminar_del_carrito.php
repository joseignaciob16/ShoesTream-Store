<?php
session_start();

include 'db.php';

if (!isset($_SESSION['usuario_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo 'No autorizado';
    exit;
}

$idUsuario = $_SESSION['usuario_id'];
$idProducto = $_POST['idProducto'] ?? 0;

// Encuentra el id_carrito asociado al usuario
$sqlCarrito = "SELECT id_carrito FROM carrito WHERE id_usuario = ?";
$stmtCarrito = $conn->prepare($sqlCarrito);
$stmtCarrito->bind_param("i", $idUsuario);
$stmtCarrito->execute();
$resultadoCarrito = $stmtCarrito->get_result();
if ($resultadoCarrito->num_rows > 0) {
    $carrito = $resultadoCarrito->fetch_assoc();
    $idCarrito = $carrito['id_carrito'];

    // Elimina el producto del detallecarrito
    $sqlEliminar = "DELETE FROM detallecarrito WHERE id_carrito = ? AND id_producto = ?";
    $stmtEliminar = $conn->prepare($sqlEliminar);
    $stmtEliminar->bind_param("ii", $idCarrito, $idProducto);
    if ($stmtEliminar->execute()) {
        echo 'Producto eliminado del carrito.';
    } else {
        echo 'No se pudo eliminar el producto del carrito.';
    }
} else {
    echo 'No se encontró el carrito.';
}

$conn->close();
?>
