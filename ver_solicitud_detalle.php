<?php
session_start();

// Verificar si el usuario está logueado y es admin
if(!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

include 'config.php';

$id = $_GET['id'] ?? 0;
$usuario_rol = $_SESSION['usuario_rol'];

// Buscar la solicitud
$solicitud = $conn->query("SELECT s.*, u.nombre_usuario as usuario_creador 
                           FROM solicitudes s 
                           LEFT JOIN usuarios u ON s.usuario_id = u.id 
                           WHERE s.id = $id")->fetch_assoc();

// Si no es admin y la solicitud no es suya, no puede verla
if($usuario_rol != 'admin' && $solicitud['usuario_id'] != $_SESSION['usuario_id']) {
    echo "No tienes permiso para ver esta solicitud";
    exit();
}

if(!$solicitud) {
    echo "Solicitud no encontrada";
    exit();
}

$dependientes = $conn->query("SELECT * FROM dependientes WHERE solicitud_id = $id");
$referencias = $conn->query("SELECT * FROM referencias_bancarias WHERE solicitud_id = $id");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalle Solicitud #<?php echo $id; ?></title>
    <style>
        body {
            font-family: Arial;
            background: #f0f2f5;
            padding: 20px;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h2 {
            color: #2c3e50;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
        }
        h3 {
            background: #3498db;
            color: white;
            padding: 8px;
            border-radius: 6px;
            margin-top: 20px;
        }
        .fila {
            display: flex;
            margin-bottom: 10px;
            border-bottom: 1px solid #eee;
            padding: 8px;
        }
        .label {
            font-weight: bold;
            width: 200px;
        }
        .valor {
            flex: 1;
        }
        button {
            background: #3498db;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            margin-top: 20px;
            margin-right: 10px;
        }
        .btn-cerrar {
            background: #e74c3c;
        }
        .btn-imprimir {
            background: #27ae60;
        }
        .estado {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: bold;
        }
        .estado-pendiente { background: #f39c12; color: white; }
        .estado-aprobado { background: #27ae60; color: white; }
        .estado-rechazado { background: #e74c3c; color: white; }
    </style>
</head>
<body>
<div class="container">
    <h2> Detalle de Solicitud #<?php echo $id; ?></h2>
    
    <?php if($usuario_rol == 'admin'): ?>
    <div class="fila">
        <div class="label">Usuario que solicitó:</div>
        <div class="valor"><?php echo $solicitud['usuario_creador']; ?> (ID: <?php echo $solicitud['usuario_id']; ?>)</div>
    </div>
    <?php endif; ?>
    
    <div class="fila">
        <div class="label">Estado:</div>
        <div class="valor">
            <span class="estado estado-<?php echo $solicitud['estado']; ?>">
                <?php echo ucfirst($solicitud['estado']); ?>
            </span>
        </div>
    </div>
    
    <h3>Datos del Préstamo</h3>
    <div class="fila"><div class="label">Monto solicitado:</div><div class="valor">$<?php echo number_format($solicitud['monto_solicitado'], 2); ?></div></div>
    <div class="fila"><div class="label">Plazos:</div><div class="valor"><?php echo $solicitud['plazos']; ?> meses</div></div>
    <div class="fila"><div class="label">Pago mensual:</div><div class="valor">$<?php echo number_format($solicitud['pago_mensual'], 2); ?></div></div>
    
    <h3> Datos Personales</h3>
    <div class="fila"><div class="label">Nombre completo:</div><div class="valor"><?php echo $solicitud['nombre_completo'] . ' ' . $solicitud['apellido_paterno'] . ' ' . $solicitud['apellido_materno']; ?></div></div>
    <div class="fila"><div class="label">RFC:</div><div class="valor"><?php echo $solicitud['rfc']; ?></div></div>
    <div class="fila"><div class="label">CURP:</div><div class="valor"><?php echo $solicitud['curp']; ?></div></div>
    <div class="fila"><div class="label">Correo:</div><div class="valor"><?php echo $solicitud['correo']; ?></div></div>
    <div class="fila"><div class="label">Teléfono:</div><div class="valor"><?php echo $solicitud['telefono_celular']; ?></div></div>
    
    <h3> Dirección</h3>
    <div class="fila"><div class="label">Domicilio:</div><div class="valor"><?php echo $solicitud['domicilio']; ?></div></div>
    <div class="fila"><div class="label">Colonia:</div><div class="valor"><?php echo $solicitud['colonia']; ?></div></div>
    <div class="fila"><div class="label">Municipio:</div><div class="valor"><?php echo $solicitud['municipio']; ?></div></div>
    
    <h3> Información Financiera</h3>
    <div class="fila"><div class="label">Ingreso bruto mensual:</div><div class="valor">$<?php echo number_format($solicitud['ingreso_bruto_mensual'], 2); ?></div></div>
    <div class="fila"><div class="label">Auto propio:</div><div class="valor"><?php echo $solicitud['auto_propio']; ?></div></div>
    
    <?php if($dependientes->num_rows > 0): ?>
    <h3> Dependientes Económicos</h3>
    <?php while($dep = $dependientes->fetch_assoc()): ?>
        <div class="fila"><div class="label">Nombre:</div><div class="valor"><?php echo $dep['nombre_completo']; ?> (<?php echo $dep['parentesco']; ?>)</div></div>
    <?php endwhile; ?>
    <?php endif; ?>
    
    <button class="btn-imprimir" onclick="window.print()">🖨️ Imprimir</button>
    <button class="btn-cerrar" onclick="window.location.href='dashboard.php'">◀ Volver</button>
</div>
</body>
</html>