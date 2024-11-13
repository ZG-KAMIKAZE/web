<?php
include 'config.php';

// Obtener los valores del filtro y el buscador (si existen)
$orden = isset($_GET['orden']) && in_array($_GET['orden'], ['asc', 'desc']) ? $_GET['orden'] : 'asc';
$buscar = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';

// Obtener productos de la categoría "Construcción" con el filtro de orden y búsqueda
$categoria_id = 3; 
$sql = "SELECT * FROM productos WHERE categoria_id = $categoria_id";

if ($buscar !== '') {
    $sql .= " AND nombre LIKE '%$buscar%'";
}

$sql .= " ORDER BY nombre $orden"; // Ordenar por nombre (A-Z o Z-A)

$result = $conn->query($sql);

// Obtener categorías (opcional, si deseas mostrar en el menú)
$resultCategorias = $conn->query("SELECT * FROM categorias");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Construcción - Sistema de Ventas Ferretería Clavijo</title>

    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/estilo9.css">
    <link rel="stylesheet" type="text/css" href="css/estilo08.css">
    <script type="text/javascript" src="js/jquery-3.1.1.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.min.js"></script>

    <style>
        /* Estilo de la tabla */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }

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
                            <li><a href="vi_banos.php" target="principal">BAÑOS</a></li>
                            <li class="divider"></li>
                            <li><a href="vi_construccion.php">CONSTRUCCIÓN</a></li>
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
        <!-- Formulario de filtro y búsqueda -->
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
        <table>
            <thead>
                <tr>
                    <th>Imagen</th>
                    <th>Nombre</th>
                    <th>Precio</th>
                    <th>Stock disponible</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><img src="<?php echo $row['imagen']; ?>" alt="<?php echo $row['nombre']; ?>" style="width: 100px; height: auto;"></td>
                    <td><?php echo $row['nombre']; ?></td>
                    <td>S/. <?php echo number_format($row['precio'], 2); ?></td>
                    <td><?php echo $row['stock']; ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
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
