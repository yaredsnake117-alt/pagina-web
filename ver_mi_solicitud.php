<?php
session_start();
if(!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}
include 'config.php';

$id = $_GET['id'] ?? 0;
$usuario_id = $_SESSION['usuario_id'];

$solicitud = $conn->query("SELECT * FROM solicitudes WHERE id = $id AND usuario_id = $usuario_id")->fetch_assoc();

if(!$solicitud) {
    echo "Solicitud no encontrada";
    exit();
}

$dependientes = $conn->query("SELECT * FROM dependientes WHERE solicitud_id = $id");
$referencias = $conn->query("SELECT * FROM referencias_bancarias WHERE solicitud_id = $id");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Mi Solicitud #<?php echo $id; ?></title>
    <style>
        body { font-family: Arial; background: #f0f2f5; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 12px; }
        h2 { color: #2c3e50; border-bottom: 2px solid #3498db; padding-bottom: 10px; }
        .fila { display: flex; margin-bottom: 10px; border-bottom: 1px solid #eee; padding: 8px; }
        .label { font-weight: bold; width: 200px; }
        .btn { background: #3498db; color: white; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; margin-top: 20px; }
    </style>
</head>
<body>
<div class="container">
    <h2>Mi Solicitud #<?php echo $id; ?></h2>
    
    <h3> Datos del Préstamo</h3>
    <div class="fila"><div class="label">Monto:</div><div>$<?php echo number_format($solicitud['monto_solicitado'], 2); ?></div></div>
    <div class="fila"><div class="label">Plazos:</div><div><?php echo $solicitud['plazos']; ?> meses</div></div>
    <div class="fila"><div class="label">Pago mensual:</div><div>$<?php echo number_format($solicitud['pago_mensual'], 2); ?></div></div>
    <div class="fila"><div class="label">Estado:</div><div><?php echo ucfirst($solicitud['estado']); ?></div></div>
    
    <h3> Mis Datos</h3>
    <div class="fila"><div class="label">Nombre:</div><div><?php echo $solicitud['nombre_completo'] . ' ' . $solicitud['apellido_paterno']; ?></div></div>
    <div class="fila"><div class="label">RFC:</div><div><?php echo $solicitud['rfc']; ?></div></div>
    <div class="fila"><div class="label">Correo:</div><div><?php echo $solicitud['correo']; ?></div></div>
    
    <button class="btn" onclick="window.location.href='dashboard.php'">◀ Volver a mis solicitudes</button>
    <button class="btn" onclick="window.print()">🖨️ Imprimir</button>
</div>
</body>
</html>