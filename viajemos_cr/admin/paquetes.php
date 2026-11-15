<?php
session_start();
require_once '../conexion.php';

// Validar sesión de usuario administrador o asesor
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

// Obtener estadísticas rápidas para las tarjetas superiores
$total_paquetes = $pdo->query("SELECT COUNT(*) FROM tbl_paquetes")->fetchColumn();
$total_activos = $pdo->query("SELECT COUNT(*) FROM tbl_paquetes WHERE estado = 1")->fetchColumn();
$total_cupos = $pdo->query("SELECT SUM(cupos_disponibles) FROM tbl_paquetes")->fetchColumn() ?: 0;
$total_internacionales = $pdo->query("SELECT COUNT(*) FROM tbl_paquetes p INNER JOIN tbl_categorias c ON p.id_categoria = c.id_categoria WHERE c.nombre LIKE '%Internacional%'")->fetchColumn();

// Obtener la lista de paquetes con su categoría 
$sql = "SELECT p.*, c.nombre as categoria_nombre 
        FROM tbl_paquetes p 
        INNER JOIN tbl_categorias c ON p.id_categoria = c.id_categoria 
        ORDER BY p.id_paquete DESC";
$stmt = $pdo->query($sql);
$paquetes = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Módulo 2 • Gestión de Paquetes — SGRT Viajemos por el Mundo CR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body { background-color: #f4f7f6; font-family: system-ui, -apple-system, sans-serif; }
        .navbar-custom { background-color: #0b3c65; }
        .card-stat-blue { background: linear-gradient(135deg, #0d6efd, #0b3c65); color: white; }
        .card-stat-green { background: linear-gradient(135deg, #198754, #146c43); color: white; }
        .card-stat-orange { background: linear-gradient(135deg, #fd7e14, #d35400); color: white; }
        .card-stat-purple { background: linear-gradient(135deg, #6f42c1, #563d7c); color: white; }
    </style>
</head>
<body>

    <!-- Barra de navegación superior -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom shadow-sm">
        <div class="container-fluid px-4">
            <a class="navbar-brand d-flex align-items-center" href="dashboard.php">
                <span class="fw-bold">✈️ VIAJEMOS POR EL MUNDO CR</span>
            </a>
            <div class="d-flex text-white align-items-center">
                <span class="me-3 small">Admin | SGRT • Sesión activa</span>
                <a href="dashboard.php" class="btn btn-outline-light btn-sm me-2">Dashboard</a>
                <a href="logout.php" class="btn btn-danger btn-sm">Cerrar Sesión</a>
            </div>
        </div>
    </nav>

    <!-- Contenido principal -->
    <div class="container-fluid px-4 my-4">
        
        <!-- Encabezado del Módulo -->
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="fw-bold text-dark">Módulo 2 • Gestión de Paquetes Turísticos</h2>
                <p class="text-muted">Listado general, creación y control de inventario para nacionales (CRC) y extranjeros (USD).</p>
            </div>
        </div>

        <!-- Tarjetas de Estadísticas -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card card-stat-blue border-0 shadow-sm rounded-4">
                    <div class="card-body p-3">
                        <div class="text-white-50 small text-uppercase fw-bold">Total Paquetes</div>
                        <div class="fs-2 fw-bold my-1"><?php echo $total_paquetes; ?></div>
                        <div class="small"><i class="bi bi-box-seam"></i> Registrados</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-stat-green border-0 shadow-sm rounded-4">
                    <div class="card-body p-3">
                        <div class="text-white-50 small text-uppercase fw-bold">Activos</div>
                        <div class="fs-2 fw-bold my-1"><?php echo $total_activos; ?></div>
                        <div class="small"><i class="bi bi-check-circle"></i> Disponibles</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-stat-orange border-0 shadow-sm rounded-4">
                    <div class="card-body p-3">
                        <div class="text-white-50 small text-uppercase fw-bold">Cupos</div>
                        <div class="fs-2 fw-bold my-1"><?php echo $total_cupos; ?></div>
                        <div class="small"><i class="bi bi-people"></i> Habilitados</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-stat-purple border-0 shadow-sm rounded-4">
                    <div class="card-body p-3">
                        <div class="text-white-50 small text-uppercase fw-bold">Internacionales</div>
                        <div class="fs-2 fw-bold my-1"><?php echo $total_internacionales; ?></div>
                        <div class="small"><i class="bi bi-globe"></i> Destinos fuera</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección de Tabla y Controles -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h4 class="fw-bold mb-0">Paquetes Turísticos</h4>
                                <span class="text-muted small">Listado de precios (Nacionales / Extranjeros)</span>
                            </div>
                            <div>
                                <a href="crear_paquete.php" class="btn btn-primary btn-sm px-3 py-2 rounded-pill"><i class="bi bi-plus-lg"></i> Nuevo Paquete</a>
                                <a href="crear_paquete.php" class="btn btn-outline-secondary btn-sm px-3 py-2 rounded-pill"><i class="bi bi-calendar-event"></i> Gestionar Fechas</a>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Título</th>
                                        <th>Categoría</th>
                                        <th>Precio (Colones ₡)</th>
                                        <th>Precio (Dólares $)</th>
                                        <th>Cupos</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($paquetes) > 0): ?>
                                        <?php foreach ($paquetes as $pkg): ?>
                                            <tr>
                                                <td><strong><?php echo htmlspecialchars($pkg['titulo']); ?></strong></td>
                                                <td>
                                                    <span class="badge bg-primary">
                                                        <?php echo htmlspecialchars($pkg['categoria_nombre']); ?>
                                                    </span>
                                                </td>
                                                <td><span class="text-success fw-bold">₡<?php echo number_format($pkg['precio_colones'] ?? 0, 2); ?></span></td>
                                                <td><span class="text-primary fw-bold">$<?php echo number_format($pkg['precio_usd'], 2); ?></span></td>
                                                <td><?php echo $pkg['cupos_disponibles']; ?></td>
                                                <td>
                                                    <?php if ($pkg['estado'] == 1): ?>
                                                        <span class="badge bg-success bg-opacity-10 text-success">Activo</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary bg-opacity-10 text-secondary">Inactivo</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <a href="editar_paquete.php?id=<?php echo $pkg['id_paquete']; ?>" class="btn btn-sm btn-light text-primary" title="Editar"><i class="bi bi-pencil-square"></i></a>
                                                    <a href="eliminar_paquete.php?id=<?php echo $pkg['id_paquete']; ?>" class="btn btn-sm btn-light text-danger" title="Eliminar" onclick="return confirm('¿Estás seguro de que deseas eliminar este paquete turístico?');"><i class="bi bi-trash"></i></a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-4">No hay paquetes turísticos registrados en este momento.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>