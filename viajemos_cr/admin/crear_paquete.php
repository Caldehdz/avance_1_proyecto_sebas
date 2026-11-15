<?php
session_start();
require_once '../conexion.php';

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

// Obtener categorías para el select
$categorias = $pdo->query("SELECT id_categoria, nombre FROM tbl_categorias ORDER BY nombre ASC")->fetchAll();

$mensaje_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo']);
    $id_categoria = (int)$_POST['id_categoria'];
    $descripcion = trim($_POST['descripcion']);
    $precio_colones = (float)$_POST['precio_colones'];
    $precio_usd = (float)$_POST['precio_usd'];
    $cupos = (int)$_POST['cupos_disponibles'];
    $estado = isset($_POST['estado']) ? 1 : 0;
    $ruta_imagen = '';

    // Validar y procesar imagen principal (JPG, PNG - Máx 5MB)
    if (isset($_FILES['imagen_principal']) && $_FILES['imagen_principal']['error'] === UPLOAD_ERR_OK) {
        $archivo = $_FILES['imagen_principal'];
        $permitidos = ['image/jpeg', 'image/png', 'image/webp'];
        $max_tamano = 5 * 1024 * 1024; // 5MB

        if (!in_array($archivo['type'], $permitidos)) {
            $mensaje_error = "Formato no válido. Solo se permiten imágenes JPG, PNG o WEBP.";
        } elseif ($archivo['size'] > $max_tamano) {
            $mensaje_error = "La imagen supera el tamaño máximo permitido de 5MB.";
        } else {
            $directorio = '../uploads/paquetes/';
            if (!is_dir($directorio)) {
                mkdir($directorio, 0777, true);
            }
            $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
            $nombre_archivo = uniqid('pkg_') . '.' . $extension;
            $destino = $directorio . $nombre_archivo;

            if (move_uploaded_file($archivo['tmp_name'], $destino)) {
                $ruta_imagen = 'uploads/paquetes/' . $nombre_archivo;
            } else {
                $mensaje_error = "Error al guardar la imagen en el servidor.";
            }
        }
    }

    if (empty($mensaje_error)) {
        try {
            $sql = "INSERT INTO tbl_paquetes (id_categoria, titulo, descripcion, precio_colones, precio_usd, cupos_disponibles, imagen_principal, estado) 
                    VALUES (:id_categoria, :titulo, :descripcion, :precio_colones, :precio_usd, :cupos, :imagen, :estado)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'id_categoria' => $id_categoria,
                'titulo' => $titulo,
                'descripcion' => $descripcion,
                'precio_colones' => $precio_colones,
                'precio_usd' => $precio_usd,
                'cupos' => $cupos,
                'imagen' => $ruta_imagen,
                'estado' => $estado
            ]);

            header("Location: paquetes.php?msg=creado");
            exit();
        } catch (PDOException $e) {
            $mensaje_error = "Error al guardar en base de datos: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Paquete — SGRT Viajemos por el Mundo CR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body { background-color: #f4f7f6; font-family: system-ui, -apple-system, sans-serif; }
        .navbar-custom { background-color: #0b3c65; }
        .card-form { border: 2px solid #0d6efd; border-radius: 1rem; }
        .btn-primary-custom { background-color: #0b3c65; border-color: #0b3c65; }
        .btn-primary-custom:hover { background-color: #082b49; border-color: #082b49; }
        .upload-box { border: 2px dashed #cbd5e1; border-radius: 0.75rem; background-color: #f8fafc; cursor: pointer; transition: 0.2s; }
        .upload-box:hover { border-color: #0d6efd; background-color: #eff6ff; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom shadow-sm">
        <div class="container-fluid px-4">
            <a class="navbar-brand fw-bold" href="dashboard.php">✈️ VIAJEMOS POR EL MUNDO CR</a>
            <div class="d-flex align-items-center">
                <a href="paquetes.php" class="btn btn-outline-light btn-sm me-2"><i class="bi bi-arrow-left"></i> Volver a Paquetes</a>
                <a href="logout.php" class="btn btn-danger btn-sm">Cerrar Sesión</a>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card card-form shadow-sm bg-white">
                    <div class="card-body p-4 p-md-5">
                        <h3 class="fw-bold text-dark mb-1">Registrar Paquete</h3>
                        <p class="text-muted small mb-4">Complete la información del tour con tarifas para turistas nacionales y extranjeros.</p>

                        <?php if (!empty($mensaje_error)): ?>
                            <div class="alert alert-danger"><?php echo htmlspecialchars($mensaje_error); ?></div>
                        <?php endif; ?>

                        <form action="crear_paquete.php" method="POST" enctype="multipart/form-data">
                            <div class="row g-3">
                                <!-- Título -->
                                <div class="col-md-8">
                                    <label for="titulo" class="form-label fw-semibold">Título del Paquete</label>
                                    <input type="text" class="form-control" id="titulo" name="titulo" placeholder="Ej. Volcán Irazú y Jardín Botánico" required>
                                </div>

                                <!-- Categoría -->
                                <div class="col-md-4">
                                    <label for="id_categoria" class="form-label fw-semibold">Categoría</label>
                                    <select class="form-select" id="id_categoria" name="id_categoria" required>
                                        <option value="">Seleccione...</option>
                                        <?php foreach ($categorias as $cat): ?>
                                            <option value="<?php echo $cat['id_categoria']; ?>">
                                                <?php echo htmlspecialchars($cat['nombre']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Descripción -->
                                <div class="col-12">
                                    <label for="descripcion" class="form-label fw-semibold">Descripción</label>
                                    <textarea class="form-control" id="descripcion" name="descripcion" rows="3" placeholder="Descripción detallada del paquete turístico..." required></textarea>
                                </div>

                                <!-- Precio Colones -->
                                <div class="col-md-4">
                                    <label for="precio_colones" class="form-label fw-semibold text-success">Precio Nacional (CRC ₡)</label>
                                    <div class="input-group">
                                        <span class="input-group-text">₡</span>
                                        <input type="number" step="0.01" min="0" class="form-control" id="precio_colones" name="precio_colones" placeholder="0.00" required>
                                    </div>
                                </div>

                                <!-- Precio Dólares -->
                                <div class="col-md-4">
                                    <label for="precio_usd" class="form-label fw-semibold text-primary">Precio Extranjero (USD $)</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" step="0.01" min="0" class="form-control" id="precio_usd" name="precio_usd" placeholder="0.00" required>
                                    </div>
                                </div>

                                <!-- Cupos Disponibles -->
                                <div class="col-md-4">
                                    <label for="cupos_disponibles" class="form-label fw-semibold">Cupos Disponibles</label>
                                    <input type="number" min="1" class="form-control" id="cupos_disponibles" name="cupos_disponibles" placeholder="Ej. 25" required>
                                </div>

                                <!-- Imagen Principal -->
                                <div class="col-12">
                                    <label for="imagen_principal" class="form-label fw-semibold">Imagen Principal</label>
                                    <div class="upload-box p-4 text-center" onclick="document.getElementById('imagen_principal').click();">
                                        <i class="bi bi-cloud-arrow-up fs-2 text-primary"></i>
                                        <div class="fw-semibold text-dark mt-1" id="nombre_archivo_label">Cargar imagen</div>
                                        <div class="text-muted small">JPG, PNG o WEBP — Máx. 5MB</div>
                                        <input type="file" class="d-none" id="imagen_principal" name="imagen_principal" accept="image/jpeg,image/png,image/webp" onchange="mostrarNombreArchivo(this)">
                                    </div>
                                </div>

                                <!-- Estado Activo -->
                                <div class="col-12 mt-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="estado" name="estado" checked>
                                        <label class="form-check-label fw-semibold" for="estado">Publicar paquete inmediatamente (Activo)</label>
                                    </div>
                                </div>

                                <!-- Botones -->
                                <div class="col-12 mt-4 d-flex gap-2">
                                    <button type="submit" class="btn btn-primary-custom text-white px-4 py-2 rounded-3 flex-grow-1 fw-semibold">
                                        <i class="bi bi-save me-1"></i> Guardar Paquete
                                    </button>
                                    <button type="reset" class="btn btn-outline-secondary px-4 py-2 rounded-3 fw-semibold" onclick="resetearLabel()">
                                        Limpiar
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function mostrarNombreArchivo(input) {
            const label = document.getElementById('nombre_archivo_label');
            if (input.files && input.files[0]) {
                label.textContent = "Seleccionado: " + input.files[0].name;
                label.classList.add('text-primary');
            }
        }
        function resetearLabel() {
            const label = document.getElementById('nombre_archivo_label');
            label.textContent = "Cargar imagen";
            label.classList.remove('text-primary');
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>