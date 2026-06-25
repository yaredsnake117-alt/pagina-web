<?php
session_start();

// Verificar sesión
if(!isset($_SESSION['usuario_id'])) {
    // Guardar la página a la que quería ir
    $_SESSION['redirect_after_login'] = "solicitud.php";
    header("Location: login.php");
    exit();
}

include 'config.php';

// Recibir datos del simulador
if(isset($_POST['monito']) && !empty($_POST['monito'])){
    $monito = $_POST['monito'];
    $meses = $_POST['meses'];
    $pagos = $_POST['pagos'];
    
    $_SESSION['simulador_monto'] = $monito;
    $_SESSION['simulador_meses'] = $meses;
    $_SESSION['simulador_pagos'] = $pagos;
} else {
    $monito = $_SESSION['simulador_monto'] ?? '';
    $meses = $_SESSION['simulador_meses'] ?? '';
    $pagos = $_SESSION['simulador_pagos'] ?? '';
}

$usuario_id = $_SESSION['usuario_id'];
$usuario_nombre = $_SESSION['usuario_completo'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Solicitud de Préstamo</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            background: #f0f2f5;
            padding: 20px;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            padding: 2rem;
            border-radius: 12px;
        }
        h1 {
            color: #2c3e50;
            text-align: center;
            margin-bottom: 1rem;
        }
        h3 {
            background: #3498db;
            color: white;
            padding: 10px;
            border-radius: 6px;
            margin: 20px 0 15px 0;
        }
        .campo {
            margin-bottom: 1rem;
        }
        label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
            color: #34495e;
        }
        input, select, textarea {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #bdc3c7;
            border-radius: 4px;
            font-size: 14px;
        }
        input[readonly] {
            background-color: #ecf0f1;
        }
        .fila {
            display: flex;
            gap: 15px;
            margin-bottom: 1rem;
        }
        .fila .campo {
            flex: 1;
            margin-bottom: 0;
        }
        button {
            background: #27ae60;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 6px;
            font-size: 1rem;
            cursor: pointer;
            width: 100%;
            margin-top: 20px;
        }
        .btn-agregar {
            background: #3498db;
            margin-top: 5px;
            font-size: 0.9rem;
            padding: 8px 16px;
            width: auto;
        }
        .btn-eliminar {
            background: #e74c3c;
            margin-top: 10px;
            font-size: 0.8rem;
            padding: 5px 10px;
            width: auto;
        }
        .dependiente, .referencia {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            border: 1px solid #dee2e6;
        }
        .info-usuario {
            background: #ecf0f1;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 20px;
            text-align: right;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="info-usuario">
         Usuario: <strong><?php echo $usuario_nombre; ?></strong>
        <a href="logout.php" style="margin-left: 15px; color: #e74c3c;">Cerrar sesión</a>
    </div>
    
    <h1> SOLICITUD DE PRÉSTAMO</h1>
    
    <form method="POST" action="procesar_solicitud.php">
        <input type="hidden" name="usuario_id" value="<?php echo $usuario_id; ?>">
        <input type="hidden" name="monito" value="<?php echo $monito; ?>">
        <input type="hidden" name="meses" value="<?php echo $meses; ?>">
        <input type="hidden" name="pagos" value="<?php echo $pagos; ?>">
        
        <h3> DATOS DEL PRÉSTAMO</h3>
        <div class="fila">
            <div class="campo">
                <label>Monto solicitado ($)</label>
                <input type="text" value="<?php echo $monito ? '$' . number_format($monito, 2) : 'No calculado'; ?>" readonly>
            </div>
            <div class="campo">
                <label>Plazos (meses)</label>
                <input type="text" value="<?php echo $meses ? $meses . ' meses' : 'No calculado'; ?>" readonly>
            </div>
            <div class="campo">
                <label>Pago mensual ($)</label>
                <input type="text" value="<?php echo $pagos ? '$' . number_format($pagos, 2) : 'No calculado'; ?>" readonly>
            </div>
        </div>
        
        <h3> DATOS DEL SOLICITANTE</h3>
        <div class="fila">
            <div class="campo">
                <label>Nombre completo *</label>
                <input type="text" name="nombre_completo" required>
            </div>
        </div>
        <div class="fila">
            <div class="campo">
                <label>Apellido paterno *</label>
                <input type="text" name="apellido_paterno" required>
            </div>
            <div class="campo">
                <label>Apellido materno *</label>
                <input type="text" name="apellido_materno" required>
            </div>
        </div>
        <div class="fila">
            <div class="campo">
                <label>R.F.C con homoclave *</label>
                <input type="text" name="rfc" required>
            </div>
            <div class="campo">
                <label>CURP *</label>
                <input type="text" name="curp" required>
            </div>
        </div>
        <div class="fila">
            <div class="campo">
                <label>Nacionalidad *</label>
                <input type="text" name="nacionalidad" required value="Mexicana">
            </div>
            <div class="campo">
                <label>Correo electrónico *</label>
                <input type="email" name="correo" required>
            </div>
        </div>
        <div class="fila">
            <div class="campo">
                <label>Estado Civil *</label>
                <select name="estado_civil" required>
                    <option value="">Seleccione</option>
                    <option>Soltero(a)</option>
                    <option>Casado(a)</option>
                    <option>Divorciado(a)</option>
                    <option>Viudo(a)</option>
                    <option>Unión libre</option>
                </select>
            </div>
            <div class="campo">
                <label>Antigüedad empleo *</label>
                <input type="text" name="antiguedad_empleo" required>
            </div>
        </div>
        <div class="fila">
            <div class="campo">
                <label>Cargo que desempeña *</label>
                <input type="text" name="cargo" required>
            </div>
            <div class="campo">
                <label>Fecha de nacimiento *</label>
                <input type="date" name="fecha_nacimiento" required>
            </div>
        </div>
        <div class="campo">
            <label>Sexo *</label>
            <select name="sexo" required>
                <option value="">Seleccione</option>
                <option>Masculino</option>
                <option>Femenino</option>
            </select>
        </div>
        
        <h3> DIRECCIÓN</h3>
        <div class="campo">
            <label>Domicilio Particular *</label>
            <input type="text" name="domicilio" required>
        </div>
        <div class="fila">
            <div class="campo">
                <label>Colonia *</label>
                <input type="text" name="colonia" required>
            </div>
            <div class="campo">
                <label>Municipio/Alcaldía *</label>
                <input type="text" name="municipio" required>
            </div>
        </div>
        <div class="fila">
            <div class="campo">
                <label>Código postal *</label>
                <input type="text" name="codigo_postal" required>
            </div>
            <div class="campo">
                <label>Habita en Casa *</label>
                <select name="habita_casa" required>
                    <option>Propia</option>
                    <option>Rentada</option>
                    <option>Prestada</option>
                    <option>Familiar</option>
                </select>
            </div>
        </div>
        <div class="fila">
            <div class="campo">
                <label>Antigüedad en el Domicilio *</label>
                <input type="text" name="antiguedad_domicilio" required>
            </div>
            <div class="campo">
                <label>Teléfono de casa</label>
                <input type="text" name="telefono_casa">
            </div>
        </div>
        <div class="campo">
            <label>Teléfono Celular *</label>
            <input type="text" name="telefono_celular" required>
        </div>
        
        <h3> DEPENDIENTES ECONÓMICOS</h3>
        <div id="dependientes-container">
            <div class="dependiente">
                <div class="fila">
                    <div class="campo"><label>Nombre Completo</label><input type="text" name="dep_nombre[]"></div>
                    <div class="campo"><label>R.F.C</label><input type="text" name="dep_rfc[]"></div>
                </div>
                <div class="fila">
                    <div class="campo"><label>Teléfono</label><input type="text" name="dep_telefono[]"></div>
                    <div class="campo"><label>Celular</label><input type="text" name="dep_celular[]"></div>
                </div>
                <div class="campo"><label>Parentesco</label><input type="text" name="dep_parentesco[]"></div>
            </div>
        </div>
        <button type="button" class="btn-agregar" onclick="agregarDependiente()">+ Agregar Dependiente</button>
        
        <h3> INFORMACIÓN FINANCIERA</h3>
        <div class="fila">
            <div class="campo"><label>Ingreso Bruto Mensual *</label><input type="number" name="ingreso_bruto" step="0.01" required></div>
            <div class="campo"><label>Pago de Renta</label><input type="number" name="pago_renta" step="0.01" value="0"></div>
        </div>
        <div class="fila">
            <div class="campo"><label>Pago de Hipoteca</label><input type="number" name="pago_hipoteca" step="0.01" value="0"></div>
            <div class="campo"><label>Pago Otras Deudas</label><input type="number" name="pago_otras_deudas" step="0.01" value="0"></div>
        </div>
        <div class="fila">
            <div class="campo">
                <label>Auto Propio</label>
                <select name="auto_propio"><option>No</option><option>Sí</option></select>
            </div>
            <div class="campo"><label>Valor estimado Autos</label><input type="number" name="valor_autos" step="0.01" value="0"></div>
        </div>
        <div class="campo"><label>Valor estimado Inmuebles</label><input type="number" name="valor_inmuebles" step="0.01" value="0"></div>
        
        <h3> REFERENCIAS BANCARIAS</h3>
        <div id="referencias-container">
            <div class="referencia">
                <div class="fila">
                    <div class="campo"><label>Institución *</label><input type="text" name="ref_institucion[]" required></div>
                    <div class="campo"><label>Número de Cuenta *</label><input type="text" name="ref_numero[]" required></div>
                </div>
                <div class="campo"><label>Comentarios</label><textarea name="ref_comentarios[]" rows="2"></textarea></div>
            </div>
        </div>
        <button type="button" class="btn-agregar" onclick="agregarReferencia()">+ Agregar Referencia</button>
        
        <button type="submit"> ENVIAR SOLICITUD</button>
    </form>
</div>

<script>
    function agregarDependiente() {
        const container = document.getElementById('dependientes-container');
        const div = document.createElement('div');
        div.className = 'dependiente';
        div.innerHTML = `
            <button type="button" class="btn-eliminar" onclick="this.parentElement.remove()">✖ Eliminar</button>
            <div class="fila">
                <div class="campo"><label>Nombre Completo</label><input type="text" name="dep_nombre[]"></div>
                <div class="campo"><label>R.F.C</label><input type="text" name="dep_rfc[]"></div>
            </div>
            <div class="fila">
                <div class="campo"><label>Teléfono</label><input type="text" name="dep_telefono[]"></div>
                <div class="campo"><label>Celular</label><input type="text" name="dep_celular[]"></div>
            </div>
            <div class="campo"><label>Parentesco</label><input type="text" name="dep_parentesco[]"></div>
        `;
        container.appendChild(div);
    }

    function agregarReferencia() {
        const container = document.getElementById('referencias-container');
        const div = document.createElement('div');
        div.className = 'referencia';
        div.innerHTML = `
            <button type="button" class="btn-eliminar" onclick="this.parentElement.remove()">✖ Eliminar</button>
            <div class="fila">
                <div class="campo"><label>Institución *</label><input type="text" name="ref_institucion[]" required></div>
                <div class="campo"><label>Número de Cuenta *</label><input type="text" name="ref_numero[]" required></div>
            </div>
            <div class="campo"><label>Comentarios</label><textarea name="ref_comentarios[]" rows="2"></textarea></div>
        `;
        container.appendChild(div);
    }
</script>
</body>
</html>