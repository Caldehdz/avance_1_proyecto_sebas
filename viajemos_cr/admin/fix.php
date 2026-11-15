<?php
require_once '../conexion.php';

$correo = 'admin@viajemoscr.com';
$nueva_clave = 'Admin123*';
$hash = password_hash($nueva_clave, PASSWORD_DEFAULT);

$sql = "UPDATE tbl_usuarios SET clave = :clave, activo = 1 WHERE correo = :correo";
$stmt = $pdo->prepare($sql);
if ($stmt->execute(['clave' => $hash, 'correo' => $correo])) {
    echo "¡Contraseña actualizada correctamente! Ya puedes ir a <a href='login.php'>login.php</a> e iniciar sesión con <strong>admin@viajemoscr.com</strong> y <strong>Admin123*</strong>.";
} else {
    echo "Hubo un error al actualizar.";
}
?>