function agregarALcarrito(idProducto) {
    fetch('agregar_al_carrito.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'idProducto=' + idProducto
    })
        .then(response => response.text())
        .then(text => {
            alert(text); // Muestra una alerta con la respuesta del servidor
            // Puedes actualizar la interfaz de usuario aquí si es necesario
        })
        .catch(error => {
            console.error('Error:', error);
        });
}

function eliminarDelCarrito(idProducto) {
    if (!confirm('¿Estás seguro de que quieres eliminar este producto del carrito?')) {
        return;
    }

    fetch('eliminar_del_carrito.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'idProducto=' + idProducto
    })
    .then(response => response.text())
    .then(text => {
        alert(text); // Muestra una alerta con la respuesta del servidor
        location.reload(); // Recarga la página para actualizar la vista del carrito
    })
    .catch(error => {
        console.error('Error:', error);
    });
}


// Aquí va el código JavaScript para manejar la adición de productos a la lista de deseos
function agregarAListaDeseos(idProducto) {
    // Puedes usar 'fetch' o 'XMLHttpRequest' para hacer la solicitud
    fetch('agregar_a_lista_deseos.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'idProducto=' + idProducto
    })
        .then(response => response.text())
        .then(text => {
            alert(text); // Muestra una alerta con la respuesta del servidor
        })
        .catch(error => {
            console.error('Error:', error);
        });
}


function eliminarDeListaDeseos(idProducto) {
    if (!confirm('¿Estás seguro de que quieres eliminar este producto de la lista de deseos?')) {
        return;
    }

    // Sustituye 'eliminar_producto_lista_deseos.php' con la ruta al script PHP que manejará la eliminación del producto
    fetch('eliminar_producto_lista_deseos.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'idProducto=' + idProducto
    })
        .then(response => response.text())
        .then(text => {
            alert(text);
            location.reload(); // Recarga la página para actualizar la lista de deseos
        })
        .catch(error => {
            console.error('Error:', error);
        });
}
