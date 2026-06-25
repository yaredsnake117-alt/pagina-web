<?php
session_start();

// Si ya está logueado, redirigir según su rol
if(isset($_SESSION['usuario_id'])) {
    if($_SESSION['usuario_rol'] == 'admin') {
        header("Location: admin_dashboard.php");
    } elseif($_SESSION['usuario_rol'] == 'contador') {
        header("Location: contador_dashboard.php");
    } else {
        header("Location: usuario_dashboard.php");
    }
    exit();
}

include 'config.php';

$error = '';

if(isset($_POST['login'])) {
    $nombre_usuario = trim($_POST['usuario']);
    $password = trim($_POST['password']);
    
    $sql = "SELECT * FROM usuarios WHERE nombre_usuario = ? AND password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $nombre_usuario, $password);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows == 1) {
        $usuario = $result->fetch_assoc();
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nombre'] = $usuario['nombre_usuario'];
        $_SESSION['usuario_completo'] = $usuario['nombre_completo'];
        $_SESSION['usuario_rol'] = $usuario['rol'];
        
        // Redirigir según el rol
        if($usuario['rol'] == 'admin') {
            header("Location: admin_dashboard.php");
        } elseif($usuario['rol'] == 'contador') {
            header("Location: contador_dashboard.php");
        } else {
            header("Location: usuario_dashboard.php");
        }
        exit();
    } else {
        $error = " Usuario o contraseña incorrectos";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-container {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            width: 350px;
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
            background: #3498db;
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
            text-align: center;
        }
        .usuarios-info {
            margin-top: 1rem;
            padding: 10px;
            background: #ecf0f1;
            border-radius: 6px;
            font-size: 12px;
        }
    </style>
</head>
<body>
<div class="login-container">
    <div style="text-align: center; margin-bottom: 50px;">
        <img src="image/Prest_logo.png" alt="Logo" style="height: 160px; width:auto;">
    
    
</div>
    <h2> Iniciar Sesión</h2>
    
    <form method="POST" action="">
        <div class="campo">
            <label>Usuario</label>
            <input type="text" name="usuario" required>
        </div>
        
        <div class="campo">
            <label>Contraseña</label>
            <input type="password" name="password" required>
        </div>
        
        <button type="submit" name="login">Ingresar</button>
        
        <?php if($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
    </form>
    
    <div class="usuarios-info">
        <strong>📌 Usuarios de prueba:</strong><br>
        admin / 1234 → Panel de Administrador<br>
        contador / 1234 → Panel de Contador<br>
        jared / 1234 → Panel de Usuario
    </div>
</div>
</body>
</html>