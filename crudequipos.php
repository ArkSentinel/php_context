<?php 
require 'conexion.php';

$error_msg = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['action'])) {
    // Detectamos si es edición o nuevo
    $id_equipo = !empty($_POST['id_equipo']) ? $_POST['id_equipo'] : null;

    try {
        $pdo->beginTransaction();

        // 1. Función para subir archivos
        function subirArchivo($file, $tipo) {
            if (!empty($file['name'])) {
                if (!is_dir('uploads')) mkdir('uploads', 0777, true);
                $nombre = time() . "_" . $tipo . "_" . basename($file['name']);
                $ruta = "uploads/" . $nombre;
                if (move_uploaded_file($file['tmp_name'], $ruta)) return $ruta;
            }
            return null;
        }

        $url_logo_path = subirArchivo($_FILES['file_logo'], "logo");
        $url_uni_path  = subirArchivo($_FILES['file_uniforme'], "uni");

        if ($id_equipo) {
            // --- MODO EDICIÓN (UPDATE) ---
            
            // 1. Actualizar datos básicos del equipo
            $sql = "UPDATE equipos SET nombre=?, apodo=?, fundacion=?, id_entrenador=?, id_estadio=? WHERE id_equipo=?";
            $pdo->prepare($sql)->execute([
                $_POST['name_equipo'], $_POST['apodos'], 
                !empty($_POST['fundacion']) ? $_POST['fundacion'] : null,
                !empty($_POST['entrenador']) ? $_POST['entrenador'] : null,
                !empty($_POST['estadios']) ? $_POST['estadios'] : null,
                $id_equipo
            ]);

            // 2. Lógica Inteligente para el LOGO
            if ($url_logo_path) {
                // Verificamos si el equipo ya tiene un registro en la tabla logos
                $stmtCheck = $pdo->prepare("SELECT id_logo FROM equipos WHERE id_equipo = ?");
                $stmtCheck->execute([$id_equipo]);
                $current_logo_id = $stmtCheck->fetchColumn();

                if ($current_logo_id) {
                    // Si ya existe, actualizamos la tabla logos
                    $pdo->prepare("UPDATE logos SET url_logo=? WHERE id_logo=?")
                        ->execute([$url_logo_path, $current_logo_id]);
                } else {
                    // Si no existe, creamos el logo y lo vinculamos al equipo
                    $stmtIns = $pdo->prepare("INSERT INTO logos (nombre_logo, url_logo) VALUES (?, ?)");
                    $stmtIns->execute([$_POST['name_equipo'] . " Logo", $url_logo_path]);
                    $new_id_logo = $pdo->lastInsertId();
                    
                    $pdo->prepare("UPDATE equipos SET id_logo=? WHERE id_equipo=?")
                        ->execute([$new_id_logo, $id_equipo]);
                }
            }

            // 3. Lógica Inteligente para el UNIFORME
            if ($url_uni_path) {
                $stmtCheckU = $pdo->prepare("SELECT id_uniforme FROM equipos WHERE id_equipo = ?");
                $stmtCheckU->execute([$id_equipo]);
                $current_uni_id = $stmtCheckU->fetchColumn();

                if ($current_uni_id) {
                    // Actualizar si existe
                    $pdo->prepare("UPDATE uniformes SET url_imagen=? WHERE id_uniforme=?")
                        ->execute([$url_uni_path, $current_uni_id]);
                } else {
                    // Crear y vincular si no existe
                    $stmtInsU = $pdo->prepare("INSERT INTO uniformes (descripcion, url_imagen) VALUES (?, ?)");
                    $stmtInsU->execute(["Uniforme " . $_POST['name_equipo'], $url_uni_path]);
                    $new_id_uni = $pdo->lastInsertId();
                    
                    $pdo->prepare("UPDATE equipos SET id_uniforme=? WHERE id_equipo=?")
                        ->execute([$new_id_uni, $id_equipo]);
                }
            }
            $status = "updated";} else {
            // --- MODO CREACIÓN (INSERT) ---

            // Insertar Logo
            $id_logo_db = null;
            if ($url_logo_path) {
                $stmt = $pdo->prepare("INSERT INTO logos (nombre_logo, url_logo) VALUES (?, ?)");
                $stmt->execute([$_POST['name_equipo'] . " Logo", $url_logo_path]);
                $id_logo_db = $pdo->lastInsertId();
            }

            // Insertar Uniforme
            $id_uni_db = null;
            if ($url_uni_path) {
                $stmt = $pdo->prepare("INSERT INTO uniformes (descripcion, url_imagen) VALUES (?, ?)");
                $stmt->execute(["Uniforme " . $_POST['name_equipo'], $url_uni_path]);
                $id_uni_db = $pdo->lastInsertId();
            }

            // Insertar Equipo
            $sql = "INSERT INTO equipos (nombre, apodo, fundacion, id_entrenador, id_estadio, id_uniforme, id_logo) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            $pdo->prepare($sql)->execute([
                $_POST['name_equipo'], $_POST['apodos'],
                !empty($_POST['fundacion']) ? $_POST['fundacion'] : null,
                !empty($_POST['entrenador']) ? $_POST['entrenador'] : null,
                !empty($_POST['estadios']) ? $_POST['estadios'] : null,
                $id_uni_db, $id_logo_db
            ]);
            $status = "success";
        }

        $pdo->commit();
        header("Location: crudequipos.php?status=$status");
        exit();

    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        $error_msg = "Error: " . $e->getMessage();
    }
}

// Lógica para ELIMINAR
if (isset($_POST['action']) && $_POST['action'] == 'delete') {
    $id = $_POST['id_equipo'];
    $pdo->prepare("DELETE FROM equipos WHERE id_equipo = ?")->execute([$id]);
    header("Location: crudequipos.php?status=deleted");
    exit();
}

// CONSULTAS
$entrenadores = $pdo->query('SELECT id_entrenador, nombre, apellido FROM entrenador')->fetchAll(PDO::FETCH_ASSOC);
$estadios = $pdo->query('SELECT id_estadio, nombre FROM estadios')->fetchAll(PDO::FETCH_ASSOC);
$equipos = $pdo->query("SELECT e.*, ent.nombre as nom_ent, ent.apellido as ape_ent, est.nombre as nom_est 
                        FROM equipos e
                        LEFT JOIN entrenador ent ON e.id_entrenador = ent.id_entrenador
                        LEFT JOIN estadios est ON e.id_estadio = est.id_estadio")->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Jabulani CRUD | Estilo Aero</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <style>
    :root {
        --aero-red: rgba(255, 0, 0, 0.7);
        --aero-cyan: #00fbff;
    }

    body {
        background: linear-gradient(135deg, rgba(160, 0, 0, 0.9) 0%, rgba(40, 0, 0, 1) 100%),
            url('https://media1.giphy.com/media/v1.Y2lkPTc5MGI3NjExaHAwNG9uaG5sdmhvMHY2dmFxajd5eXE2OGJjNXFoZHMwY3d1cndjMiZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/6uqz4G16YT8u80b7aX/giphy.gif');
        background-attachment: fixed;
        background-size: cover;
        color: white;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    /* Paneles de Cristal (Frutiger Aero) */
    .glass-card {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(15px);
        -webkit-backdrop-filter: blur(15px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 25px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.5);
        transition: 0.4s;
    }

    /* Títulos con brillo */
    .glow-text {
        color: #fff;
        text-shadow: 0 0 10px rgba(255, 255, 255, 0.8), 0 0 20px var(--aero-cyan);
        font-weight: 800;
    }

    /* Tabla con transparencias */
    .table {
        color: white !important;
    }

    .table thead {
        background: rgba(255, 255, 255, 0.15);
    }

    .table-hover tbody tr:hover {
        background: rgba(255, 255, 255, 0.1);
    }

    /* Botones Glossy */
    .btn-glossy {
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.4) 0%, rgba(255, 255, 255, 0) 50%, rgba(0, 0, 0, 0.1) 100%);
        border: 1px solid rgba(255, 255, 255, 0.5);
        border-radius: 50px;
        color: white;
        font-weight: bold;
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
    }

    .btn-glossy-cyan {
        background-color: #0088ff;
    }

    .btn-glossy-red {
        background-color: #ff0000;
    }

    /* Modales */
    .modal-content {
        background: rgba(40, 0, 0, 0.9);
        backdrop-filter: blur(20px);
        border-radius: 30px;
        border: 1px solid var(--aero-cyan);
    }
    </style>
</head>

<body>

    <?php include './componentes/navbarlogin.php' ?>



    <div class="container mt-3">
        <?php if ($error_msg): ?>
        <div class="alert alert-danger shadow">
            <strong><i class="bi bi-exclamation-triangle"></i> Error de Base de Datos:</strong><br>
            <?= $error_msg ?>
        </div>
        <?php endif; ?>

        <?php if (!empty($debug_info)): ?>
        <div class="alert alert-info shadow-sm"
            style="background: rgba(0, 200, 255, 0.2); color: white; border: 1px solid cyan;">
            <strong><i class="bi bi-search"></i> Log de Procesamiento:</strong>
            <ul class="mb-0">
                <?php foreach ($debug_info as $log): ?>
                <li><?= $log ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>
    </div>


    <div class="container py-5">
        <h1 class="text-center glow-text mb-5">CENTRO DE MANDO</h1>

        <div class="glass-card p-4 mb-5">
            <h4 class="mb-4"><i class="bi bi-plus-circle-dotted text-cyan"></i> INGRESAR NUEVO EQUIPO</h4>
            <form method="POST" enctype="multipart/form-data" class="row g-3">
                <div class="col-md-4">
                    <label class="small fw-bold">NOMBRE OFICIAL</label>
                    <input type="text" name="name_equipo" class="form-control" required
                        placeholder="Ej: Universidad de Chile">
                </div>
                <div class="col-md-4">
                    <label class="small fw-bold">APODO</label>
                    <input type="text" name="apodos" class="form-control" placeholder="Ej: El Romántico Viajero">
                </div>
                <div class="col-md-4">
                    <label class="small fw-bold">FECHA FUNDACIÓN</label>
                    <input type="date" name="fundacion" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="small fw-bold">DIRECTOR TÉCNICO</label>
                    <select name="entrenador" class="form-select">
                        <option value="">Seleccionar Estratega...</option>
                        <?php foreach ($entrenadores as $e): ?>
                        <option value="<?= $e['id_entrenador']?>"><?= $e['nombre']." ".$e['apellido']?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="small fw-bold">RECINTO LOCAL</label>
                    <select name="estadios" class="form-select">
                        <option value="">Seleccionar Estadio...</option>
                        <?php foreach ($estadios as $es): ?>
                        <option value="<?= $es['id_estadio']?>"><?= $es['nombre']?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="small fw-bold">SUBIR ESCUDO</label>
                    <input type="file" name="file_logo" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="small fw-bold">SUBIR UNIFORME</label>
                    <input type="file" name="file_uniforme" class="form-control">
                </div>
                <div class="col-12 text-end">
                    <button type="submit" class="btn btn-glossy btn-glossy-cyan px-5 py-2 mt-3">REGISTRAR DATOS</button>
                </div>
            </form>
        </div>

        <div class="glass-card p-4">
            <h4 class="mb-4 border-bottom pb-2">EQUIPOS REGISTRADOS</h4>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr class="text-info">
                            <th>IDENTIDAD</th>
                            <th>FUNDACIÓN</th>
                            <th>ESTRATEGIA (DT)</th>
                            <th>LOCALÍA</th>
                            <th class="text-center">GESTIÓN</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($equipos as $eq): ?>
                        <tr>
                            <td>
                                <span class="fw-bold"><?= $eq['nombre'] ?></span><br>
                                <span class="small opacity-75 text-cyan"><?= $eq['apodo'] ?></span>
                            </td>
                            <td><?= $eq['fundacion'] ?></td>
                            <td><?= $eq['nom_ent']." ".$eq['ape_ent'] ?></td>
                            <td><?= $eq['nom_est'] ?></td>
                            <td class="text-center">
                                <button class="btn btn-glossy btn-sm me-2 btn-edit" data-json='<?= json_encode($eq) ?>'
                                    data-bs-toggle="modal" data-bs-target="#editModal">
                                    <i class="bi bi-pencil-fill"></i>
                                </button>
                                <form method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar registro?')">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id_equipo" value="<?= $eq['id_equipo'] ?>">
                                    <button type="submit" class="btn btn-glossy btn-glossy-red btn-sm">
                                        <i class="bi bi-trash-fill"></i>
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
                    <h5 class="modal-title glow-text">MODIFICAR EXPEDIENTE</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body row g-3">
                    <input type="hidden" name="id_equipo" id="e_id">
                    <div class="col-md-6">
                        <label class="small fw-bold">NOMBRE CLUB</label>
                        <input type="text" name="name_equipo" id="e_nombre" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="small fw-bold">APODO</label>
                        <input type="text" name="apodos" id="e_apodo" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="small fw-bold">DIRECTOR TÉCNICO</label>
                        <select name="entrenador" id="e_entrenador" class="form-select">
                            <?php foreach ($entrenadores as $e): ?>
                            <option value="<?= $e['id_entrenador']?>"><?= $e['nombre']." ".$e['apellido']?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="small fw-bold">ESTADIO</label>
                        <select name="estadios" id="e_estadio" class="form-select">
                            <?php foreach ($estadios as $es): ?>
                            <option value="<?= $es['id_estadio']?>"><?= $es['nombre']?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="small fw-bold">NUEVO LOGO</label>
                        <input type="file" name="file_logo" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="small fw-bold">NUEVO UNIFORME</label>
                        <input type="file" name="file_uniforme" class="form-control">
                    </div>
                    <div class="col-md-12">
                        <label class="small fw-bold">FECHA FUNDACIÓN</label>
                        <input type="date" name="fundacion" id="e_fundacion" class="form-control">
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="submit" class="btn btn-glossy btn-glossy-cyan w-100 py-3">SINCRONIZAR CAMBIOS</button>
                </div>
            </form>
        </div>
    </div>

    <?php include './componentes/footer.php' ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Lógica para poblar el modal
    document.querySelectorAll('.btn-edit').forEach(btn => {
        btn.addEventListener('click', function() {
            const d = JSON.parse(this.getAttribute('data-json'));
            document.getElementById('e_id').value = d.id_equipo;
            document.getElementById('e_nombre').value = d.nombre;
            document.getElementById('e_apodo').value = d.apodo;
            document.getElementById('e_fundacion').value = d.fundacion;
            document.getElementById('e_entrenador').value = d.id_entrenador;
            document.getElementById('e_estadio').value = d.id_estadio;
        });
    });
    </script>
</body>

</html>