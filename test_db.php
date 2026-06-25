<?php
// =============================================
// ARCHIVO DE PRUEBA DE CONEXIÓN A BD
// =============================================

$host = 'localhost';
$usuario = 'root';
$password = '';
$basedatos = 'prestamos';  // ← Tu base de datos se llama "prestamos"

echo "<h1> Prueba de Conexión a Base de Datos</h1>";

// Intentar conectar
$conn = new mysqli($host, $usuario, $password);

if ($conn->connect_error) {
    echo "<p style='color:red;'> Error de conexión: " . $conn->connect_error . "</p>";
    exit();
} else {
    echo "<p style='color:green;'> Conexión al servidor MySQL exitosa</p>";
}

// Verificar si existe la base de datos "prestamos"
$result = $conn->query("SHOW DATABASES LIKE '$basedatos'");

if ($result->num_rows > 0) {
    echo "<p style='color:green;'> La base de datos <strong>'$basedatos'</strong> existe</p>";
    
    // Seleccionar la base de datos
    $conn->select_db($basedatos);
    
    // Mostrar todas las tablas
    echo "<h2> Tablas en la base de datos '$basedatos':</h2>";
    $tablas = $conn->query("SHOW TABLES");
    
    if ($tablas->num_rows > 0) {
        echo "<ul>";
        while($tabla = $tablas->fetch_array()) {
            echo "<li> " . $tabla[0] . "</li>";
        }
        echo "</ul>";
    } else {
        echo "<p style='color:orange;'>⚠️ La base de datos está vacía (no hay tablas)</p>";
    }
    
    // Mostrar cantidad de registros en cada tabla
    echo "<h2> Registros por tabla:</h2>";
    $tablas = $conn->query("SHOW TABLES");
    while($tabla = $tablas->fetch_array()) {
        $nombre_tabla = $tabla[0];
        $count = $conn->query("SELECT COUNT(*) as total FROM $nombre_tabla")->fetch_assoc()['total'];
        echo "<p><strong>$nombre_tabla:</strong> $count registros</p>";
    }
    
} else {
    echo "<p style='color:red;'> La base de datos <strong>'$basedatos'</strong> NO existe</p>";
    echo "<p> Crea la base de datos con: <code>CREATE DATABASE $basedatos;</code></p>";
}

$conn->close();
?>