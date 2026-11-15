<?php
session_start();
require_once '../conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $correo = trim($_POST['correo']);
    $clave = trim($_POST['clave']);

    if (!empty($correo) && !empty($clave)) {
        // Consulta segura usando sentencia preparada 
        $sql = "SELECT id_usuario, nombre, correo, clave, rol, activo FROM tbl_usuarios WHERE correo = :correo AND activo = 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['correo' => $correo]);
        $usuario = $stmt->fetch();

        // Verificar si el usuario existe y la contraseña coincide
        if ($usuario && password_verify($clave, $usuario['clave'])) {
            // Apertura de variables de sesión 
            $_SESSION['id_usuario'] = $usuario['id_usuario'];
            $_SESSION['nombre_usuario'] = $usuario['nombre'];
            $_SESSION['correo_usuario'] = $usuario['correo'];
            $_SESSION['rol_usuario'] = $usuario['rol'];

            header("Location: dashboard.php");
            exit();
        } else {
            header("Location: login.php?error=1");
            exit();
        }
    } else {
        header("Location: login.php?error=1");
        exit();
    }
} else {
    header("Location: login.php");
    exit();
}
?>