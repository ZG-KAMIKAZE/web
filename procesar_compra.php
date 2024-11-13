<?php
session_start();
include 'config.php'; // Asegúrate de que este archivo contenga la conexión a la base de datos.

if (!isset($_SESSION['carrito'])) {
    echo "El carrito está vacío.";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $metodo_pago = filter_input(INPUT_POST, 'metodo_pago', FILTER_SANITIZE_STRING);
    $numero_yape = ($metodo_pago === 'yape') ? filter_input(INPUT_POST, 'numero_yape', FILTER_SANITIZE_STRING) : null;
    $monto_efectivo = ($metodo_pago === 'efectivo') ? filter_input(INPUT_POST, 'monto_efectivo', FILTER_VALIDATE_FLOAT) : null;
    $productosComprados = $_SESSION['carrito'];
    $totalMonto = 0;

    // Detalles de los productos comprados
    $detallesCompra = "";

    foreach ($productosComprados as $producto) {
        $producto_id = filter_var($producto['id'], FILTER_SANITIZE_NUMBER_INT);
        $cantidad_comprar = filter_var($producto['cantidad'], FILTER_SANITIZE_NUMBER_INT);

        // Obtener detalles del producto de la base de datos
        $stmt = $conn->prepare("SELECT id, nombre, precio, stock FROM productos WHERE id = ?");
        $stmt->bind_param("i", $producto_id);
        $stmt->execute();
        $productoDetalles = $stmt->get_result()->fetch_assoc();

        if (!$productoDetalles || $productoDetalles['stock'] < $cantidad_comprar) {
            echo "Error con el producto: " . htmlspecialchars($producto['nombre']) . ".";
            exit();
        }

        // Actualizar el stock
        $nuevo_stock = $productoDetalles['stock'] - $cantidad_comprar;
        $stmt = $conn->prepare("UPDATE productos SET stock = ? WHERE id = ?");
        $stmt->bind_param("ii", $nuevo_stock, $producto_id);
        $stmt->execute();

        $monto = $productoDetalles['precio'] * $cantidad_comprar;
        $totalMonto += $monto;

        $detallesCompra .= htmlspecialchars($productoDetalles['nombre']) . " - Cantidad: " . $cantidad_comprar . " - Total: S/. " . number_format($monto, 2) . "\n";
    }

    function censurarNumeroYape($numero) {
        $longitud = strlen($numero);
        return substr($numero, 0, 3) . str_repeat('*', $longitud - 3);
    }

    $qrContent = "Total de Compra: S/. " . number_format($totalMonto, 2) . "\n" .
                 "Método de Pago: " . ucfirst($metodo_pago) . "\n" .
                 ($metodo_pago === 'yape' ? "Número Yape: " . htmlspecialchars($numero_yape) . "\n" : "") .
                 ($metodo_pago === 'efectivo' ? "Monto Recibido: S/. " . number_format($monto_efectivo, 2) . "\n" . 
                 "Vuelto: S/. " . number_format($monto_efectivo - $totalMonto, 2) . "\n" : "") .
                 "Detalles de la Compra:\n" .
                 $detallesCompra;

    $qrContentEncoded = urlencode($qrContent);
    $qrCodeUrl = "https://api.qrserver.com/v1/create-qr-code/?data=$qrContentEncoded&size=200x200";

    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Boleta de Pago</title>
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <style>
            .receipt-card {
                padding: 20px;
                background: #fff;
                border-radius: 8px;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
                margin-top: 50px;
            }
            .qr-code {
                text-align: center;
                margin-top: 20px;
            }
            .product-table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 20px;
            }
            .product-table th, .product-table td {
                border: 1px solid #ddd;
                padding: 8px;
                text-align: left;
            }
            .product-table th {
                background-color: #f8f8f8;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="receipt-card">
                <h1>Boleta de Pago</h1>
                <div>
                    <p><strong>Método de Pago:</strong> <?php echo ucfirst($metodo_pago); ?></p>
                    <?php if ($metodo_pago === 'yape'): ?>
                        <p><strong>Número de Yape:</strong> <?php echo censurarNumeroYape($numero_yape); ?></p>
                    <?php elseif ($metodo_pago === 'efectivo'): ?>
                        <p><strong>Monto Recibido:</strong> S/. <?php echo number_format($monto_efectivo, 2); ?></p>
                        <p><strong>Vuelto:</strong> S/. <?php echo number_format($monto_efectivo - $totalMonto, 2); ?></p>
                    <?php endif; ?>
                    <p><strong>Total a Pagar:</strong> S/. <?php echo number_format($totalMonto, 2); ?></p>
                </div>
                <h2>Productos Comprados</h2>
                <table class="product-table">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Precio Unitario</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($productosComprados as $producto): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
                                <td><?php echo htmlspecialchars($producto['cantidad']); ?></td>
                                <td>S/. <?php echo number_format($producto['precio'], 2); ?></td>
                                <td>S/. <?php echo number_format($producto['precio'] * $producto['cantidad'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="qr-code">
                    <h3>Escanea el Código QR</h3>
                    <img src="<?php echo $qrCodeUrl; ?>" alt="Código QR">
                </div>
                <div class="text-center mt-4">
                    <a href="ver_carrito.php" class="btn btn-primary">Regresar al Carrito</a>
                    <a href="cli_aditivos.php" class="btn btn-secondary">Continuar Comprando</a>
                </div>
            </div>
        </div>
    </body>
    </html>
    <?php

    unset($_SESSION['carrito']); // Limpiar el carrito después de la compra
    exit();
}
?>
