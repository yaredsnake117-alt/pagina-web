<?php
// =============================================
// 
// =============================================

// Datos de conexión
$host = 'localhost';
$usuario = 'root';
$password = '';
$basedatos = 'prestamos';

// Crear conexión
$conn = new mysqli($host, $usuario, $password);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Crear base de datos si no existe
$conn->query("CREATE DATABASE IF NOT EXISTS $basedatos");
$conn->select_db($basedatos);

// ========== TABLA DE USUARIOS ==========
$conn->query("CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_usuario VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(100) NOT NULL,
    nombre_completo VARCHAR(150) NOT NULL,
    email VARCHAR(100),
    rol VARCHAR(50) DEFAULT 'usuario',
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");



// Insertar usuarios de ejemplo (agrega el contador)
$result = $conn->query("SELECT * FROM usuarios LIMIT 1");
if($result->num_rows == 0) {
    $conn->query("INSERT INTO usuarios (nombre_usuario, password, nombre_completo, email, rol) VALUES 
        ('admin', '1234', 'Administrador del Sistema', 'admin@prestamos.com', 'admin'),
        ('contador', '1234', 'Usuario Contador', 'contador@prestamos.com', 'contador'),
        ('jared', '1234', 'Jared Jasiel Reyes Elizalde', 'jared@email.com', 'usuario'),
        ('daniel', '1234', 'Daniel Enrique Amador Rivera', 'daniel@email.com', 'usuario')");
}


// ========== TABLA DE SOLICITUDES ==========
$conn->query("CREATE TABLE IF NOT EXISTS solicitudes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    monto_solicitado DECIMAL(10,2),
    plazos INT,
    pago_mensual DECIMAL(10,2),
    nombre_completo VARCHAR(200),
    apellido_paterno VARCHAR(100),
    apellido_materno VARCHAR(100),
    rfc VARCHAR(20),
    curp VARCHAR(20),
    nacionalidad VARCHAR(50),
    correo VARCHAR(150),
    estado_civil VARCHAR(50),
    antiguedad_empleo VARCHAR(50),
    cargo VARCHAR(100),
    fecha_nacimiento DATE,
    sexo VARCHAR(20),
    domicilio VARCHAR(200),
    colonia VARCHAR(100),
    municipio VARCHAR(100),
    codigo_postal VARCHAR(10),
    habita_casa VARCHAR(50),
    antiguedad_domicilio VARCHAR(50),
    telefono_casa VARCHAR(20),
    telefono_celular VARCHAR(20),
    ingreso_bruto_mensual DECIMAL(10,2),
    pago_renta DECIMAL(10,2),
    pago_hipoteca DECIMAL(10,2),
    pago_otras_deudas DECIMAL(10,2),
    auto_propio VARCHAR(10),
    valor_autos DECIMAL(10,2),
    valor_inmuebles DECIMAL(10,2),
    estado VARCHAR(20) DEFAULT 'pendiente',
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
)");

// Tabla de dependientes
$conn->query("CREATE TABLE IF NOT EXISTS dependientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    solicitud_id INT,
    nombre_completo VARCHAR(200),
    rfc VARCHAR(20),
    telefono VARCHAR(20),
    celular VARCHAR(20),
    parentesco VARCHAR(50),
    FOREIGN KEY (solicitud_id) REFERENCES solicitudes(id) ON DELETE CASCADE
)");

// Tabla de referencias bancarias
$conn->query("CREATE TABLE IF NOT EXISTS referencias_bancarias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    solicitud_id INT,
    institucion VARCHAR(150),
    numero_cuenta VARCHAR(100),
    comentarios TEXT,
    FOREIGN KEY (solicitud_id) REFERENCES solicitudes(id) ON DELETE CASCADE
)");


?>