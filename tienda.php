<?php
session_start(); // Iniciar la sesión

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    // Si el usuario no está logueado, redirigir a la página de login
    header('Location: login.php');
    exit;
}

include 'db.php'; // Conexion a la Base de Datos

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo de Productos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
        <link rel="stylesheet" href="Styles/store.css">
    <script src="scripts.js"></script>

</head>

<body>
    <header>
        <div class="header-left">
            <img src="Imagenes/logo.png" alt="Logo de ShoeStream" class="logo">
            <span class="site-name">ShoeStream Store</span>
        </div>
        <div class="header-right">
        <a href="compras.php">
                <img src="Imagenes/pedidos.png" alt="historial" class="icon">
            </a>
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
    <h1 >Catálogo de Productos</h1>
    <h2 style="text-align: center;">DESTACADOS DE LA SEMANA</h2>
        <section class="mt-5">
            <div id="carouselExampleControls" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <div class="carousel-item active border rounded info p-5">
                        <div class="row">
                            <div class="col-lg-7 d-flex align-items-center">
                                <div>
                                    <h1>Adidas Breaknet Court</h1>
                                    <h5>Desde 1984, son un símbolo de la expresión personal y de lo que puedes conseguir trabajando duro. Esta versión luce una parte superior y detalles en contraste que conservan intacto el legado de los Forum.</h5>
                                    <h1>$ 216.900</h1>

                                </div>
                            </div>
                            <div class="col-lg-5">
                                <img src="Imagenes/adidas.png" class="d-block w-100" alt="...">
                            </div>
                        </div>
                    </div>
                    <div class="carousel-item active border rounded info p-5">
                        <div class="row">
                            <div class="col-lg-7 d-flex align-items-center">
                                <div>
                                    <h1>Reebok Classics Advance</h1>
                                    <h5>Todo el estilo de cancha de Reebok que amas. Estos tenis veganos para mujer combinan los íconos Club C 85 y Club C Revenge para hacer el nuevo Court Advance. Tan cómodos y acolchados con espuma y Microburbujas DMX, querrás usarlas siempre. Una línea nítida de color añade el toque final.</h5>
                                    <h3> <strike>$ 319.900</strike> </h3>
                                    <h1>$ 229.900 -28%</h1>
                                    

                                </div>
                            </div>
                            <div class="col-lg-5">
                                <img src="Imagenes/reebok.png" class="d-block w-100" alt="...">
                            </div>
                            
                        </div>
                        <br>
                    </div>
                    <div class="carousel-item active border rounded info p-5">
                        <div class="row">
                            <div class="col-lg-7 d-flex align-items-center">
                                <div>
                                    <h1>Puma Caven 2.0 Vt</h1>
                                    <h5>Llevando los Caven 2.0 Vt de PUMA podrás disfrutar plenamente de tu actividad física favorita o de tu tiempo de ocio.</h5>
                                    <h3> <strike> $ 394.000</strike> </h3>
                                    <h1>$ 331.900 -16%</h1>
                                </div>
                            </div>
                            <div class="col-lg-5">
                                <img src="Imagenes/puma.png" class="d-block w-100" alt="...">
                            </div>
                        </div>
                    </div>

                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleControls"
                    data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">anterior</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleControls"
                    data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">siguiente</span>
                </button>
            </div>
        </section>
      
        <div class="product-grid">
            <?php
// Consulta para obtener los productos
$sql = "SELECT * FROM productos"; 
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Mostrar datos de cada fila
    while ($row = $result->fetch_assoc()) {
        echo "<div class='product-item'>";
        echo "<img src='Imagenes/" . $row['imagen'] . "' alt='" . $row['nombre'] . "' class='product-image'>";
        echo "<h2 class='product-name'>" . $row['nombre'] . "</h2>";
        echo "<p class='product-description'>" . $row['descripcion'] . "</p>";
        echo "<p class='product-price'>Precio: $ " . $row['precio'] . "</p>";
        echo "<p class='product-sizes'>Talla: " . $row['talla'] . "</p>";
        echo "<p class='product-quantity'>Cantidad Disponible: " . $row['cantidad_disponible'] . "</p>";

        // Botón de agregar a lista de deseos
        echo "<img src='Imagenes/lista-de-deseos.png' alt='Agregar a Lista de Deseos' onclick='agregarAListaDeseos(" . $row['id_producto'] . ")' style='cursor: pointer;' />";
        echo "<img src='Imagenes/anadir-al-carrito.png' alt='Agregar a Lista de Deseos' onclick='agregarALcarrito(" . $row['id_producto'] . ")' style='cursor: pointer;' />";

        echo "</div>"; // Cierra el div de product-item
    }
} else {
    echo "No hay productos disponibles.";
}

$conn->close();
?>
        </div>

        <footer class="mt-5 text-center">
        <p><small>©2023. Jose Berastegui All rights reserved.</small></p>
    </footer>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
        <a href="https://api.whatsapp.com/send?phone=573234526491&text=Hola%21%20Quisiera%20m%C3%A1s%20informaci%C3%B3n."
            class="float" target="_blank">
            <i class="fa fa-whatsapp my-float"></i>
        </a>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-A3rJD856KowSb7dwlZdYEkO39Gagi7vIsF0jrRAoQmDKKtQBHUuLZ9AsSv4jD4Xa"
        crossorigin="anonymous"></script>

    <script>

    </main>


</body>
</html>
