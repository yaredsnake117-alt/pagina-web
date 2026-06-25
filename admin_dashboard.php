<?php
session_start();
include 'verificar_rol.php';

// Solo admin puede ver esta página
solo_admin();

include 'config.php';

$usuario_nombre = $_SESSION['usuario_completo'];

// ========== ESTADÍSTICAS GENERALES ==========
$total_solicitudes = $conn->query("SELECT COUNT(*) as total FROM solicitudes")->fetch_assoc()['total'];
$total_monto = $conn->query("SELECT SUM(monto_solicitado) as total FROM solicitudes")->fetch_assoc()['total'];
$total_usuarios = $conn->query("SELECT COUNT(*) as total FROM usuarios")->fetch_assoc()['total'];

// Solicitudes por estado
$pendientes = $conn->query("SELECT COUNT(*) as total FROM solicitudes WHERE estado = 'pendiente'")->fetch_assoc()['total'];
$aprobados = $conn->query("SELECT COUNT(*) as total FROM solicitudes WHERE estado = 'aprobado'")->fetch_assoc()['total'];
$rechazados = $conn->query("SELECT COUNT(*) as total FROM solicitudes WHERE estado = 'rechazado'")->fetch_assoc()['total'];

// Monto por mes (últimos 6 meses)
$monto_por_mes = [];
for($i = 5; $i >= 0; $i--) {
    $mes = date('Y-m', strtotime("-$i months"));
    $nombre_mes = date('M', strtotime("-$i months"));
    $result = $conn->query("SELECT SUM(monto_solicitado) as total FROM solicitudes WHERE DATE_FORMAT(fecha_registro, '%Y-%m') = '$mes'");
    $monto = $result->fetch_assoc()['total'] ?? 0;
    $monto_por_mes[] = ['mes' => $nombre_mes, 'monto' => $monto];
}

// Usuarios por rol
$admins = $conn->query("SELECT COUNT(*) as total FROM usuarios WHERE rol = 'admin'")->fetch_assoc()['total'];
$contadores = $conn->query("SELECT COUNT(*) as total FROM usuarios WHERE rol = 'contador'")->fetch_assoc()['total'];
$usuarios_normales = $conn->query("SELECT COUNT(*) as total FROM usuarios WHERE rol = 'usuario'")->fetch_assoc()['total'];

// Top 5 usuarios que más han solicitado
$top_usuarios = $conn->query("SELECT u.nombre_usuario, u.nombre_completo, COUNT(s.id) as total_solicitudes, SUM(s.monto_solicitado) as total_monto 
                              FROM usuarios u 
                              LEFT JOIN solicitudes s ON u.id = s.usuario_id 
                              GROUP BY u.id 
                              ORDER BY total_solicitudes DESC 
                              LIMIT 5");

// Obtener todas las solicitudes
$solicitudes = $conn->query("SELECT s.*, u.nombre_usuario as usuario FROM solicitudes s LEFT JOIN usuarios u ON s.usuario_id = u.id ORDER BY s.id DESC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Administrador</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f0f2f5;
        }
        .header {
            background: linear-gradient(135deg, #1a2a6c, #b21f1f);
            color: white;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .logo {
            font-size: 1.5rem;
            font-weight: bold;
        }
        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .badge-admin {
            background: #fdbb4d;
            color: #1a2a6c;
            padding: 5px 12px;
            border-radius: 20px;
            font-weight: bold;
        }
        .btn-logout {
            background: #e74c3c;
            color: white;
            padding: 0.5rem 1rem;
            text-decoration: none;
            border-radius: 6px;
            transition: 0.3s;
        }
        .btn-logout:hover {
            background: #c0392b;
        }
        .container {
            padding: 2rem;
            max-width: 1400px;
            margin: 0 auto;
        }
        /* Tarjetas de estadísticas */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .stat-icon {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #1a2a6c;
        }
        .stat-label {
            color: #666;
            margin-top: 0.5rem;
        }
        /* Gráficas */
        .charts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .chart-card {
            background: white;
            padding: 1.5rem;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .chart-card h3 {
            color: #1a2a6c;
            margin-bottom: 1rem;
            text-align: center;
        }
        canvas {
            max-height: 300px;
        }
        /* Tablas */
        .table-container {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-top: 2rem;
        }
        .table-header {
            background: #1a2a6c;
            color: white;
            padding: 1rem;
            font-size: 1.2rem;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #f8f9fa;
            color: #1a2a6c;
        }
        tr:hover {
            background: #f5f5f5;
        }
        .estado-pendiente {
            background: #f39c12;
            color: white;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
        }
        .estado-aprobado {
            background: #27ae60;
            color: white;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
        }
        .estado-rechazado {
            background: #e74c3c;
            color: white;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
        }
        .btn-ver {
            background: #3498db;
            color: white;
            padding: 5px 12px;
            text-decoration: none;
            border-radius: 6px;
            font-size: 0.8rem;
        }
        .btn-ver:hover {
            background: #2980b9;
        }
        .top-usuarios {
            margin-top: 2rem;
        }
        .top-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        .top-nombre {
            font-weight: bold;
        }
        .top-cantidad {
            background: #fdbb4d;
            padding: 3px 10px;
            border-radius: 20px;
        }
        @media (max-width: 768px) {
            .charts-grid {
                grid-template-columns: 1fr;
            }
            .container {
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
<div class="header">
    <div style="display: flex; align-items: center; gap: 15px;">
        <img src="image/Prest_logo.png" alt="Logo" style="height: 75px;">
        <span style="font-size: 1.8rem; font-weight: bold;"> Panel de Administración</span>
    </div>
    <div class="user-info">
        <span class="badge-admin">Admin: <?php echo $usuario_nombre; ?></span>
        <a href="simulador.html" class="btn-logout" style="background: #27ae60;"> Simulador</a>
        <a href="logout.php" class="btn-logout">Cerrar Sesión</a>
    </div>
</div>

<div class="container">
    <!-- Tarjetas de estadísticas -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon"></div>
            <div class="stat-number"><?php echo $total_solicitudes; ?></div>
            <div class="stat-label">Total Solicitudes</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"></div>
            <div class="stat-number">$<?php echo number_format($total_monto, 0); ?></div>
            <div class="stat-label">Monto Total</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"></div>
            <div class="stat-number"><?php echo $total_usuarios; ?></div>
            <div class="stat-label">Usuarios Registrados</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"></div>
            <div class="stat-number"><?php echo $pendientes; ?></div>
            <div class="stat-label">Pendientes</div>
        </div>
    </div>

    <!-- Gráficas -->
    <div class="charts-grid">
        <div class="chart-card">
            <h3> Solicitudes por Estado</h3>
            <canvas id="estadosChart"></canvas>
        </div>
        <div class="chart-card">
            <h3> Monto Solicitado por Mes</h3>
            <canvas id="montoChart"></canvas>
        </div>
        <div class="chart-card">
            <h3>👥 Usuarios por Rol</h3>
            <canvas id="rolesChart"></canvas>
        </div>
    </div>

    <!-- Top 5 Usuarios -->
    <div class="chart-card top-usuarios">
        <h3> Top 5 Usuarios con más solicitudes</h3>
        <?php while($top = $top_usuarios->fetch_assoc()): ?>
        <div class="top-item">
            <span class="top-nombre">👤 <?php echo $top['nombre_completo']; ?> (@<?php echo $top['nombre_usuario']; ?>)</span>
            <span class="top-cantidad"><?php echo $top['total_solicitudes']; ?> solicitudes - $<?php echo number_format($top['total_monto'], 0); ?></span>
        </div>
        <?php endwhile; ?>
    </div>

    <!-- Tabla de solicitudes -->
    <div class="table-container">
        <div class="table-header"> Todas las Solicitudes</div>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Fecha</th>
                    <th>Usuario</th>
                    <th>Solicitante</th>
                    <th>Monto</th>
                    <th>Plazos</th>
                    <th>Pago Mensual</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $solicitudes->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo date('d/m/Y', strtotime($row['fecha_registro'])); ?></td>
                    <td><?php echo $row['usuario']; ?></td>
                    <td><?php echo $row['nombre_completo']; ?></td>
                    <td>$<?php echo number_format($row['monto_solicitado'], 2); ?></td>
                    <td><?php echo $row['plazos']; ?> meses</td>
                    <td>$<?php echo number_format($row['pago_mensual'], 2); ?></td>
                    <td>
                        <?php 
                        $estado = $row['estado'] ?? 'pendiente';
                        if($estado == 'pendiente'): ?>
                            <span class="estado-pendiente"> Pendiente</span>
                        <?php elseif($estado == 'aprobado'): ?>
                            <span class="estado-aprobado"> Aprobado</span>
                        <?php else: ?>
                            <span class="estado-rechazado"> Rechazado</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="ver_solicitud_detalle.php?id=<?php echo $row['id']; ?>" class="btn-ver">Ver</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    // Gráfica de estados
    const ctxEstados = document.getElementById('estadosChart').getContext('2d');
    new Chart(ctxEstados, {
        type: 'doughnut',
        data: {
            labels: ['Pendientes', 'Aprobados', 'Rechazados'],
            datasets: [{
                data: [<?php echo $pendientes; ?>, <?php echo $aprobados; ?>, <?php echo $rechazados; ?>],
                backgroundColor: ['#f39c12', '#27ae60', '#e74c3c'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });

    // Gráfica de montos por mes
    const ctxMonto = document.getElementById('montoChart').getContext('2d');
    new Chart(ctxMonto, {
        type: 'bar',
        data: {
            labels: [<?php echo "'" . implode("','", array_column($monto_por_mes, 'mes')) . "'"; ?>],
            datasets: [{
                label: 'Monto Solicitado ($)',
                data: [<?php echo implode(',', array_column($monto_por_mes, 'monto')); ?>],
                backgroundColor: '#3498db',
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'top' }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Gráfica de roles de usuarios
    const ctxRoles = document.getElementById('rolesChart').getContext('2d');
    new Chart(ctxRoles, {
        type: 'pie',
        data: {
            labels: ['Administradores', 'Contadores', 'Usuarios'],
            datasets: [{
                data: [<?php echo $admins; ?>, <?php echo $contadores; ?>, <?php echo $usuarios_normales; ?>],
                backgroundColor: ['#e74c3c', '#f39c12', '#3498db'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
</script>
</body>
</html>