<?php 
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: /login.php");
    exit;
}

require_once '../../src/Database.php';
require_once '../../src/functions.php';

$pdo = Database::getInstance()->getConnection();
$error_msg = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action']) && $_POST['action'] == 'delete') {
        $id = $_POST['id_uniforme'];
        $pdo->prepare("DELETE FROM uniformes WHERE id_uniforme = ?")->execute([$id]);
        header("Location: uniformes.php?status=deleted");
        exit();
    }

    $id_uniforme = $_POST['id_uniforme'] ?? null;
    $descripcion = $_POST['descripcion'];

    try {
        $pdo->beginTransaction();

        $url_imagen = null;
        if (!empty($_FILES['file_uniforme']['name'])) {
            $contenido_binario = file_get_contents($_FILES['file_uniforme']['tmp_name']);
            $url_imagen = $contenido_binario;
        }

        if ($id_uniforme) {
            if ($url_imagen) {
                $sql = "UPDATE uniformes SET descripcion=?, url_imagen=? WHERE id_uniforme=?";
                $pdo->prepare($sql)->execute([$descripcion, $url_imagen, $id_uniforme]);
            } else {
                $sql = "UPDATE uniformes SET descripcion=? WHERE id_uniforme=?";
                $pdo->prepare($sql)->execute([$descripcion, $id_uniforme]);
            }
            $msg = "updated";
        } else {
            $sql = "INSERT INTO uniformes (descripcion, url_imagen) VALUES (?, ?)";
            $pdo->prepare($sql)->execute([$descripcion, $url_imagen]);
            $msg = "success";
        }

        $pdo->commit();
        header("Location: uniformes.php?status=$msg");
        exit();
    } catch (PDOException $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        $error_msg = $e->getMessage();
    }
}

$uniformes = $pdo->query("SELECT * FROM uniformes ORDER BY id_uniforme DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Uniformes | Jabulani CRUD</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/css/style.css">

    <style>
        :root { --aero-cyan: #00fbff; }
        body {
            background: linear-gradient(135deg, rgba(140,0,0,0.95) 0%, rgba(20,0,0,1) 100%), 
                url('https://media1.giphy.com/media/v1.Y2lkPTc5MGI3NjExaHAwNG9uaG5sdmhvMHY2dmFxajd5eXE2OGJjNXFoZHMwY3d1cndjMiZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/6uqz4G16YT8u80b7aX/giphy.gif');
            background-attachment: fixed;
            background-size: cover;
            color: white;
        }
        .img-preview {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 10px;
            border: 1px solid var(--aero-cyan);
        }
        .data-label { color: var(--aero-cyan); font-size: 0.75rem; font-weight: bold; text-transform: uppercase; }
        .form-control, .form-select {
            background: rgba(255, 255, 255, 0.15) !important;
            color: #fff !important;
        }
        .form-control::placeholder { color: rgba(255,255,255,0.6); }
    </style>
</head>
<body>
    <?php include '../../views/layouts/navbar_admin.php'; ?>

    <div class="container py-5">
        <h1 class="text-center glow-title mb-5"><i class="bi bi-tshirt"></i> Archivo de Uniformes</h1>

        <?php if ($error_msg): ?>
        <div class="alert alert-danger"><?= h($error_msg) ?></div>
        <?php endif; ?>

        <div class="glass-card p-4 mb-5">
            <h5 class="mb-4 text-white"><i class="bi bi-plus-square-dotted text-info me-2"></i>REGISTRAR NUEVO KIT</h5>
            <form method="POST" enctype="multipart/form-data" class="row g-3">
                <div class="col-md-7">
                    <label class="data-label">Descripción del Uniforme</label>
                    <input type="text" name="descripcion" class="form-control" required placeholder="Ej: Kit Titular 2024">
                </div>
                <div class="col-md-5">
                    <label class="data-label">Archivo de Imagen</label>
                    <input type="file" name="file_uniforme" class="form-control" required>
                </div>
                <div class="col-12 text-end mt-4">
                    <button type="submit" class="btn btn-glossy btn-glossy-cyan px-5 py-2">SUBIR AL SISTEMA</button>
                </div>
            </form>
        </div>

        <div class="glass-card p-4">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr class="text-info">
                            <th>VISTA PREVIA</th>
                            <th>DESCRIPCIÓN</th>
                            <th class="text-center">GESTIÓN</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($uniformes as $uni): ?>
                        <tr>
                            <td>
                                <?php if (!empty($uni['url_imagen'])): ?>
                                <img src="data:image/jpeg;base64,<?= base64_encode($uni['url_imagen']) ?>" class="img-preview" alt="Kit">
                                <?php endif; ?>
                            </td>
                            <td class="fw-bold"><?= h($uni['descripcion']) ?></td>
                            <td class="text-center">
                                <button class="btn btn-glossy btn-sm me-2 btn-edit" 
                                        data-json='<?= json_encode($uni) ?>'
                                        data-bs-toggle="modal" data-bs-target="#editModal">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este uniforme?')">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id_uniforme" value="<?= $uni['id_uniforme'] ?>">
                                    <button type="submit" class="btn btn-glossy btn-glossy-red btn-sm">
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
                    <div class="col-12 text-center mb-3">
                        <label class="data-label">IMAGEN ACTUAL</label><br>
                        <img id="edit_imagen_preview" src="" style="max-width: 150px; max-height: 150px; border-radius: 8px; display: none;">
                    </div>
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
                    <button type="submit" class="btn btn-glossy btn-glossy-cyan w-100 py-3">ACTUALIZAR ARCHIVO</button>
                </div>
            </form>
        </div>
    </div>

    <?php include '../../views/layouts/footer.php'; ?>

    <script>
    document.querySelectorAll('.btn-edit').forEach(btn => {
        btn.addEventListener('click', function() {
            const data = JSON.parse(this.getAttribute('data-json'));
            document.getElementById('edit_id').value = data.id_uniforme;
            document.getElementById('edit_descripcion').value = data.descripcion;
            
            const imgPreview = document.getElementById('edit_imagen_preview');
            if (data.url_imagen) {
                imgPreview.src = 'data:image/jpeg;base64,' + btoa(String.fromCharCode.apply(null, new Uint8Array(data.url_imagen)));
                imgPreview.style.display = 'inline-block';
            } else {
                imgPreview.style.display = 'none';
            }
        });
    });
    </script>
</body>
</html>