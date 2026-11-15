<?php
session_start();

// Validar que el usuario haya iniciado sesión
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - SGRT Viajemos por el Mundo CR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <!-- Barra de navegación superior -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php">SGRT - Panel Administrativo</a>
            <div class="d-flex text-white align-items-center">
                <span class="me-3">Hola, <strong><?php echo htmlspecialchars($_SESSION['nombre_usuario']); ?></strong> (<em><?php echo $_SESSION['rol_usuario']; ?></em>)</span>
                <a href="logout.php" class="btn btn-outline-light btn-sm">Cerrar Sesión</a>
            </div>
        </div>
    </nav>

    <!-- Contenido principal -->
    <div class="container my-5">
        <div class="row">
            <div class="col-md-12 mb-4">
                <div class="p-4 bg-white rounded shadow-sm">
                    <h2>Bienvenido al Sistema de Gestión y Reservas Turísticas (SGRT)</h2>
                    <p class="text-muted">Utiliza el menú de administración para gestionar los paquetes turísticos, revisar comprobantes de pago y controlar las reservas de la agencia Viajemos por el Mundo CR.</p>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Módulo de Paquetes -->
            <div class="col-md-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="card-title">Paquetes Turísticos</h5>
                        <p class="card-text">Crear, modificar, actualizar itinerarios, precios y cupos de los tours[cite: 13].</p>
                        <a href="paquetes.php" class="btn btn-primary">Gestionar Paquetes</a>
                    </div>
                </div>
            </div>

            <!-- Módulo de Reservas y Pagos -->
            <div class="col-md-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="card-title">Reservas y Comprobantes</h5>
                        <p class="card-text">Verificar comprobantes de pago (SINPE/Transferencia) y solicitudes de clientes[cite: 15].</p>
                        <a href="reservas.php" class="btn btn-primary">Ver Reservas</a>
                    </div>
                </div>
            </div>

            <!-- Módulo de Reportes -->
            <div class="col-md-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="card-title">Reportes y Consultas</h5>
                        <p class="card-text">Consultar estadísticas de destinos más populares y estado general de ventas[cite: 17].</p>
                        <a href="reportes.php" class="btn btn-primary">Ver Reportes</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>