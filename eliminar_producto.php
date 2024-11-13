<?php
session_start();

if (isset($_POST['id'])) {
    $idProducto = $_POST['id'];

    // Verifica si el carrito está definido
    if (isset($_SESSION['carrito'])) {
        // Busca el índice del producto en el carrito
        foreach ($_SESSION['carrito'] as $key => $producto) {
            if ($producto['id'] == $idProducto) {
                // Elimina el producto del carrito
                unset($_SESSION['carrito'][$key]);
                $_SESSION['mensaje'] = "Producto eliminado del carrito.";
                break;
            }
        }
        
        // Reindexa el array del carrito (opcional, pero recomendado)
        $_SESSION['carrito'] = array_values($_SESSION['carrito']);
    }

    // Redirige de vuelta a la página del carrito
    header("Location: ver_carrito.php");
    exit();
} else {
    // Si no se recibe el ID, redirige a la página del carrito
    header("Location: ver_carrito.php");
    exit();
}
?>
