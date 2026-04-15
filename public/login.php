<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Jabulani Files</title>
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
        .login-card {
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
        .btn-jabulani:hover {
            background-color: #146c43;
            color: white;
        }
        .brand-title {
            color: #dc3545;
            text-align: center;
            margin-bottom: 1.5rem;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <h2 class="brand-title">Jabulani Files</h2>
        <p class="text-center text-muted">Ingresa a la base de datos</p>

        <?php if (isset($_GET['registered'])): ?>
            <div class="alert alert-success py-2 mb-3 text-center">
                <i class="bi bi-check-circle"></i> Cuenta creada exitosamente. ¡Inicia sesión!
            </div>
        <?php endif; ?>
        
        <form action="/src/procesar_login.php" method="POST">
            <div class="mb-3">
                <label for="email" class="form-label text-dark">Email</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="nombre@ejemplo.com" required>
            </div>

            <div class="mb-4">
                <label for="password" class="form-label text-dark">Contraseña</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="••••••••" required>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-jabulani btn-lg">Entrar</button>
            </div>

            <div class="d-grid mt-2">
                <a href="/register.php" class="btn btn-outline-primary btn-lg">Crear Cuenta</a>
            </div>
        </form>

        <div class="mt-3 text-center">
            <a href="/index.php" class="text-decoration-none text-muted small">← Volver al inicio</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>