<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ferreteria_clavijo"; 

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $contrasena = password_hash($_POST['contrasena'], PASSWORD_DEFAULT); // Hashear la contraseña
    $rol = $_POST['rol'];

    $stmt = $conn->prepare("INSERT INTO usuarios (nombre, correo, contrasena, rol) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $nombre, $correo, $contrasena, $rol);

    if ($stmt->execute()) {
        $mensaje = "Usuario registrado exitosamente.";
    } else {
        $mensaje = "Error al registrar usuario: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #e0f7fa;
            padding: 50px;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .registro-container {
            max-width: 400px;
            width: 100%;
            background: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #00796b;
        }
        label {
            margin-bottom: 5px;
            color: #00796b;
        }
        input[type="text"], input[type="email"], input[type="password"], select {
            width: 100%;
            padding: 10px;
            margin: 5px 0 15px;
            border: 2px solid #00796b;
            border-radius: 5px;
            transition: border 0.3s;
        }
        input[type="text"]:focus, input[type="email"]:focus, input[type="password"]:focus, select:focus {
            border-color: #004d40;
            outline: none;
        }
        input[type="submit"] {
            background: #00796b;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: background 0.3s;
            width: 100%;
        }
        input[type="submit"]:hover {
            background: #004d40;
        }
        .mensaje {
            color: green;
            text-align: center;
        }
        .login-btn {
            display: block;
            text-align: center;
            margin-top: 10px;
            color: #00796b;
            text-decoration: none;
            font-weight: bold;
        }
        .login-btn:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="registro-container">
        <h2>Registro de Usuario</h2>
        <form method="POST" action="">
            <label for="nombre">Nombre:</label>
            <input type="text" name="nombre" required>
            <label for="correo">Correo:</label>
            <input type="email" name="correo" required>
            <label for="contrasena">Contraseña:</label>
            <input type="password" name="contrasena" required>
            <label for="rol">Rol:</label>
            <select name="rol" required>
                <option value="administrador">Administrador</option>
                <option value="cliente">Cliente</option>
            </select>
            <input type="submit" value="Registrar">
        </form>
        <a class="login-btn" href="login.php">Ya tengo una cuenta</a>
        <?php if (isset($mensaje)) echo "<p class='mensaje'>$mensaje</p>"; ?>
    </div>
</body>
</html>
