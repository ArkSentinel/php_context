<?php 
require 'conexion.php';

// --- 1. LÓGICA DE PROCESAMIENTO (CRUD) ---
$error_msg = null;
$status = $_GET['status'] ?? null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // ACCIÓN: ELIMINAR
    if (isset($_POST['action']) && $_POST['action'] == 'delete') {
        $id = $_POST['id_uniforme'];
        $pdo->prepare("DELETE FROM uniformes WHERE id_uniforme = ?")->execute([$id]);
        header("Location: " . $_SERVER['PHP_SELF'] . "?status=deleted");
        exit();
    }

    // DATOS DEL FORMULARIO
    $id_uniforme = $_POST['id_uniforme'] ?? null;
    $descripcion = $_POST['descripcion'];

    try {
        $pdo->beginTransaction();

        // Procesar imagen del Uniforme
        $url_imagen = null;
        if (!empty($_FILES['file_uniforme']['name'])) {
            $nombre_img = time() . "_kit_" . $_FILES['file_uniforme']['name'];
            $ruta = "uploads/" . $nombre_img;
            if (move_uploaded_file($_FILES['file_uniforme']['tmp_name'], $ruta)) {
                $url_imagen = $ruta;
            }
        }

        if ($id_uniforme) {
            // ACTUALIZAR (UPDATE)
            if ($url_imagen) {
                $sql = "UPDATE uniformes SET descripcion=?, url_imagen=? WHERE id_uniforme=?";
                $pdo->prepare($sql)->execute([$descripcion, $url_imagen, $id_uniforme]);
            } else {
                $sql = "UPDATE uniformes SET descripcion=? WHERE id_uniforme=?";
                $pdo->prepare($sql)->execute([$descripcion, $id_uniforme]);
            }
            $msg = "updated";
        } else {
            // INSERTAR (CREATE)
            $sql = "INSERT INTO uniformes (descripcion, url_imagen) VALUES (?, ?)";
            $pdo->prepare($sql)->execute([$descripcion, $url_imagen]);
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

// --- 2. CONSULTA DE UNIFORMES ---
$uniformes = $pdo->query("SELECT * FROM uniformes ORDER BY id_uniforme DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Uniformes | Jabulani Aero CRUD</title>
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
        .table thead { background: rgba(255, 255, 255, 0.1); }

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
            background: rgba(40, 0, 0, 0.95);
            backdrop-filter: blur(25px);
            border: 1px solid var(--aero-cyan);
            border-radius: 30px;
        }

        .img-preview {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 10px;
            border: 1px solid var(--aero-cyan);
        }

        .data-label { color: var(--aero-cyan); font-size: 0.75rem; font-weight: bold; text-transform: uppercase; }
    </style>
</head>
<body>

    <?php include './componentes/navbarlogin.php' ?>

    <div class="container py-5">
        <h1 class="text-center glow-title mb-5"><i class="bi bi-tshirt"></i> Archivo de Uniformes</h1>

        <div class="glass-card p-4 mb-5">
            <h5 class="mb-4 text-white"><i class="bi bi-plus-square-dotted text-info me-2"></i>REGISTRAR NUEVO KIT</h5>
            <form method="POST" enctype="multipart/form-data" class="row g-3">
                <div class="col-md-7">
                    <label class="data-label">Descripción del Uniforme</label>
                    <input type="text" name="descripcion" class="form-control" required placeholder="Ej: Kit Titular 2024 - Universidad de Chile">
                </div>
                <div class="col-md-5">
                    <label class="data-label">Archivo de Imagen</label>
                    <input type="file" name="file_uniforme" class="form-control" required>
                </div>
                <div class="col-12 text-end mt-4">
                    <button type="submit" class="btn btn-aero px-5 py-2">SUBIR AL SISTEMA</button>
                </div>
            </form>
        </div>

        <div class="glass-card p-4">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr class="text-info">
                            <th>VISTA PREVIA</th>
                            <th>DESCRIPCIÓN DEL EQUIPAMIENTO</th>
                            <th class="text-center">GESTIÓN</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($uniformes as $uni): ?>
                        <tr>
                            <td>
                                <img src="<?= htmlspecialchars($uni['url_imagen']) ?>" class="img-preview" alt="Kit">
                            </td>
                            <td class="fw-bold"><?= htmlspecialchars($uni['descripcion']) ?></td>
                            <td class="text-center">
                                <button class="btn btn-aero btn-sm me-2 btn-edit" 
                                        data-json='<?= json_encode($uni) ?>'
                                        data-bs-toggle="modal" data-bs-target="#editModal">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este uniforme del archivo?')">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id_uniforme" value="<?= $uni['id_uniforme'] ?>">
                                    <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill">
                                        <i class="bi bi-trash"></i>
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
        <div class="modal-dialog">
            <form method="POST" enctype="multipart/form-data" class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title glow-title text-info">Modificar Kit</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body row g-3">
                    <input type="hidden" name="id_uniforme" id="edit_id">
                    <div class="col-12">
                        <label class="data-label">Descripción</label>
                        <input type="text" name="descripcion" id="edit_descripcion" class="form-control">
                    </div>
                    <div class="col-12">
                        <label class="data-label">Cambiar Imagen (Opcional)</label>
                        <input type="file" name="file_uniforme" class="form-control">
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="submit" class="btn btn-aero w-100 py-3">ACTUALIZAR ARCHIVO</button>
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
                document.getElementById('edit_id').value = data.id_uniforme;
                document.getElementById('edit_descripcion').value = data.descripcion;
            });
        });
    </script>
</body>
</html>