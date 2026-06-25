<?php
session_start();
include 'config.php';

$error = '';
$exito = '';

if(isset($_POST['registro'])) {
    $nombre_usuario = trim($_POST['nombre_usuario']);
    $password = trim($_POST['password']);
    $nombre_completo = trim($_POST['nombre_completo']);
    $email = trim($_POST['email']);
    
    // Verificar si el usuario ya existe
    $check = $conn->query("SELECT id FROM usuarios WHERE nombre_usuario = '$nombre_usuario'");
    if($check->num_rows > 0) {
        $error = " El nombre de usuario ya existe, elige otro";
    } else {
        // Insertar nuevo usuario
        $sql = "INSERT INTO usuarios (nombre_usuario, password, nombre_completo, email, rol) VALUES (?, ?, ?, ?, 'usuario')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $nombre_usuario, $password, $nombre_completo, $email);
        
        if($stmt->execute()) {
            $exito = " ¡Registro exitoso! Ahora puedes iniciar sesión.";
        } else {
            $error = " Error al registrar: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Usuario</title>
    <style>
        body {
            font-family: Arial;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            width: 400px;
        }
        h2 {
            text-align: center;
            color: #2c3e50;
        }
        .campo {
            margin-bottom: 1rem;
        }
        label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }
        input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            width: 100%;
            padding: 10px;
            background: #27ae60;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 6px;
            margin-top: 1rem;
        }
        .exito {
            background: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 6px;
            margin-top: 1rem;
        }
        .login-link {
            text-align: center;
            margin-top: 1rem;
        }
    </style>
</head>
<body>
<div class="container">
    <h2> Registro de Usuario</h2>
    
    <form method="POST" action="">
        <div class="campo">
            <label>Nombre de usuario *</label>
            <input type="text" name="nombre_usuario" required>
        </div>
        
        <div class="campo">
            <label>Contraseña *</label>
            <input type="password" name="password" required>
        </div>
        
        <div class="campo">
            <label>Nombre completo *</label>
            <input type="text" name="nombre_completo" required>
        </div>
        
        <div class="campo">
            <label>Correo electrónico</label>
            <input type="email" name="email">
        </div>
        
        <button type="submit" name="registro">Registrarse</button>
        
        <?php if($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if($exito): ?>
            <div class="exito"><?php echo $exito; ?></div>
        <?php endif; ?>
    </form>
    
    <div class="login-link">
        <a href="login.php">← Volver al inicio de sesión</a>
    </div>
</div>
</body>
</html>