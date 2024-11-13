<?php
session_start();

// Verificamos si el carrito ya está inicializado
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// Obtenemos los datos del producto desde el formulario
$id = $_POST['id'] ?? null;
$nombre = $_POST['nombre'] ?? null;
$precio = $_POST['precio'] ?? null;
$cantidad = (int) ($_POST['cantidad'] ?? 1); // Aseguramos que la cantidad sea un número

// Validamos que los datos sean correctos
if ($id && $nombre && $precio && $cantidad > 0) {
    // Verificamos si el producto ya está en el carrito
    $productoExistente = false;

    foreach ($_SESSION['carrito'] as &$producto) {
        if ($producto['id'] == $id) {
            // Si el producto ya existe en el carrito, actualizamos su cantidad
            $producto['cantidad'] = $cantidad; // Asignamos la nueva cantidad
            $productoExistente = true;
            break;
        }
    }

    // Si el producto no está en el carrito, lo añadimos
    if (!$productoExistente) {
        $nuevoProducto = [
            'id' => $id,
            'nombre' => $nombre,
            'precio' => $precio,
            'cantidad' => $cantidad,
        ];
        $_SESSION['carrito'][] = $nuevoProducto;
    }

    // Mensaje de éxito
    $_SESSION['mensaje'] = "Se ha agregado $cantidad de $nombre al carrito.";
} else {
    $_SESSION['mensaje'] = "Error al agregar el producto al carrito. Verifique los datos.";
}

// Redirigimos al carrito o a la página anterior
header('Location: ver_carrito.php');
exit();
?>
