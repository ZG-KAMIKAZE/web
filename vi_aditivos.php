<?php
include 'config.php';

// Obtener los valores del filtro y el buscador (si existen)
$orden = isset($_GET['orden']) && in_array($_GET['orden'], ['asc', 'desc']) ? $_GET['orden'] : 'asc';
$buscar = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';

// Construir la consulta SQL dinámicamente
$categoria_id = 1; // ID de la categoría Aditivos
$sql = "SELECT * FROM productos WHERE categoria_id = $categoria_id";
if ($buscar !== '') {
    $sql .= " AND nombre LIKE '%$buscar%'";
}
$sql .= " ORDER BY nombre $orden";

// Ejecutar la consulta
$result = $conn->query($sql);
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
        .footer-style {
            background-color: rgba(255, 165, 0, 0.8);
            padding: 10px 0;
            color: #333;
        }
        .footer-col h3 {
            font-weight: bold;
            margin-bottom: 10px;
        }
        .footer-col p {
            margin-bottom: 5px;
        }
        .social-icon {
            color: #007bff;
            text-decoration: none;
        }
        .social-icon:hover {
            text-decoration: underline;
        }
        .footer-col a {
            display: block;
            margin-bottom: 3px;
        }
        .social-icon i {
            margin-right: 5px;
        }
        @media (max-width: 768px) {
            .footer-col {
                margin-bottom: 10px;
            }
        }
        .table-responsive {
            margin-top: 20px;
        }
    </style>
</head>
<body>
<header>
    <section class="jumbotron">
        <div class="container">
            <h1>FERRETERIA CLAVIJO</h1>
            <p></p>
        </div>
    </section>
    <nav class="navbar navbar-inverse navbar-static-top" role="navigation">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" 
                        data-toggle="collapse" 
                        data-target="#navegacion-sw">
                    <span class="sr-only">Desplegar/Ocultar-Menú</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a href="index.html" class="navbar-brand">FERRETERIA</a>
            </div>
            <div class="collapse navbar-collapse" id="navegacion-sw">
                <ul class="nav navbar-nav">
                    <li class="active"><a href="#">INICIO</a></li>
                    <li><a href="introduccion.html">NOSOTROS</a></li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button">TODAS LAS CATEGORIAS <span class="caret"></span></a>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="vi_aditivos.php" target="principal">ADITIVOS</a></li>
                            <li class="divider"></li>
                            <li><a href="vi_baño.php">BAÑO</a></li>
                            <li class="divider"></li>
                            <li><a href="vi_construccion.php">CONSTRUCCION</a></li>
                        </ul>
                    </li>
                    <li><a href="contacto.html">CONTACTO</a></li>
                    <li><a href="login.php">MI CUENTA</a></li>
                </ul>
            </div>
        </div>
    </nav>
</header>

<br>

<section class="main">
    <div class="container">
        <!-- Filtro y buscador -->
        <form method="GET" action="" class="form-inline mb-4">
            <div class="form-group">
                <label for="buscar">Buscar producto:</label>
                <input type="text" name="buscar" id="buscar" class="form-control mx-2" placeholder="Nombre del producto" value="<?php echo htmlspecialchars($buscar); ?>">
            </div>
            <div class="form-group">
                <label for="orden">Ordenar por:</label>
                <select name="orden" id="orden" class="form-control mx-2">
                    <option value="asc" <?php echo $orden === 'asc' ? 'selected' : ''; ?>>A-Z</option>
                    <option value="desc" <?php echo $orden === 'desc' ? 'selected' : ''; ?>>Z-A</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Aplicar</button>
        </form>
        
        <!-- Tabla de productos -->
        <div class="table-responsive">
            <?php if ($result->num_rows > 0): ?>
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Stock disponible</th>
                            <th>Imagen</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['nombre']; ?></td>
                            <td><?php echo $row['stock']; ?></td>
                            <td><img src="<?php echo $row['imagen']; ?>" alt="<?php echo $row['nombre']; ?>" width="100"></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-center">No se encontraron productos.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<footer class="text-center footer-style">
    <div class="container">
        <div class="row">
            <div class="col-md-3 footer-col">
                <h3>Contactemos</h3>
                <p>
                    Visítanos o llámanos:<br />
                    Revisa aquí los horarios de nuestros módulos a nivel nacional
                </p>
            </div>
            <div class="col-md-3 footer-col">
                <h3>Dirección</h3>
                <p>
                    PIURA - Perú <br />
                    Av. La Encantada 123
                </p>
            </div>
            <div class="col-md-3 footer-col">
                <h3>Social</h3>
                <p><a href="#" class="social-icon"><i class="fa fa-facebook"></i>Facebook</a></p>
                <p><a href="#" class="social-icon"><i class="fa fa-twitter"></i>Twitter</a></p>
                <p><a href="#" class="social-icon"><i class="fa fa-instagram"></i>Instagram</a></p>
            </div>
            <div class="col-md-3 footer-col">
                <h3>Políticas</h3>
                <p><a href="#">Términos y condiciones</a></p>
                <p><a href="#">Política de privacidad</a></p>
            </div>
        </div>
        <p>&copy; 2023 FERRETERIA CLAVIJO. Todos los derechos reservados.</p>
    </div>
</footer>
</body>
</html>