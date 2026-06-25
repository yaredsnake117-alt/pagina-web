<?php
session_start();

if(!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

include 'config.php';

$usuario_id = $_SESSION['usuario_id'];
$usuario_rol = $_SESSION['usuario_rol'];

// Si es admin, ve todas las solicitudes; si no, solo las suyas
if($usuario_rol == 'admin') {
    $solicitudes = $conn->query("SELECT * FROM solicitudes ORDER BY id DESC");
} else {
    $solicitudes = $conn->query("SELECT * FROM solicitudes WHERE usuario_id = $usuario_id ORDER BY id DESC");
}

$total_solicitudes = $solicitudes->num_rows;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi Panel - Préstamos</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            background: #f0f2f5;
        }
        .header {
            background: #2c3e50;
            color: white;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .logout-btn {
            background: #e74c3c;
            color: white;
            padding: 0.5rem 1rem;
            text-decoration: none;
            border-radius: 6px;
        }
        .container {
            padding: 2rem;
        }
        .bienvenida {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        table {
            width: 100%;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #3498db;
            color: white;
        }
        .btn-nueva {
            background: #27ae60;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 6px;
            display: inline-block;
            margin-bottom: 1rem;
        }
        .estado-pendiente {
            background: #f39c12;
            color: white;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 0.8rem;
        }
        .estado-aprobado {
            background: #27ae60;
            color: white;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 0.8rem;
        }
        .estado-rechazado {
            background: #e74c3c;
            color: white;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 0.8rem;
        }
    </style>
</head>
<body>
<div class="header">
    <h1> Sistema de Préstamos</h1>
    <div class="user-info">
        <span> <?php echo $_SESSION['usuario_completo']; ?></span>
        <a href="logout.php" class="logout-btn">Cerrar Sesión</a>
    </div>
</div>

<div class="container">
    <div class="bienvenida">
        <h2>Bienvenido, <?php echo $_SESSION['usuario_completo']; ?> </h2>
        <p>Aquí puedes ver el estado de tus solicitudes de préstamo.</p>
    </div>
    
    <a href="solicitud.php" class="btn-nueva">+ Nueva Solicitud de Préstamo</a>
    
    <h3> Mis Solicitudes (<?php echo $total_solicitudes; ?>)</h3>
    
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Fecha</th>
                <th>Monto</th>
                <th>Plazos</th>
                <th>Pago Mensual</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if($total_solicitudes > 0): ?>
                <?php while($row = $solicitudes->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo date('d/m/Y', strtotime($row['fecha_registro'])); ?></td>
                    <td>$<?php echo number_format($row['monto_solicitado'], 2); ?></td>
                    <td><?php echo $row['plazos']; ?> meses</td>
                    <td>$<?php echo number_format($row['pago_mensual'], 2); ?></td>
                    <td>
                        <?php if($row['estado'] == 'pendiente'): ?>
                            <span class="estado-pendiente"> Pendiente</span>
                        <?php elseif($row['estado'] == 'aprobado'): ?>
                            <span class="estado-aprobado"> Aprobado</span>
                        <?php else: ?>
                            <span class="estado-rechazado"> Rechazado</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="ver_mi_solicitud.php?id=<?php echo $row['id']; ?>">Ver Detalle</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" style="text-align: center;">No tienes solicitudes registradas</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>