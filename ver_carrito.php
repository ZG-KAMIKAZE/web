<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>  
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Carrito</title>
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1>Tu Carrito de Compras</h1>

        <?php if (isset($_SESSION['mensaje'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['mensaje']; unset($_SESSION['mensaje']); ?></div>
        <?php endif; ?>

        <table class="table">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Precio</th>
                    <th>Cantidad</th>
                    <th>Total</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (isset($_SESSION['carrito']) && count($_SESSION['carrito']) > 0): ?>
                    <?php 
                    $totalGeneral = 0; 
                    foreach ($_SESSION['carrito'] as $producto): 
                        $total = $producto['precio'] * $producto['cantidad'];
                        $totalGeneral += $total;
                    ?>
                        <tr>
                            <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
                            <td>S/. <?php echo number_format($producto['precio'], 2); ?></td>
                            <td><?php echo htmlspecialchars($producto['cantidad']); ?></td>
                            <td>S/. <?php echo number_format($total, 2); ?></td>
                            <td>
                                <form action="eliminar_producto.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($producto['id']); ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan="3" class="text-right"><strong>Total General:</strong></td>
                        <td>S/. <?php echo number_format($totalGeneral, 2); ?></td>
                        <td></td>
                    </tr>
                <?php else: ?>
                    <tr>
                        <td colspan="5">El carrito está vacío.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <a href="cli_aditivos.php" class="btn btn-primary">Seguir comprando</a>
        
        <?php if (isset($_SESSION['carrito']) && count($_SESSION['carrito']) > 0): ?>
            <form action="procesar_compra.php" method="POST" class="mt-3">
                <div class="form-group">
                    <label for="metodo_pago">Método de Pago:</label>
                    <select class="form-control" name="metodo_pago" id="metodo_pago" required>
                        <option value="">Seleccione un método de pago</option>
                        <option value="yape">Yape</option>
                        <option value="efectivo">Efectivo</option>
                    </select>
                </div>
                
                <!-- Campos para Yape -->
                <div id="yape_info" style="display: none;">
                    <div class="form-group">
                        <label for="numero_yape">Número de Yape:</label>
                        <input type="text" class="form-control" name="numero_yape" id="numero_yape">
                    </div>
                </div>

                <!-- Campos para Efectivo -->
                <div id="efectivo_info" style="display: none;">
                    <div class="form-group">
                        <label for="monto_efectivo">Monto a Pagar:</label>
                        <input type="number" class="form-control" name="monto_efectivo" id="monto_efectivo" step="0.01" min="0">
                    </div>
                    <div class="form-group">
                        <label for="vuelto">Vuelto:</label>
                        <input type="text" class="form-control" id="vuelto" readonly>
                    </div>
                </div>

                <button type="submit" class="btn btn-success">Proceder a Pagar</button>
            </form>
        <?php endif; ?>
    </div>

    <script>
        // Variables para JavaScript
        const totalGeneral = <?php echo isset($totalGeneral) ? $totalGeneral : 0; ?>;
        
        // Cambio dinámico de formularios según método de pago
        document.getElementById('metodo_pago').addEventListener('change', function() {
            const yapeInfo = document.getElementById('yape_info');
            const efectivoInfo = document.getElementById('efectivo_info');
            const montoEfectivo = document.getElementById('monto_efectivo');
            const vuelto = document.getElementById('vuelto');

            if (this.value === 'yape') {
                yapeInfo.style.display = 'block';
                efectivoInfo.style.display = 'none';
                montoEfectivo.value = ''; // Limpiar monto
                vuelto.value = ''; // Limpiar vuelto
            } else if (this.value === 'efectivo') {
                yapeInfo.style.display = 'none';
                efectivoInfo.style.display = 'block';
            } else {
                yapeInfo.style.display = 'none';
                efectivoInfo.style.display = 'none';
                montoEfectivo.value = ''; // Limpiar monto
                vuelto.value = ''; // Limpiar vuelto
            }
        });

        // Calcular vuelto automáticamente
        document.getElementById('monto_efectivo').addEventListener('input', function() {
            const monto = parseFloat(this.value) || 0;
            const vuelto = monto - totalGeneral;
            document.getElementById('vuelto').value = vuelto >= 0 ? vuelto.toFixed(2) : "Monto insuficiente";
        });
    </script>
</body>
</html>
