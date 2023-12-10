<?php
session_start();
include 'db.php';

$id_usuario = $_SESSION['usuario_id'];
$problemaConCompra = false;

$sql = "SELECT dc.id_producto, dc.cantidad, p.cantidad_disponible, dc.id_detalle
        FROM detallecarrito dc
        INNER JOIN carrito c ON dc.id_carrito = c.id_carrito
        INNER JOIN productos p ON dc.id_producto = p.id_producto
        WHERE c.id_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {
    // Iniciar transacción
    $conn->begin_transaction();
    try {
        while ($fila = $resultado->fetch_assoc()) {
            if ($fila['cantidad'] <= 0 || $fila['cantidad'] > $fila['cantidad_disponible']) {
                $problemaConCompra = true;
                $conn->rollback();
                break;
            }
        }

        if (!$problemaConCompra) {
            // Insertar la compra
            $stmtCompra = $conn->prepare("INSERT INTO compra (id_usuario, fecha_compra) VALUES (?, NOW())");
            $stmtCompra->bind_param("i", $id_usuario);
            $stmtCompra->execute();
            $id_compra = $conn->insert_id;
            $stmtCompra->close();

            // Insertar los detalles de la compra
            foreach ($resultado as $fila) {
                $stmtDetalleCompra = $conn->prepare("INSERT INTO detallecompra (id_compra, id_producto, cantidad) VALUES (?, ?, ?)");
                $stmtDetalleCompra->bind_param("iii", $id_compra, $fila['id_producto'], $fila['cantidad']);
                $stmtDetalleCompra->execute();
                $stmtDetalleCompra->close();

                // Actualizar la cantidad disponible en la base de datos
                $nuevaCantidad = $fila['cantidad_disponible'] - $fila['cantidad'];
                $stmtUpdate = $conn->prepare("UPDATE productos SET cantidad_disponible = ? WHERE id_producto = ?");
                $stmtUpdate->bind_param("ii", $nuevaCantidad, $fila['id_producto']);
                $stmtUpdate->execute();
                $stmtUpdate->close();
            }

            // Vaciar el carrito del usuario
            $stmtVaciarCarrito = $conn->prepare("DELETE FROM detallecarrito WHERE id_carrito IN (SELECT id_carrito FROM carrito WHERE id_usuario = ?)");
            $stmtVaciarCarrito->bind_param("i", $id_usuario);
            $stmtVaciarCarrito->execute();
            $stmtVaciarCarrito->close();

            // Comprometer la transacción
            $conn->commit();
            
            // Redirigir a una página de confirmación
            header("Location: compra_exitosa.php?id_compra=$id_compra");
            exit;
        } else {
            // Redirigir al usuario a la página del carrito con un mensaje de error
            header('Location: carrito.php?error=compraInvalida');
            exit;
        }
     } catch (Exception $e) {
        // Si ocurre un error, revertir la transacción
        $conn->rollback();
        header('Location: carrito.php?error=errorProceso');
        exit;
    } 
} else {
    // El carrito está vacío
    header('Location: carrito.php?error=carritoVacio');
    exit;
}

$conn->close();
?>
