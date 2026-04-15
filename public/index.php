<?php 
require_once '../src/Database.php';
require_once '../src/functions.php';

$pdo = Database::getInstance()->getConnection();

$sql = "SELECT e.*, ent.nombre AS dt_nom, ent.apellido AS dt_ape, est.nombre AS est_nom, 
               uni.url_imagen AS uni_url, log.url_logo AS log_url 
        FROM equipos e
        LEFT JOIN entrenador ent ON e.id_entrenador = ent.id_entrenador
        LEFT JOIN estadios est ON e.id_estadio = est.id_estadio
        LEFT JOIN uniformes uni ON e.id_uniforme = uni.id_uniforme
        LEFT JOIN logos log ON e.id_logo = log.id_logo";

try {
    $equipos = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error en la base de datos: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Equipos | Jabulani Files</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/css/style.css">

    <style>
        body {
            background: linear-gradient(rgba(180, 0, 0, 0.8), rgba(20, 0, 0, 0.9)),
                url('https://media1.giphy.com/media/v1.Y2lkPTc5MGI3NjExaHAwNG9uaG5sdmhvMHY2dmFxajd5eXE2OGJjNXFoZHMwY3d1cndjMiZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/6uqz4G16YT8u80b7aX/giphy.gif');
            background-size: 400px;
            background-attachment: fixed;
            color: #ffffff;
            font-family: 'Segoe UI', sans-serif;
            min-height: 100vh;
        }
        .form-control, .form-select {
            background: rgba(255, 255, 255, 0.15) !important;
            color: #fff !important;
        }
        .form-control::placeholder { color: rgba(255,255,255,0.6); }

        .carousel-container { padding: 40px 0; }

        .img-carousel {
            height: 180px;
            margin: 0 15px;
            filter: drop-shadow(0 0 15px rgba(255, 255, 255, 0.4));
            object-fit: contain;
            transition: transform 0.3s ease;
        }

        .img-carousel:hover { transform: scale(1.1); }

        .carousel-control-prev, .carousel-control-next {
            width: 5%;
            filter: invert(1) grayscale(100) brightness(2);
        }

        .carousel-indicators [data-bs-target] {
            background-color: #ff0000;
            height: 5px;
            border-radius: 2px;
        }

        #panel-detalle { display: none; }

        .img-det {
            border: 3px solid white;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(255, 0, 0, 0.6);
            max-height: 350px;
            object-fit: cover;
        }

        .header-title {
            text-shadow: 0 0 10px rgba(255, 255, 255, 0.5);
            font-weight: 800;
            letter-spacing: 2px;
        }
    </style>
</head>
<body>

    <?php include '../views/layouts/navbar.php'; ?>

    <div class="container py-4">
        <div class="glass mb-4 carousel-container">
            <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-indicators">
                    <?php 
                    $grupos = array_chunk($equipos, 4);
                    foreach ($grupos as $i => $grupo): 
                    ?>
                    <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="<?= $i ?>"
                        class="<?= $i == 0 ? 'active' : '' ?>" aria-current="<?= $i == 0 ? 'true' : 'false' ?>"
                        aria-label="Slide <?= $i + 1 ?>"></button>
                    <?php endforeach; ?>
                </div>

                <div class="carousel-inner">
                    <?php foreach ($grupos as $i => $grupo): ?>
                    <div class="carousel-item <?= $i == 0 ? 'active' : '' ?>">
                        <div class="d-flex justify-content-center align-items-center mb-5">
                            <?php foreach ($grupo as $eq): ?>
                            <img src="<?= !empty($eq['log_url']) ? 'data:image/jpeg;base64,' . base64_encode($eq['log_url']) : '' ?>" class="img-carousel" alt="Logo de <?= h($eq['nombre']) ?>">
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Anterior</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Siguiente</span>
                </button>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="glass">
                    <h5 class="mb-3 border-bottom pb-2"><i class="bi bi-list-ul"></i> EQUIPOS</h5>
                    <div class="list-group">
                        <?php foreach ($equipos as $eq): ?>
                        <button class="list-group-item list-group-item-action btn-equipo"
                            data-nombre="<?= h($eq['nombre']) ?>"
                            data-apodo="<?= h($eq['apodo']) ?>"
                            data-dt="<?= h($eq['dt_nom'].' '.$eq['dt_ape']) ?>"
                            data-est="<?= h($eq['est_nom']) ?>" 
                            data-img="<?= !empty($eq['uni_url']) ? htmlspecialchars(base64_encode($eq['uni_url'])) : '' ?>"
                            data-logo="<?= !empty($eq['log_url']) ? htmlspecialchars(base64_encode($eq['log_url'])) : '' ?>">
                            <i class="bi bi-shield-shaded me-2"></i> <?= h($eq['nombre']) ?>
                        </button>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div id="msg-espera" class="glass p-5 text-center">
                    <img src="https://web.archive.org/web/20091024032410/http://geocities.com/SoHo/Museum/2034/under_construction.gif" class="mb-3" alt="Cargando">
                    <h5><i class="bi bi-info-circle"></i> SELECCIONE UN REGISTRO</h5>
                </div>

                <div id="panel-detalle" class="glass">
                    <div class="row">
                        <div class="col-md-5 text-center">
                            <img id="view-img" src="" class="img-fluid img-det mb-3" alt="Uniforme">
                        </div>
                        <div class="col-md-7">
                            <div class="d-flex align-items-center mb-3">
                                <img id="view-logo" src="" width="60" class="me-3 bg-white rounded-circle p-1" alt="Logo">
                                <h2 id="view-nombre" class="m-0 fw-bold"></h2>
                            </div>
                            <hr>
                            <p><i class="bi bi-chat-quote-fill text-danger"></i> <strong>APODO:</strong> <span id="view-apodo"></span></p>
                            <p><i class="bi bi-person-fill text-danger"></i> <strong>D.T.:</strong> <span id="view-dt"></span></p>
                            <p><i class="bi bi-geo-alt-fill text-danger"></i> <strong>ESTADIO:</strong> <span id="view-est"></span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../views/layouts/footer.php'; ?>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const botones = document.querySelectorAll('.btn-equipo');
        const msgEspera = document.getElementById('msg-espera');
        const panelDetalle = document.getElementById('panel-detalle');

        botones.forEach(btn => {
            btn.addEventListener('click', function() {
                botones.forEach(b => b.classList.remove('active'));
                this.classList.add('active');

                document.getElementById('view-nombre').innerText = this.dataset.nombre;
                document.getElementById('view-apodo').innerText = this.dataset.apodo || 'Sin datos';
                document.getElementById('view-dt').innerText = this.dataset.dt;
                document.getElementById('view-est').innerText = this.dataset.est;
                document.getElementById('view-img').src = this.dataset.img ? 'data:image/jpeg;base64,' + this.dataset.img : '';
                document.getElementById('view-logo').src = this.dataset.logo ? 'data:image/jpeg;base64,' + this.dataset.logo : '';

                msgEspera.style.display = 'none';
                panelDetalle.style.display = 'block';
            });
        });
    });
    </script>
</body>
</html>