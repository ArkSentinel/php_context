<?php
require_once '../src/Database.php';
require_once '../src/functions.php';

$pdo = Database::getInstance()->getConnection();

$query = $pdo->query('SELECT id_estadio, nombre, ciudad, capacidad, url_imagen FROM estadios');
$estadios_raw = $query->fetchAll(PDO::FETCH_ASSOC);
$estadios_json = json_encode($estadios_raw);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Estadios | Jabulani Files</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/css/style.css">

    <style>
        body {
            background: linear-gradient(rgba(180, 0, 0, 0.85), rgba(20, 0, 0, 0.95)),
                url('https://media1.giphy.com/media/v1.Y2lkPTc5MGI3NjExaHAwNG9uaG5sdmhvMHY2dmFxajd5eXE2OGJjNXFoZHMwY3d1cndjMiZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/6uqz4G16YT8u80b7aX/giphy.gif');
            background-size: 400px;
            background-attachment: fixed;
            color: #ffffff;
            font-family: 'Segoe UI', sans-serif;
            min-height: 100vh;
        }

        h2, h3, h5, p, span, a, i { color: #ffffff !important; }

        .card-aero {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            overflow: hidden;
            backdrop-filter: blur(10px);
        }

        .card-header-aero {
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.2) 0%, rgba(0, 0, 0, 0.5) 100%);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            font-weight: bold;
        }

        .data-box {
            background: rgba(0, 0, 0, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 15px;
            padding: 15px;
            height: 100%;
        }

        .label-aero {
            color: #00f2ff !important;
            font-size: 0.75rem;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
            display: block;
        }

        .stadium-image-container {
            position: relative;
            padding: 10px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 18px;
            border: 1px dashed rgba(255, 255, 255, 0.2);
        }

        .img-stadium-aero {
            width: 100%;
            max-height: 400px;
            object-fit: cover;
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.4);
            box-shadow: 0 0 20px rgba(0, 242, 255, 0.3);
        }
    </style>
</head>
<body>
    <?php include '../views/layouts/navbar.php'; ?>

    <div class="container mt-4">
        <h2 class="mb-4 fw-bold">
            <i class="bi bi-geo-alt-fill"></i> ESTADIOS
        </h2>

        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="glass p-3">
                    <h5 class="mb-3 border-bottom pb-2">RECINTOS</h5>
                    <div class="list-group" id="lista-estadios">
                        <?php foreach ($estadios_raw as $es): ?>
                        <a class="list-group-item list-group-item-action estadio-item"
                            data-id="<?= $es['id_estadio'] ?>">
                            <i class="bi bi-building-fill me-2"></i> <?= h($es['nombre']) ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card card-aero shadow-lg" id="card-detalle" style="display: none;">
                    <div class="card-header card-header-aero p-3">
                        <i class="bi bi-cpu me-2"></i> DETALLES DEL RECINTO
                    </div>
                    <div class="card-body p-4">
                        <h2 id="det-nombre" class="fw-bold mb-4" style="text-shadow: 2px 2px 5px rgba(0,0,0,0.8);"></h2>

                        <div class="row g-3 mb-4">
                            <div class="col-sm-6">
                                <div class="data-box">
                                    <span class="label-aero">Ubicación Geográfica</span>
                                    <h5 class="m-0"><i class="bi bi-map me-2"></i> <span id="det-ciudad"></span></h5>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="data-box">
                                    <span class="label-aero">Capacidad de Aforo</span>
                                    <h5 class="m-0"><i class="bi bi-people-fill me-2"></i> <span id="det-capacidad"></span></h5>
                                </div>
                            </div>
                        </div>

                        <div class="stadium-image-container">
                            <span class="label-aero mb-2">Vista Satelital / Recinto</span>
                            <img id="det-foto" src="" class="img-stadium-aero" alt="Vista del estadio">
                        </div>
                    </div>
                </div>

                <div id="mensaje-ayuda" class="glass text-center py-5">
                    <i class="bi bi-search d-block mb-3 display-4"></i>
                    <h5>SELECCIONE UN ESTADIO PARA VER INFORMACIÓN</h5>
                </div>
            </div>
        </div>
    </div>

    <?php include '../views/layouts/footer.php'; ?>

    <script>
    const estadiosData = <?php echo $estadios_json; ?>;

    document.addEventListener('DOMContentLoaded', function() {
        const items = document.querySelectorAll('.estadio-item');
        const card = document.getElementById('card-detalle');
        const mensaje = document.getElementById('mensaje-ayuda');
        const fotoEstadio = document.getElementById('det-foto');

        items.forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();

                items.forEach(i => i.classList.remove('active'));
                this.classList.add('active');

                const estadioId = this.getAttribute('data-id');
                const estadio = estadiosData.find(est => est.id_estadio == estadioId);

                if (estadio) {
                    document.getElementById('det-nombre').textContent = estadio.nombre;
                    document.getElementById('det-ciudad').textContent = estadio.ciudad;
                    document.getElementById('det-capacidad').textContent =
                        new Intl.NumberFormat().format(estadio.capacidad) + " Espectadores";

                    if (estadio.url_imagen && estadio.url_imagen.trim() !== "") {
                        fotoEstadio.src = estadio.url_imagen;
                    } else {
                        fotoEstadio.src = 'https://via.placeholder.com/800x400/300000/FFFFFF?text=Imagen+No+Disponible';
                    }

                    card.style.display = 'block';
                    mensaje.style.display = 'none';
                }
            });
        });
    });
    </script>
</body>
</html>