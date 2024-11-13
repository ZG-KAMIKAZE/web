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
    $correo = $_POST['correo'];
    $contrasena = $_POST['contrasena'];

    $stmt = $conn->prepare("SELECT id, nombre, contrasena, rol FROM usuarios WHERE correo = ?");
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $nombre, $hashed_password, $rol);
    
    if ($stmt->num_rows > 0) {
        $stmt->fetch();
        
        // Comparar la contraseña hasheada
        if (password_verify($contrasena, $hashed_password)) {
            $_SESSION['usuario_id'] = $id;
            $_SESSION['usuario_nombre'] = $nombre;
            $_SESSION['usuario_rol'] = $rol;
            
            // Redireccionar según el rol
            if ($rol === 'administrador') {
                header("Location: administrador.html");
            } elseif ($rol === 'cliente') {
                header("Location: cliente.html");
            }
            exit();
        } else {
            $error = "Contraseña incorrecta.";
        }
    } else {
        $error = "Usuario no encontrado.";
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
    <title>Iniciar Sesión</title>
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
        .login-container {
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
        input[type="email"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 5px 0 15px;
            border: 2px solid #00796b;
            border-radius: 5px;
            transition: border 0.3s;
        }
        input[type="email"]:focus, input[type="password"]:focus {
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
        .error {
            color: red;
            text-align: center;
        }
        .registro-btn {
            display: block;
            text-align: center;
            margin-top: 10px;
            color: #00796b;
            text-decoration: none;
            font-weight: bold;
        }
        .registro-btn:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Iniciar Sesión</h2>
        <form method="POST" action="">
            <label for="correo">Correo:</label>
            <input type="email" name="correo" required>
            <label for="contrasena">Contraseña:</label>
            <input type="password" name="contrasena" required>
            <input type="submit" value="Iniciar sesión">
        </form>
        <a class="registro-btn" href="registro.php">Registrar Usuario</a>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
    </div>
</body>
</html>
