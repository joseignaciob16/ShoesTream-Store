<?php
session_start(); // Iniciar sesión para tener acceso a $_SESSION

include 'db.php'; // Asegúrate de que este archivo contiene la conexión a tu base de datos

// Verificar si el usuario está logueado y si la solicitud es POST
if (!isset($_SESSION['usuario_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo 'No autorizado';
    exit;
}

$idUsuario = $_SESSION['usuario_id']; // Obtener el ID del usuario de la sesión
$idProducto = $_POST['idProducto'] ?? 0; // El operador de fusión de null (??) es para PHP 7+

// Verificar si el ID del producto es válido
if ($idProducto <= 0) {
    echo 'ID de producto inválido';
    exit;
}

// Función que elimina un producto de la lista de deseos
function eliminarDeListaDeseos($conn, $idUsuario, $idProducto) {
    // Primero, obtenemos el ID de la lista de deseos del usuario
    $sqlListaDeseos = "SELECT ID_ListaDeseos FROM ListaDeseos WHERE ID_Usuario = ?";
    $stmtLista = $conn->prepare($sqlListaDeseos);
    $stmtLista->bind_param("i", $idUsuario);
    $stmtLista->execute();
    $resultadoLista = $stmtLista->get_result();

    if ($resultadoLista->num_rows > 0) {
        $filaLista = $resultadoLista->fetch_assoc();
        $idListaDeseos = $filaLista['ID_ListaDeseos'];
        
        // Ahora, eliminamos el producto de la lista de deseos
        $sqlEliminar = "DELETE FROM ProductoListaDeseos WHERE ID_ListaDeseos = ? AND ID_Producto = ?";
        $stmtEliminar = $conn->prepare($sqlEliminar);
        $stmtEliminar->bind_param("ii", $idListaDeseos, $idProducto);
        
        if ($stmtEliminar->execute()) {
            return true;
        } else {
            // Manejar el error aquí
            return false;
        }
    } else {
        echo 'No se encontró la lista de deseos.';
        return false;
    }
}

// Llamar a la función y pasarle la conexión y los IDs
if(eliminarDeListaDeseos($conn, $idUsuario, $idProducto)) {
    echo 'Producto eliminado de la lista de deseos con éxito';
} else {
    echo 'Hubo un error al eliminar el producto de la lista de deseos';
}

$conn->close();
?>
