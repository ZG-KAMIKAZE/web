<?php
session_start();
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $producto_id = filter_input(INPUT_POST, 'producto_id', FILTER_SANITIZE_NUMBER_INT);
    $cantidad_comprar = filter_input(INPUT_POST, 'cantidad_comprar', FILTER_SANITIZE_NUMBER_INT);
    $monto = filter_input(INPUT_POST, 'monto', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $numero_yape = filter_input(INPUT_POST, 'numero_yape', FILTER_SANITIZE_STRING);

    // Obtener detalles del producto de la base de datos
    $stmt = $conn->prepare("SELECT id, nombre, precio, stock, imagen FROM productos WHERE id = ?");
    
    // Verificar si la preparación de la consulta fue exitosa
    if (!$stmt) {
        echo "Error en la preparación de la consulta: " . $conn->error;
        exit();
    }
    
    $stmt->bind_param("i", $producto_id);
    $stmt->execute();
    $producto = $stmt->get_result()->fetch_assoc();

    if (!$producto) {
        echo "Producto no encontrado.";
        exit();
    }

    // Verificar si hay suficiente stock
    if (!isset($producto['stock']) || $producto['stock'] < $cantidad_comprar) {
        echo "No hay suficiente stock disponible.";
        exit();
    }

    // Actualizar el stock
    $nuevo_stock = $producto['stock'] - $cantidad_comprar;
    $stmt = $conn->prepare("UPDATE productos SET stock = ? WHERE id = ?");
    
    if (!$stmt) {
        echo "Error en la preparación de la consulta de actualización: " . $conn->error;
        exit();
    }
    
    $stmt->bind_param("ii", $nuevo_stock, $producto_id);
    if (!$stmt->execute()) {
        echo "Error al actualizar el stock.";
        exit();
    }

    // Función para censurar el número de Yape
    function censurarNumeroYape($numero) {
        $longitud = strlen($numero);
        if ($longitud > 3) {
            return substr($numero, 0, 3) . str_repeat('*', $longitud - 3);
        }
        return $numero; // Si el número tiene 3 dígitos o menos, no se censura
    }

    // Generar la boleta
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Boleta de Pago</title>
        <link rel="stylesheet" href="css/bootstrap.min.css">
    </head>
    <body>
        <div class="container">
            <h1>Boleta de Pago</h1>
            <h2>Detalles del Producto</h2>
            <img src="<?php echo htmlspecialchars($producto['imagen']); ?>" alt="<?php echo htmlspecialchars($producto['nombre']); ?>" style="width: 200px; height: auto;" onerror="this.onerror=null; this.src='images/default.jpg';">
            <p><strong>Nombre:</strong> <?php echo htmlspecialchars($producto['nombre']); ?></p>
            <p><strong>Cantidad Comprada:</strong> <?php echo htmlspecialchars($cantidad_comprar); ?></p>
            <p><strong>Precio:</strong> S/. <?php echo number_format($producto['precio'], 2); ?></p>
            <p><strong>Monto Total:</strong> S/. <?php echo number_format($monto, 2); ?></p>
            <p><strong>Método de Pago:</strong> Yape</p>
            <p><strong>Número de Yape:</strong> <?php echo htmlspecialchars(censurarNumeroYape($numero_yape)); ?></p>

            <button onclick="window.print()" class="btn btn-primary">Imprimir Boleta</button>
            <button onclick="window.location.href='tienda.php';" class="btn btn-secondary">Volver a Tienda</button>
        </div>
    </body>
    </html>
    <?php
} 
?>
