<?php
session_start();

// Verificar si hay sesión activa
if(!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

// Verificar que el rol sea el permitido para esta página
function verificar_rol($rol_permitido) {
    if($_SESSION['usuario_rol'] != $rol_permitido) {
        if($_SESSION['usuario_rol'] == 'admin') {
            header("Location: admin_dashboard.php");
        } elseif($_SESSION['usuario_rol'] == 'contador') {
            header("Location: contador_dashboard.php");
        } else {
            header("Location: usuario_dashboard.php");
        }
        exit();
    }
}

// Función solo para admin
function solo_admin() {
    if($_SESSION['usuario_rol'] != 'admin') {
        header("Location: login.php");
        exit();
    }
}

// Función solo para contador
function solo_contador() {
    if($_SESSION['usuario_rol'] != 'contador') {
        header("Location: login.php");
        exit();
    }
}

// Función para admin o contador
function solo_admin_contador() {
    if($_SESSION['usuario_rol'] != 'admin' && $_SESSION['usuario_rol'] != 'contador') {
        header("Location: login.php");
        exit();
    }
}
?>