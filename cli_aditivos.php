<?php
include 'config.php';

// Obtener productos de la categoría "Aditivos"
$categoria_id = 1; 
$result = $conn->query("SELECT * FROM productos WHERE categoria_id = $categoria_id");

// Obtener categorías (opcional, si deseas mostrar en el menú)
$resultCategorias = $conn->query("SELECT * FROM categorias");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aditivos - Sistema de Ventas Ferretería Clavijo</title>
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/estilo9.css">
    <link rel="stylesheet" type="text/css" href="css/estilo08.css">
    <script type="text/javascript" src="js/jquery-3.1.1.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.min.js"></script>
    <style>
        .product-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .product-table th, .product-table td {
            text-align: center;
            padding: 15px;
        }
        .product-table th {
            background-color: #f8f9fa;
        }
        .product-card {
            border: none; /* Sin bordes visibles */
            border-radius: 5px;
            transition: transform 0.2s;
            margin: 0; /* Sin margen para que no haya espacios */
        }
        .product-card:hover {
            transform: scale(1.05);
        }
        .product-image {
            max-width: 100%;
            height: auto;
        }
        .quantity-control {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 10px;
        }
        .quantity-control button {
            width: 30px;
            height: 30px;
        }
    </style>
</head>
<body>
<header>
    <section class="jumbotron">
        <div class="container">
            <h1>FERRETERIA CLAVIJO</h1>
        </div>
    </section>
    <nav class="navbar navbar-inverse navbar-static-top" role="navigation">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navegacion-sw">
                    <span class="sr-only">Desplegar/Ocultar-Menú</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a href="cliente.html" class="navbar-brand">FERRETERIA</a>
            </div>
            <div class="collapse navbar-collapse" id="navegacion-sw">
                <ul class="nav navbar-nav">
                    <li class="active"><a href="#">INICIO</a></li>
                    <li><a href="introduccion.html">NOSOTROS</a></li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button">TODAS LAS CATEGORIAS <span class="caret"></span></a>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="cli_aditivos.php">ADITIVOS</a></li>
                            <li class="divider"></li>
                            <li><a href="cli_baño.php">BAÑO</a></li>
                            <li class="divider"></li>
                            <li><a href="cli_construccion.php">CONSTRUCCIÓN</a></li>
                        </ul>
                    </li>
                    <li><a href="contacto.html">CONTACTO</a></li>
                    <li><a href="login.php">MI CUENTA</a></li>
                </ul>
            </div>
        </div>
    </nav>
</header>

<section class="main">
    <div class="container">
        <table class="product-table">
            <thead>
                <tr>
                    <th>Imagen</th>
                    <th>Nombre</th>
                    <th>Precio</th>
                    <th>Stock disponible</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><img src="<?php echo $row['imagen']; ?>" alt="<?php echo $row['nombre']; ?>" class="product-image"></td>
                    <td><?php echo $row['nombre']; ?></td>
                    <td>S/. <?php echo number_format($row['precio'], 2); ?></td>
                    <td><?php echo $row['stock']; ?></td>
                    <td>
                        <button type="button" class="btn btn-info" data-toggle="modal" data-target="#productoModal" 
                                data-id="<?php echo $row['id']; ?>" 
                                data-nombre="<?php echo $row['nombre']; ?>" 
                                data-precio="<?php echo $row['precio']; ?>" 
                                data-stock="<?php echo $row['stock']; ?>" 
                                data-imagen="<?php echo $row['imagen']; ?>">Ver detalles</button>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</section>

<!-- Modal -->
<div class="modal fade" id="productoModal" tabindex="-1" role="dialog" aria-labelledby="productoModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="productoModalLabel">Detalles del producto</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <img src="" alt="" class="img-fluid" id="modal-imagen">
                <h5 id="modal-nombre"></h5>
                <p>Precio: S/. <span id="modal-precio"></span></p>
                <p>Stock disponible: <span id="modal-stock"></span></p>
                <div class="quantity-control">
                    <button type="button" class="btn btn-secondary" id="btn-menos">-</button>
                    <input type="number" id="cantidad" value="1" min="1" style="width: 50px; text-align: center;">
                    <button type="button" class="btn btn-secondary" id="btn-mas">+</button>
                </div>
                <div class="alert alert-danger d-none" id="stock-alert">No puedes agregar más de <span id="max-stock"></span> productos al carrito.</div>
            </div>
            <div class="modal-footer">
                <form action="procesar_carrito.php" method="POST" id="agregarCarritoForm">
                    <input type="hidden" name="id" id="modal-id">
                    <input type="hidden" name="nombre" id="modal-nombre-hidden">
                    <input type="hidden" name="precio" id="modal-precio-hidden">
                    <input type="hidden" name="cantidad" id="modal-cantidad" value="1">
                    <button type="submit" class="btn btn-success" id="btn-agregar">Agregar al carrito</button>
                </form>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Seguir comprando</button>
                <a href="ver_carrito.php" class="btn btn-primary">Ver carrito</a>
            </div>
        </div>
    </div>
</div>

<footer class="text-center footer-style">
    <div class="container">
        <div class="row">
            <div class="col-md-3 footer-col">
                <h3>Contactemos</h3>
                <p>Visítanos o llámanos:<br />Revisa aquí los horarios de nuestros módulos a nivel nacional</p>
            </div>
            <div class="col-md-3 footer-col">
                <h3>Dirección</h3>
                <p>PIURA - Perú <br />Av. La Encantada 123</p>
            </div>
            <div class="col-md-3 footer-col">
                <h3>Social</h3>
                <p><a href="#" class="social-icon"><i class="fa fa-facebook"></i>Facebook</a></p>
                <p><a href="#" class="social-icon"><i class="fa fa-twitter"></i>Twitter</a></p>
                <p><a href="#" class="social-icon"><i class="fa fa-instagram"></i>Instagram</a></p>
            </div>
            <div class="col-md-3 footer-col">
                <h3>Información</h3>
                <p><a href="#">Sobre nosotros</a></p>
                <p><a href="#">Política de privacidad</a></p>
                <p><a href="#">Términos y condiciones</a></p>
            </div>
        </div>
    </div>
</footer>

<script type="text/javascript">
$(document).ready(function() {
    $('#productoModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget); // Botón que activó el modal
        var id = button.data('id');
        var nombre = button.data('nombre');
        var precio = button.data('precio');
        var stock = button.data('stock');
        var imagen = button.data('imagen');

        // Actualizar el modal con la información del producto
        var modal = $(this);
        modal.find('#modal-id').val(id);
        modal.find('#modal-nombre').text(nombre);
        modal.find('#modal-precio').text(precio);
        modal.find('#modal-stock').text(stock);
        modal.find('#modal-imagen').attr('src', imagen);
        modal.find('#modal-nombre-hidden').val(nombre);
        modal.find('#modal-precio-hidden').val(precio);

        // Lógica para controlar la cantidad
        var cantidadInput = modal.find('#cantidad');
        cantidadInput.val(1); // Inicializar a 1
        modal.find('#modal-cantidad').val(1); // Inicializar en el modal también
        modal.find('#max-stock').text(stock); // Mostrar stock máximo en la alerta

        // Manejar el botón de más
        modal.find('#btn-mas').off('click').on('click', function() {
            var cantidadActual = parseInt(cantidadInput.val());
            if (cantidadActual < stock) { // Verifica que la cantidad no exceda el stock
                cantidadInput.val(cantidadActual + 1);
                modal.find('#modal-cantidad').val(cantidadActual + 1);
                modal.find('#stock-alert').addClass('d-none'); // Ocultar alerta si es válida
            } else {
                modal.find('#stock-alert').removeClass('d-none'); // Mostrar alerta
            }
        });

        // Manejar el botón de menos
        modal.find('#btn-menos').off('click').on('click', function() {
            var cantidadActual = parseInt(cantidadInput.val());
            if (cantidadActual > 1) {
                cantidadInput.val(cantidadActual - 1);
                modal.find('#modal-cantidad').val(cantidadActual - 1);
                modal.find('#stock-alert').addClass('d-none'); // Ocultar alerta si es válida
            }
        });

        // Manejar el envío del formulario
        $('#agregarCarritoForm').off('submit').on('submit', function() {
            var cantidadFinal = parseInt(cantidadInput.val());
            if (cantidadFinal > stock) {
                alert("No puedes agregar más de " + stock + " unidades al carrito."); // Mensaje de alerta
                return false; // Cancelar envío del formulario
            }
            return true; // Permitir envío del formulario
        });
    });
});
</script>
</body>
</html>

