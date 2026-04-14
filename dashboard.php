<?php 
require 'conexion.php';

// 1. Consultas para llenar los selectores
$entrenadores = $pdo->query('SELECT id_entrenador, nombre, apellido FROM entrenador')->fetchAll(PDO::FETCH_ASSOC);
$estadios = $pdo->query('SELECT id_estadio, nombre FROM estadios')->fetchAll(PDO::FETCH_ASSOC);

$error_msg = null;

// 2. Lógica de Procesamiento al enviar el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name_equipo   = $_POST['name_equipo'];
    $apodos        = $_POST['apodos'];
    $fundacion     = $_POST['fundacion'];
    $id_entrenador = !empty($_POST['entrenador']) ? $_POST['entrenador'] : null;
    $id_estadio    = !empty($_POST['estadios'])   ? $_POST['estadios']   : null;

    $id_uniforme = null;
    $id_logo     = null;

    try {
        // INICIO DE TRANSACCIÓN (Opcional pero recomendado para que no guarde a medias)
        $pdo->beginTransaction();

        // --- PROCESAR UNIFORME ---
        if (!empty($_FILES['file_uniforme']['name'])) {
            $nombre_uni = time() . "_uni_" . $_FILES['file_uniforme']['name'];
            $ruta_final_uni = "uploads/" . $nombre_uni;

            if (move_uploaded_file($_FILES['file_uniforme']['tmp_name'], $ruta_final_uni)) {
                $stmt_uni = $pdo->prepare("INSERT INTO uniformes (url_imagen) VALUES (:url)");
                $stmt_uni->execute([':url' => $ruta_final_uni]);
                $id_uniforme = $pdo->lastInsertId();
            }
        }

        // --- PROCESAR LOGO ---
        if (!empty($_FILES['file_logo']['name'])) {
            $nombre_log = time() . "_logo_" . $_FILES['file_logo']['name'];
            $ruta_final_log = "uploads/" . $nombre_log;

            if (move_uploaded_file($_FILES['file_logo']['tmp_name'], $ruta_final_log)) {
                // Ajusta 'nombre_logo' si en tu DB se llama 'url_logo'
                $stmt_log = $pdo->prepare("INSERT INTO logos (nombre_logo) VALUES (:url)");
                $stmt_log->execute([':url' => $ruta_final_log]);
                $id_logo = $pdo->lastInsertId();
            }
        }

        // --- INSERTAR EQUIPO ---
        $sql = "INSERT INTO equipos (nombre, apodo, fundacion, id_entrenador, id_estadio, id_uniforme, id_logo) 
                VALUES (:nom, :apo, :fun, :ent, :est, :uni, :log)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nom' => $name_equipo,
            ':apo' => $apodos,
            ':fun' => $fundacion,
            ':ent' => $id_entrenador,
            ':est' => $id_estadio,
            ':uni' => $id_uniforme, 
            ':log' => $id_logo
        ]);

        $pdo->commit(); // Confirmamos los cambios

        header("Location: " . $_SERVER['PHP_SELF'] . "?status=success");
        exit();

    } catch (PDOException $e) {
        if ($pdo->inTransaction()) $pdo->rollBack(); // Si algo falla, deshace lo que alcanzó a insertar
        $error_msg = "Error: " . $e->getMessage();
    }
}
?>

<!<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registro de Equipos | Soccer Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        body {
            background-color: #f4f7f6;
            font-family: 'Segoe UI', Roboto, sans-serif;
            background-image: url('https://media1.giphy.com/media/v1.Y2lkPTc5MGI3NjExaHAwNG9uaG5sdmhvMHY2dmFxajd5eXE2OGJjNXFoZHMwY3d1cndjMiZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/6uqz4G16YT8u80b7aX/giphy.gif');
            background-repeat: repeat;
            background-size: 300px; /* <--- Esto hace que las llamas sean más grandes y parezcan más lentas */
 /* El fondo se queda quieto mientras haces scroll, ¡muy pro! */

        }
        .card {
            border: none;
            border-radius: 15px;
        }
        .form-title {
            color: #2c3e50;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .btn-success {
            background-color: #27ae60;
            border: none;
            padding: 12px;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        .btn-success:hover {
            background-color: #219150;
            transform: translateY(-1px);
        }
        .input-group-text {
            background-color: #f8f9fa;
            color: #6c757d;
        }
    </style>
</head>

<body>
    <?php include './componentes/navbarlogin.php' ?>

    <main class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                
                <?php if(isset($_GET['status']) && $_GET['status'] == 'success'): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i> ¡Equipo registrado con éxito!
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if(isset($error_msg)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= $error_msg ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <div class="card shadow-lg">
                    <div class="card-body p-4 p-md-5">
                        <div class="text-center mb-4">
                            <i class="bi bi-trophy text-success" style="font-size: 3rem;"></i>
                            <h2 class="form-title mt-2">Registro de Equipo</h2>
                            <p class="text-muted">Completa los datos para inscribir al club</p>
                        </div>

                        <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" enctype="multipart/form-data">
                            
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Nombre del Equipo</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-shield-shaded"></i></span>
                                    <input type="text" name="name_equipo" class="form-control" placeholder="Ej: Real Madrid" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">Apodo</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-chat-quote"></i></span>
                                        <input type="text" name="apodos" class="form-control" placeholder="Ej: Merengues">
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">Fecha de Fundación</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-calendar-event"></i></span>
                                        <input type="date" name="fundacion" class="form-control">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Director Técnico</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-person-badge"></i></span>
                                    <select name="entrenador" class="form-select">
                                        <option value="">-- Seleccionar Entrenador --</option>
                                        <?php foreach ($entrenadores as $e): ?>
                                            <option value="<?= $e['id_entrenador']?>"><?= $e['nombre'] . " " . $e['apellido'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Estadio Principal</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-building"></i></span>
                                    <select name="estadios" class="form-select">
                                        <option value="">-- Seleccionar Estadio --</option>
                                        <?php foreach ($estadios as $es): ?>
                                            <option value="<?= $es['id_estadio']?>"><?= $es['nombre']?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <hr class="my-4 text-muted">

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold small"><i class="bi bi-tsert-fill me-1"></i> Uniforme</label>
                                    <input type="file" name="file_uniforme" class="form-control form-control-sm" accept="image/*">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold small"><i class="bi bi-image me-1"></i> Escudo / Logo</label>
                                    <input type="file" name="file_logo" class="form-control form-control-sm" accept="image/*">
                                </div>
                            </div>

                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="bi bi-plus-circle me-2"></i>Registrar Equipo
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include './componentes/footer.php' ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>