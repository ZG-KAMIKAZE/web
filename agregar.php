<?php
include 'config.php';

// Agregar categoría
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_categoria'])) {
    $nombre_categoria = $conn->real_escape_string($_POST['nombre_categoria']);
    
    // Verificar si la categoría ya existe
    $result_categoria = $conn->query("SELECT * FROM categorias WHERE nombre = '$nombre_categoria'");
    
    if ($result_categoria->num_rows > 0) {
        echo "<div class='error'>La categoría '$nombre_categoria' ya existe.</div>";
    } else {
        // Insertar nueva categoría en la base de datos
        $stmt_categoria = $conn->prepare("INSERT INTO categorias (nombre) VALUES (?)");
        $stmt_categoria->bind_param("s", $nombre_categoria);
        
        if ($stmt_categoria->execute()) {
            echo "<div class='success'>Categoría agregada exitosamente.</div>";
        } else {
            echo "<div class='error'>Error al agregar categoría: " . $stmt_categoria->error . "</div>";
        }
    }
}

// Agregar producto
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add'])) {
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $precio = (float) $_POST['precio'];
    $stock = (int) $_POST['stock'];
    $categoria_id = (int) $_POST['categoria_id'];

    // Manejar la subida de la imagen
    $imagen = $_FILES['imagen'];
    $rutaImagen = 'img/' . basename($imagen['name']);

    // Validar si la imagen se subió correctamente
    if (move_uploaded_file($imagen['tmp_name'], $rutaImagen)) {
        // Usamos una consulta preparada para seguridad
        $stmt = $conn->prepare("INSERT INTO productos (nombre, precio, stock, imagen, categoria_id) VALUES (?, ?, ?, ?, ?)");
        
        // Verificar si la preparación de la consulta fue exitosa
        if ($stmt === false) {
            die("Error en la preparación de la consulta: " . $conn->error);
        }

        $stmt->bind_param("sdssi", $nombre, $precio, $stock, $rutaImagen, $categoria_id);

        if ($stmt->execute()) {
            header('Location: agregar.php?categoria_id=' . $categoria_id);
            exit();
        } else {
            echo "<div class='error'>Error al agregar producto: " . $stmt->error . "</div>";
        }
    } else {
        echo "<div class='error'>Error al subir la imagen.</div>";
    }
}

// Eliminar producto
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $sql = "DELETE FROM productos WHERE id = $id";
    $conn->query($sql);
}

// Obtener productos filtrados por categoría
$categoria_id = isset($_GET['categoria_id']) ? (int)$_GET['categoria_id'] : 0;
$result = $conn->query("SELECT * FROM productos" . ($categoria_id ? " WHERE categoria_id = $categoria_id" : ""));

// Obtener categorías
$resultCategorias = $conn->query("SELECT * FROM categorias");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/editar.css">
    <title>Sistema de Ventas Ferretería Clavijo</title>
    <style>
        /* Estilos del modal */
        .modal {
            display: none; /* Oculto por defecto */
            position: fixed; /* Posición fija */
            z-index: 1000; /* Sobre el contenido */
            left: 0;
            top: 0;
            width: 100%; /* Ancho completo */
            height: 100%; /* Alto completo */
            overflow: auto; /* Permitir desplazamiento si es necesario */
            background-color: rgba(0, 0, 0, 0.5); /* Fondo negro con opacidad */
        }

        .modal-content {
            background-color: #ffffff;
            margin: 10% auto; /* Centrando el modal */
            padding: 20px; /* Ajuste en el padding del contenido */
            border: 1px solid #888;
            width: 400px; /* Ancho del modal ajustado */
            max-width: 90%; /* Ancho máximo */
            border-radius: 8px; /* Bordes redondeados */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Sombra del modal */
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px; /* Tamaño del ícono de cerrar ajustado */
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        /* Estilos de botones */
        .btn-modal {
            padding: 10px 15px; /* Tamaño */
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-bottom: 20px;
            font-size: 16px; /* Tamaño de fuente */
        }

        .btn-modal:hover {
            background-color: #0056b3;
        }

        /* Estilos de la tabla */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px; /* Espacio superior de la tabla */
        }

        th, td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
        }

        .btn-delete, .btn-edit {
            padding: 5px 10px;
            margin: 0 5px;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn-delete {
            background-color: #e74c3c;
        }

        .btn-edit {
            background-color: #3498db;
        }

        .btn-delete:hover {
            background-color: #c0392b;
        }

        .btn-edit:hover {
            background-color: #2980b9;
        }

        .error {
            color: red;
            margin-top: 20px; /* Margen superior */
        }

        .success {
            color: green;
            margin-top: 20px; /* Margen superior */
        }

        /* Estilos del botón de regresar */
        .btn-regresar {
            margin-top: 20px;
            padding: 10px 15px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn-regresar:hover {
            background-color: #218838;
        }

        input[type="text"],
        input[type="number"],
        input[type="file"],
        select {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 4px; /* Bordes redondeados */
        }

        button[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        button[type="submit"]:hover {
            background-color: #0056b3;
        }
    </style>
    <script>
        function openModal(modalId) {
            document.getElementById(modalId).style.display = "block";
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = "none";
        }

        // Cerrar el modal cuando se hace clic fuera de él
        window.onclick = function(event) {
            if (event.target.className === 'modal') {
                closeModal('modalCategoria');
                closeModal('modalProducto');
            }
        }
    </script>
</head>
<body>
    <div class="container">
        <h1>Productos</h1>

        <!-- Botones para abrir los modales -->
        <button onclick="openModal('modalCategoria')" class="btn-modal">Agregar Categoría</button>
        <button onclick="openModal('modalProducto')" class="btn-modal">Agregar Producto</button>

        <!-- Botón para regresar al index.html -->
        <button onclick="window.location.href='administrador.html'" class="btn-regresar">Regresar al Inicio</button>

        <!-- Modal para agregar categoría -->
        <div id="modalCategoria" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal('modalCategoria')">&times;</span>
                <h2>Agregar Categoría</h2>
                <form method="POST" action="">
                    <input type="text" name="nombre_categoria" placeholder="Nombre de la categoría" required>
                    <button type="submit" name="add_categoria">Agregar</button>
                </form>
            </div>
        </div>

        <!-- Modal para agregar producto -->
        <div id="modalProducto" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal('modalProducto')">&times;</span>
                <h2>Agregar Producto</h2>
                <form method="POST" action="" enctype="multipart/form-data">
                    <input type="text" name="nombre" placeholder="Nombre del producto" required>
                    <input type="number" name="precio" placeholder="Precio" step="0.01" required>
                    <input type="number" name="stock" placeholder="Stock" required>
                    <input type="file" name="imagen" accept="image/*" required>
                    <select name="categoria_id" required>
                        <option value="">Seleccione una categoría</option>
                        <?php while ($categoria = $resultCategorias->fetch_assoc()): ?>
                            <option value="<?= $categoria['id'] ?>"><?= $categoria['nombre'] ?></option>
                        <?php endwhile; ?>
                    </select>
                    <button type="submit" name="add">Agregar</button>
                </form>
            </div>
        </div>

        <!-- Tabla de productos -->
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Precio</th>
                    <th>Stock</th>
                    <th>Imagen</th>
                    <th>Categoría</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($producto = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $producto['id'] ?></td>
                        <td><?= $producto['nombre'] ?></td>
                        <td><?= number_format($producto['precio'], 2) ?></td>
                        <td><?= $producto['stock'] ?></td>
                        <td><img src="<?= $producto['imagen'] ?>" alt="Imagen de <?= $producto['nombre'] ?>" width="100"></td>
                        <td><?= $producto['categoria_id'] ?></td>
                        <td>
                            <a href="edit.php?id=<?= $producto['id'] ?>" class="btn-edit">Editar</a>
                            <a href="?delete=<?= $producto['id'] ?>" class="btn-delete" onclick="return confirm('¿Estás seguro de que deseas eliminar este producto?');">Eliminar</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
