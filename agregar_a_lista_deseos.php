<?php
session_start(); // Iniciar sesión para tener acceso a $_SESSION

include 'db.php'; 

// Verificar si el usuario está logueado y si la solicitud es POST
if (!isset($_SESSION['usuario_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Manejar el caso de que el usuario no esté logueado o la solicitud no sea POST
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

// Función que agrega un producto a la lista de deseos
function agregarAListaDeseos($conn, $idUsuario, $idProducto) {
    // Buscar la lista de deseos del usuario
    $sql = "SELECT ID_ListaDeseos FROM ListaDeseos WHERE ID_Usuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $idUsuario);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // El usuario ya tiene una lista de deseos
        $lista = $result->fetch_assoc();
        $idListaDeseos = $lista['ID_ListaDeseos'];
    } else {
        // No hay lista de deseos, crear una nueva
        $sql = "INSERT INTO ListaDeseos (ID_Usuario) VALUES (?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $idUsuario);
        $stmt->execute();
        $idListaDeseos = $stmt->insert_id;
    }

    // Verificar si el producto ya está en la lista de deseos
    $sql = "SELECT ID_ProductoListaDeseos FROM ProductoListaDeseos WHERE ID_ListaDeseos = ? AND ID_Producto = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $idListaDeseos, $idProducto);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 0) {
        // El producto no está en la lista, agregarlo
        $sql = "INSERT INTO ProductoListaDeseos (ID_ListaDeseos, ID_Producto) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $idListaDeseos, $idProducto);
        if($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    } else {
        // El producto ya está en la lista
        return false;
    }
}

// Llamar a la función y pasarle la conexión y los IDs
if(agregarAListaDeseos($conn, $idUsuario, $idProducto)) {
    echo 'Producto agregado a la lista de deseos con éxito';
} else {
    echo 'El producto ya está en la lista de deseos o hubo un error';
}

$conn->close();
?>
