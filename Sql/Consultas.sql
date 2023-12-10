--SUBCONSULTAS

-- Encontrar todos los usuarios que han añadido al menos un producto a su carrito pero no han realizado una compra:
SELECT u.*
FROM usuarios u
WHERE EXISTS (
    SELECT 1 FROM carrito c
    WHERE c.id_usuario = u.id
    AND NOT EXISTS (
        SELECT 1 FROM compra cp
        WHERE cp.id_usuario = u.id
    )
);

-- Obtener el total gastado por cada usuario en compras:
SELECT u.id, SUM(dc.cantidad * p.precio) AS total_gasto
FROM usuarios u
JOIN compra c ON u.id = c.id_usuario
JOIN detallecompra dc ON c.id_compra = dc.id_compra
JOIN productos p ON dc.id_producto = p.id_producto
GROUP BY u.id;

-- Listar todos los productos que nunca se han añadido a una lista de deseos:
SELECT *
FROM productos p
WHERE NOT EXISTS (
    SELECT 1 FROM productolistadeseos pl
    WHERE pl.id_producto = p.id_producto
);

-- FUNCIONES

-- Función para contar la cantidad de productos en el carrito de un usuario:
DELIMITER //
CREATE FUNCTION ContarProductosCarrito(p_id_usuario INT) RETURNS INT DETERMINISTIC
BEGIN
    DECLARE v_result INT;
    SELECT COUNT(*) INTO v_result FROM detallecarrito dc
    JOIN carrito c ON dc.id_carrito = c.id_carrito
    WHERE c.id_usuario = p_id_usuario;
    RETURN v_result;
END //
DELIMITER ;

-- Función para calcular el total del carrito de un usuario:
DELIMITER //
CREATE FUNCTION CalcularTotalCarrito(p_id_usuario INT) RETURNS DECIMAL(10,2) DETERMINISTIC
BEGIN
    DECLARE v_total DECIMAL(10,2);
    SELECT SUM(p.precio * dc.cantidad) INTO v_total FROM detallecarrito dc
    JOIN carrito c ON dc.id_carrito = c.id_carrito
    JOIN producto p ON dc.id_producto = p.id_producto
    WHERE c.id_usuario = p_id_usuario;
    RETURN IFNULL(v_total, 0);
END //
DELIMITER ;

-- Función para obtener el último producto añadido por un usuario a su lista de deseos:
DELIMITER //
CREATE FUNCTION UltimoProductoListaDeseos(p_id_usuario INT) RETURNS VARCHAR(100)
BEGIN
    DECLARE v_nombre_producto VARCHAR(100);
    SELECT p.nombre INTO v_nombre_producto
    FROM productolistadeseos pl
    JOIN producto p ON pl.id_producto = p.id_producto
    JOIN listadeseos l ON pl.id_listadeseos = l.id_listadeseos
    WHERE l.id_usuario = p_id_usuario
    ORDER BY pl.id_productolistadeseos DESC LIMIT 1;
    RETURN v_nombre_producto;
END //
DELIMITER ;

--PROCEDIMIENTOS ALMACENADOS

-- Procedimiento para añadir un producto a la lista de deseos de un usuario:
DELIMITER //
CREATE PROCEDURE AgregarListaDeseos(IN p_id_usuario INT, IN p_id_producto INT)
BEGIN
    INSERT INTO productolistadeseos (id_listadeseos, id_producto)
    SELECT id_listadeseos, p_id_producto FROM listadeseos
    WHERE id_usuario = p_id_usuario;
END //
DELIMITER ;

-- Procedimiento para crear una nueva compra a partir de los items en el carrito de un usuario:
DELIMITER //
CREATE PROCEDURE RealizarCompra(IN p_id_usuario INT)
BEGIN
    DECLARE v_id_compra INT;
    INSERT INTO compra (id_usuario, fechahora) VALUES (p_id_usuario, NOW());
    SET v_id_compra = LAST_INSERT_ID();
    INSERT INTO detallecompra (id_compra, id_producto, cantidad)
    SELECT v_id_compra, id_producto, cantidad FROM detallecarrito
    WHERE id_carrito = (SELECT id_carrito FROM carrito WHERE id_usuario = p_id_usuario);
    DELETE FROM detallecarrito WHERE id_carrito = (SELECT id_carrito FROM carrito WHERE id_usuario = p_id_usuario);
END //
DELIMITER ;

-- Procedimiento para actualizar la cantidad disponible de un producto tras una compra:
DELIMITER //
CREATE PROCEDURE ActualizarInventario()
BEGIN
    UPDATE productoS p
    JOIN detallecompra dc ON p.id_producto = dc.id_producto
    SET p.cantidadDisponible = p.cantidadDisponible - dc.cantidad;
END //
DELIMITER ;


-- Disparadores

DELIMITER //
CREATE TRIGGER AntesDeAñadirCarrito
BEFORE INSERT ON detallecarrito
FOR EACH ROW
BEGIN
    DECLARE stock INT;
    SELECT cantidadDisponible INTO stock FROM productos WHERE id_producto = NEW.id_producto;
    IF stock < NEW.cantidad THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'No hay suficiente stock para este producto.';
    END IF;
END //
DELIMITER ;


--Disparador para limpiar el carrito después de una compra:
DELIMITER //
CREATE TRIGGER LimpiarCarrito
AFTER INSERT ON compra
FOR EACH ROW
BEGIN
    DELETE FROM detallecarrito
    WHERE id_carrito = (SELECT id_carrito FROM carrito WHERE id_usuario = NEW.id_usuario);
END //
DELIMITER ;


--Disparador para crear automáticamente la lista de deseos para un nuevo usuario:
DELIMITER //
CREATE TRIGGER CrearListaDeseosParaNuevoUsuario
AFTER INSERT ON usuarios
FOR EACH ROW
BEGIN
    INSERT INTO listadeseos (id_usuario)
    VALUES (NEW.id_usuario);
END //
DELIMITER ;
