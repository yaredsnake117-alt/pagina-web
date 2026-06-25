<?php
session_start();
include 'verificar_rol.php';

// Solo contador puede ver esta página
solo_contador();

include 'config.php';

$usuario_nombre = $_SESSION['usuario_completo'];

// Estadísticas financieras
$total_monto = $conn->query("SELECT SUM(monto_solicitado) as total FROM solicitudes")->fetch_assoc()['total'];
$total_aprobados = $conn->query("SELECT SUM(monto_solicitado) as total FROM solicitudes WHERE estado = 'aprobado'")->fetch_assoc()['total'];
$total_pendientes = $conn->query("SELECT COUNT(*) as total FROM solicitudes WHERE estado = 'pendiente'")->fetch_assoc()['total'];

$solicitudes = $conn->query("SELECT * FROM solicitudes ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Contador</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f0f2f5;
            margin: 0;
            padding: 0;
        }
        .header {
            background: #16a085;
            color: white;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .container {
            padding: 2rem;
        }
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            text-align: center;
        }
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #16a085;
        }
        table {
            width: 100%;
            background: white;
            border-radius: 12px;
            overflow: hidden;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #16a085;
            color: white;
        }
        .btn-logout {
            background: #e74c3c;
            color: white;
            padding: 0.5rem 1rem;
            text-decoration: none;
            border-radius: 6px;
        }
        .badge-contador {
            background: #16a085;
            padding: 5px 10px;
            border-radius: 4px;
        }
    </style>
</head>
<body>
<div class="header">
    <h1> Panel de Contador</h1>
    <div>
        <span class="badge-contador">Contador: <?php echo $usuario_nombre; ?></span>
        <a href="logout.php" class="btn-logout">Cerrar Sesión</a>
    </div>
</div>

<div class="container">
    <div class="stats">
        <div class="stat-card">
            <div class="stat-number">$<?php echo number_format($total_monto, 0); ?></div>
            <div>Monto Total Solicitado</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">$<?php echo number_format($total_aprobados, 0); ?></div>
            <div>Monto Aprobado</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?php echo $total_pendientes; ?></div>
            <div>Solicitudes Pendientes</div>
        </div>
    </div>
    
    <h2> Reporte de Solicitudes</h2>
    
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Fecha</th>
                <th>Solicitante</th>
                <th>Monto</th>
                <th>Plazos</th>
                <th>Pago Mensual</th>
                <th>Ingreso Mensual</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $solicitudes->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo date('d/m/Y', strtotime($row['fecha_registro'])); ?></td>
                <td><?php echo $row['nombre_completo']; ?></td>
                <td>$<?php echo number_format($row['monto_solicitado'], 2); ?></td>
                <td><?php echo $row['plazos']; ?> meses</td>
                <td>$<?php echo number_format($row['pago_mensual'], 2); ?></td>
                <td>$<?php echo number_format($row['ingreso_bruto_mensual'], 2); ?></td>
                <td><?php echo ucfirst($row['estado'] ?? 'pendiente'); ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>