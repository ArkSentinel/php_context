<?php 
require 'conexion.php';

// --- 1. LÓGICA DE PROCESAMIENTO (CRUD) ---
$error_msg = null;
$status = $_GET['status'] ?? null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // ACCIÓN: ELIMINAR
    if (isset($_POST['action']) && $_POST['action'] == 'delete') {
        $id = $_POST['id_estadio'];
        $pdo->prepare("DELETE FROM estadios WHERE id_estadio = ?")->execute([$id]);
        header("Location: " . $_SERVER['PHP_SELF'] . "?status=deleted");
        exit();
    }

    // DATOS DEL FORMULARIO
    $id_estadio = $_POST['id_estadio'] ?? null;
    $nombre     = $_POST['nombre'];
    $ciudad     = $_POST['ciudad'];
    $capacidad  = $_POST['capacidad'];

    try {
        $pdo->beginTransaction();

        // Procesar imagen del estadio
        $url_imagen = null;
        if (!empty($_FILES['file_estadio']['name'])) {
            $nombre_img = time() . "_stadium_" . $_FILES['file_estadio']['name'];
            $ruta = "uploads/" . $nombre_img;
            if (move_uploaded_file($_FILES['file_estadio']['tmp_name'], $ruta)) {
                $url_imagen = $ruta;
            }
        }

        if ($id_estadio) {
            // ACTUALIZAR (UPDATE)
            if ($url_imagen) {
                $sql = "UPDATE estadios SET nombre=?, ciudad=?, capacidad=?, url_imagen=? WHERE id_estadio=?";
                $pdo->prepare($sql)->execute([$nombre, $ciudad, $capacidad, $url_imagen, $id_estadio]);
            } else {
                $sql = "UPDATE estadios SET nombre=?, ciudad=?, capacidad=? WHERE id_estadio=?";
                $pdo->prepare($sql)->execute([$nombre, $ciudad, $capacidad, $id_estadio]);
            }
            $msg = "updated";
        } else {
            // INSERTAR (CREATE)
            $sql = "INSERT INTO estadios (nombre, ciudad, capacidad, url_imagen) VALUES (?, ?, ?, ?)";
            $pdo->prepare($sql)->execute([$nombre, $ciudad, $capacidad, $url_imagen]);
            $msg = "success";
        }

        $pdo->commit();
        header("Location: " . $_SERVER['PHP_SELF'] . "?status=$msg");
        exit();
    } catch (PDOException $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        $error_msg = $e->getMessage();
    }
}

// --- 2. CONSULTA DE ESTADIOS ---
$estadios = $pdo->query("SELECT * FROM estadios ORDER BY nombre ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Estadios | Jabulani Aero CRUD</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        :root {
            --aero-cyan: #00fbff;
            --glass-bg: rgba(255, 255, 255, 0.1);
        }

        body {
            background: linear-gradient(135deg, rgba(140,0,0,0.95) 0%, rgba(20,0,0,1) 100%), 
                        url('https://media1.giphy.com/media/v1.Y2lkPTc5MGI3NjExaHAwNG9uaG5sdmhvMHY2dmFxajd5eXE2OGJjNXFoZHMwY3d1cndjMiZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/6uqz4G16YT8u80b7aX/giphy.gif');
            background-attachment: fixed;
            background-size: cover;
            color: white;
            font-family: 'Segoe UI', sans-serif;
        }

        .glass-card {
            background: var(--glass-bg);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 25px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.6);
        }

        .glow-title {
            text-shadow: 0 0 15px var(--aero-cyan);
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .table { color: white !important; vertical-align: middle; }
        .table thead { background: rgba(0,0,0,0.4); border-radius: 10px; }

        .btn-aero {
            background: linear-gradient(180deg, rgba(255,255,255,0.3) 0%, rgba(0,0,0,0.2) 100%);
            border: 1px solid rgba(255, 255, 255, 0.4);
            color: white;
            font-weight: bold;
            border-radius: 50px;
            transition: 0.3s;
        }
        .btn-aero:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0, 251, 255, 0.4); }

        .modal-content {
            background: rgba(30, 0, 0, 0.95);
            backdrop-filter: blur(25px);
            border: 1px solid var(--aero-cyan);
            border-radius: 30px;
        }

        .data-label { color: var(--aero-cyan); font-size: 0.75rem; font-weight: bold; }
    </style>
</head>
<body>

    <?php include './componentes/navbarlogin.php' ?>

    <div class="container py-5">
        <h1 class="text-center glow-title mb-5"><i class="bi bi-building-gear"></i> Módulo de Estadios</h1>

        <div class="glass-card p-4 mb-5">
            <h5 class="mb-4"><i class="bi bi-plus-circle-fill text-info me-2"></i>NUEVO RECINTO DEPORTIVO</h5>
            <form method="POST" enctype="multipart/form-data" class="row g-3">
                <div class="col-md-5">
                    <label class="data-label">NOMBRE DEL ESTADIO</label>
                    <input type="text" name="nombre" class="form-control" required placeholder="Ej: Estadio Nacional">
                </div>
                <div class="col-md-4">
                    <label class="data-label">CIUDAD / UBICACIÓN</label>
                    <input type="text" name="ciudad" class="form-control" placeholder="Ej: Santiago, Chile">
                </div>
                <div class="col-md-3">
                    <label class="data-label">CAPACIDAD (AFORO)</label>
                    <input type="number" name="capacidad" class="form-control" placeholder="00000">
                </div>
                <div class="col-md-12">
                    <label class="data-label">FOTOGRAFÍA DEL RECINTO</label>
                    <input type="file" name="file_estadio" class="form-control">
                </div>
                <div class="col-12 text-end mt-4">
                    <button type="submit" class="btn btn-aero px-5 py-2">REGISTRAR EN BASE DE DATOS</button>
                </div>
            </form>
        </div>

        <div class="glass-card p-4">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr class="text-info">
                            <th>ESTADIO</th>
                            <th>UBICACIÓN</th>
                            <th>CAPACIDAD</th>
                            <th class="text-center">OPCIONES</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($estadios as $es): ?>
                        <tr>
                            <td class="fw-bold"><?= $es['nombre'] ?></td>
                            <td><i class="bi bi-geo-alt me-1"></i> <?= $es['ciudad'] ?></td>
                            <td><i class="bi bi-people-fill me-1"></i> <?= number_format($es['capacidad']) ?></td>
                            <td class="text-center">
                                <button class="btn btn-aero btn-sm me-2 btn-edit" 
                                        data-json='<?= json_encode($es) ?>'
                                        data-bs-toggle="modal" data-bs-target="#editModal">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <form method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este estadio?')">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id_estadio" value="<?= $es['id_estadio'] ?>">
                                    <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form method="POST" enctype="multipart/form-data" class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title glow-title text-info">Actualizar Recinto</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body row g-3">
                    <input type="hidden" name="id_estadio" id="edit_id">
                    <div class="col-md-6">
                        <label class="data-label">NOMBRE DEL ESTADIO</label>
                        <input type="text" name="nombre" id="edit_nombre" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="data-label">CIUDAD</label>
                        <input type="text" name="ciudad" id="edit_ciudad" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="data-label">CAPACIDAD AFORO</label>
                        <input type="number" name="capacidad" id="edit_capacidad" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="data-label">CAMBIAR IMAGEN</label>
                        <input type="file" name="file_estadio" class="form-control">
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="submit" class="btn btn-aero w-100 py-3">SINCRONIZAR DATOS</button>
                </div>
            </form>
        </div>
    </div>

    <?php include './componentes/footer.php' ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.querySelectorAll('.btn-edit').forEach(btn => {
            btn.addEventListener('click', function() {
                const data = JSON.parse(this.getAttribute('data-json'));
                document.getElementById('edit_id').value = data.id_estadio;
                document.getElementById('edit_nombre').value = data.nombre;
                document.getElementById('edit_ciudad').value = data.ciudad;
                document.getElementById('edit_capacidad').value = data.capacidad;
            });
        });
    </script>
</body>
</html>