<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Insertar solicitud principal
    $stmt = $conn->prepare("INSERT INTO solicitudes (
        monto_solicitado, plazos, pago_mensual,
        nombre_completo, apellido_paterno, apellido_materno,
        rfc, curp, nacionalidad, correo, estado_civil,
        antiguedad_empleo, cargo, fecha_nacimiento, sexo,
        domicilio, colonia, municipio, codigo_postal,
        habita_casa, antiguedad_domicilio, telefono_casa,
        telefono_celular, ingreso_bruto_mensual, pago_renta,
        pago_hipoteca, pago_otras_deudas, auto_propio,
        valor_autos, valor_inmuebles
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $stmt->bind_param(
        "iiddsssssssssssssssssssddddssdd",
        $_POST['monito'], $_POST['meses'], $_POST['pagos'],
        $_POST['nombre_completo'], $_POST['apellido_paterno'], $_POST['apellido_materno'],
        $_POST['rfc'], $_POST['curp'], $_POST['nacionalidad'], $_POST['correo'],
        $_POST['estado_civil'], $_POST['antiguedad_empleo'], $_POST['cargo'],
        $_POST['fecha_nacimiento'], $_POST['sexo'], $_POST['domicilio'],
        $_POST['colonia'], $_POST['municipio'], $_POST['codigo_postal'],
        $_POST['habita_casa'], $_POST['antiguedad_domicilio'], $_POST['telefono_casa'],
        $_POST['telefono_celular'], $_POST['ingreso_bruto'], $_POST['pago_renta'],
        $_POST['pago_hipoteca'], $_POST['pago_otras_deudas'], $_POST['auto_propio'],
        $_POST['valor_autos'], $_POST['valor_inmuebles']
    );
    
    $stmt->execute();
    $solicitud_id = $conn->insert_id;
    $stmt->close();
    
    // Insertar dependientes
    if(isset($_POST['dep_nombre'])) {
        $dep_stmt = $conn->prepare("INSERT INTO dependientes (solicitud_id, nombre_completo, rfc, telefono, celular, parentesco) VALUES (?, ?, ?, ?, ?, ?)");
        for($i = 0; $i < count($_POST['dep_nombre']); $i++) {
            if(!empty($_POST['dep_nombre'][$i])) {
                $dep_stmt->bind_param("isssss", $solicitud_id, $_POST['dep_nombre'][$i], $_POST['dep_rfc'][$i], $_POST['dep_telefono'][$i], $_POST['dep_celular'][$i], $_POST['dep_parentesco'][$i]);
                $dep_stmt->execute();
            }
        }
        $dep_stmt->close();
    }
    
    // Insertar referencias bancarias
    if(isset($_POST['ref_institucion'])) {
        $ref_stmt = $conn->prepare("INSERT INTO referencias_bancarias (solicitud_id, institucion, numero_cuenta, comentarios) VALUES (?, ?, ?, ?)");
        for($i = 0; $i < count($_POST['ref_institucion']); $i++) {
            if(!empty($_POST['ref_institucion'][$i])) {
                $ref_stmt->bind_param("isss", $solicitud_id, $_POST['ref_institucion'][$i], $_POST['ref_numero'][$i], $_POST['ref_comentarios'][$i]);
                $ref_stmt->execute();
            }
        }
        $ref_stmt->close();
    }
    
    $conn->close();
    
    // Mostrar mensaje de éxito
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Solicitud Enviada</title>
        <style>
            body { font-family: Arial; background: #27ae60; display: flex; justify-content: center; align-items: center; height: 100vh; }
            .card { background: white; padding: 40px; border-radius: 12px; text-align: center; max-width: 500px; }
            button { background: #3498db; color: white; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; margin-top: 20px; }
        </style>
    </head>
    <body>
        <div class="card">
            <h2> ¡Solicitud Enviada!</h2>
            <p>Tu solicitud de préstamo ha sido registrada exitosamente.</p>
            <p><strong>Número de folio:</strong> <?php echo $solicitud_id; ?></p>
            <p>Un asesor se contactará contigo en las próximas 24 horas.</p>
            <button onclick="window.close()">Cerrar</button>
        </div>
    </body>
    </html>
    <?php
} else {
    header("Location: solicitud.php");
    exit();
}
?>