<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Jabulani Files</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/style.css">
    <style>
        body {
            background-color: #121212;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-image: url('https://media1.giphy.com/media/v1.Y2lkPTc5MGI3NjExaWZpOXZubGl6Z2h6MmFucXd5eHp4dXo2bms2MmNzZm5pYW11dWlrdyZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/JSgpbvs51IXLy/giphy.gif');
            background-repeat: repeat;
            background-size: 300px;
            background-attachment: fixed;
        }
        .register-card {
            background: #ffffff;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.5);
            padding: 2rem;
            width: 100%;
            max-width: 400px;
        }
        .btn-jabulani {
            background-color: #198754;
            color: white;
            border: none;
        }
        .brand-title {
            color: #dc3545;
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .form-control::placeholder {
            color: #666 !important;
        }
    </style>
</head>
<body>

    <div class="register-card">
        <h2 class="brand-title">Registrarse</h2>
        <p class="text-center text-muted">Crea tu cuenta</p>
        
        <form action="/src/procesar_registro.php" method="POST">
            <div class="mb-3">
                <label for="nombre" class="form-label text-dark">Nombre</label>
                <input type="text" class="form-control" id="nombre" name="nombre" style="color: #000;" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label text-dark">Email</label>
                <input type="email" class="form-control" id="email" name="email" style="color: #000;" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label text-dark">Contraseña</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Mínimo 4 caracteres" style="color: #000;" required>
            </div>

            <div class="mb-4">
                <label for="confirm_password" class="form-label text-dark">Confirmar Contraseña</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" style="color: #000;" required>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-jabulani btn-lg">Registrarse</button>
            </div>
        </form>

        <div class="mt-3 text-center">
            <a href="/login.php" class="text-decoration-none text-muted small">← Ya tengo cuenta</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>