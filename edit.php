<?php
include 'config.php';

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $result = $conn->query("SELECT * FROM productos WHERE id = $id");
    $producto = $result->fetch_assoc();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $precio = (float)$_POST['precio'];
    $stock = (int)$_POST['stock'];
    $categoria_id = (int)$_POST['categoria_id'];

    // Manejar la subida de la imagen
    $imagen = $_FILES['imagen'];
    $rutaImagen = $producto['imagen']; // Mantener la imagen existente

    // Verificar si se subió una nueva imagen
    if ($imagen['size'] > 0) {
        $rutaImagen = 'img/' . basename($imagen['name']);
        move_uploaded_file($imagen['tmp_name'], $rutaImagen);
    }

    $stmt = $conn->prepare("UPDATE productos SET nombre=?, precio=?, stock=?, imagen=?, categoria_id=? WHERE id=?");
    $stmt->bind_param("sdssii", $nombre, $precio, $stock, $rutaImagen, $categoria_id, $id);

    if ($stmt->execute()) {
        header('Location: agregar.php');
        exit();
    } else {
        echo "<div class='error'>Error al actualizar producto: " . $stmt->error . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/edit.css">
    <title>Editar Producto</title>
</head>
<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f9f9f9;
        margin: 0;
        padding: 20px;
    }
    
    h1 {
        text-align: center;
        color: #333;
    }
    
    form {
        background-color: #fff;
        border-radius: 5px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        max-width: 500px;
        margin: auto;
        padding: 20px;
    }

    label {
        display: block;
        margin-bottom: 5px;
        color: #555;
    }
    
    input[type="text"],
    input[type="number"],
    input[type="file"] {
        width: 100%;
        padding: 10px;
        margin-bottom: 20px;
        border: 1px solid #ccc;
        border-radius: 3px;
        box-sizing: border-box;
    }

    button {
        width: 100%;
        padding: 10px;
        background-color: #5cb85c;
        border: none;
        border-radius: 3px;
        color: #fff;
        font-size: 16px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    button:hover {
        background-color: #4cae4c;
    }

    a {
        display: block;
        text-align: center;
        margin-top: 20px;
        color: #007bff;
        text-decoration: none;
    }

    a:hover {
        text-decoration: underline;
    }
</style>
<body>
    <div class="container">
        <h1>Editar Producto</h1>
        <form method="POST" enctype="multipart/form-data">
            <input type="text" name="nombre" value="<?php echo $producto['nombre']; ?>" required>
            <input type="number" step="0.01" name="precio" value="<?php echo $producto['precio']; ?>" required>
            <input type="number" name="stock" value="<?php echo $producto['stock']; ?>" required>
            <select name="categoria_id" required>
                <option value="">Seleccionar categoría</option>
                <?php
                $resultCategorias = $conn->query("SELECT * FROM categorias");
                while ($categoria = $resultCategorias->fetch_assoc()) {
                    $selected = $categoria['id'] == $producto['categoria_id'] ? 'selected' : '';
                    echo "<option value='{$categoria['id']}' $selected>{$categoria['nombre']}</option>";
                }
                ?>
            </select>
            <input type="file" name="imagen" accept="image/*">
            <button type="submit" name="update">Actualizar</button>
        </form>
        <a href="agregar.php">Volver a la lista de productos</a>
    </div>
</body>
</html>
