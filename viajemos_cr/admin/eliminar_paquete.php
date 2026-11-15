<?php
session_start();
require_once '../conexion.php';

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

$id_paquete = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id_paquete > 0) {
    // Buscar la imagen asociada para eliminarla del servidor si existe
    $stmt = $pdo->prepare("SELECT imagen_principal FROM tbl_paquetes WHERE id_paquete = :id");
    $stmt->execute(['id' => $id_paquete]);
    $paquete = $stmt->fetch();

    if ($paquete) {
        if (!empty($paquete['imagen_principal']) && file_exists('../' . $paquete['imagen_principal'])) {
            unlink('../' . $paquete['imagen_principal']);
        }

        // Eliminar el registro de la base de datos
        $stmt_delete = $pdo->prepare("DELETE FROM tbl_paquetes WHERE id_paquete = :id");
        $stmt_delete->execute(['id' => $id_paquete]);
    }
}

header("Location: paquetes.php?msg=eliminado");
exit();
?>