<?php
// Incluir la conexión a la base de datos
include('conexion.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener el nombre de la nueva categoría desde el formulario
    $categoria = $_POST['categoriaInput'];

    // Preparar la consulta para insertar la nueva categoría en la base de datos
    $sql = "INSERT INTO categorias (nombre_categoria) VALUES (:categoria)";
    $stmt = $conexion->prepare($sql);

    // Ejecutar la consulta con el valor del formulario
    if ($stmt->execute([':categoria' => $categoria])) {
        echo "Categoría agregada correctamente";
    } else {
        echo "Error al agregar la categoría";
    }

    // Redirigir al usuario de vuelta a la página principal
    header("Location: index.php"); 
    exit();
}
?>
