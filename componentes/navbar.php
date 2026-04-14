<style>
    /* Estilo para el contenedor del Navbar */
    .frutiger-navbar {
        background: linear-gradient(180deg, 
            rgba(255, 255, 255, 0.3) 0%, 
            rgba(200, 0, 0, 0.8) 15%, 
            rgba(100, 0, 0, 0.9) 85%, 
            rgba(0, 0, 0, 1) 100%) !important;
        backdrop-filter: blur(10px);
        border-bottom: 2px solid rgba(255, 255, 255, 0.4);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.4);
    }

    /* Brillo superior tipo 'burbuja' */
    .frutiger-navbar::before {
        content: "";
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 50%;
        background: linear-gradient(180deg, rgba(255,255,255,0.4) 0%, rgba(255,255,255,0) 100%);
        pointer-events: none;
    }

    /* Botones con efecto glossy */
    .frutiger-navbar .btn-success {
        background: linear-gradient(180deg, #32cd32 0%, #008000 100%) !important;
        border-radius: 50px !important;
        border: 1px solid rgba(255,255,255,0.5) !important;
        box-shadow: 0 2px 4px rgba(0,0,0,0.3), inset 0 2px 2px rgba(255,255,255,0.5) !important;
        font-weight: bold;
    }

    .frutiger-navbar .btn-outline-success {
        border-radius: 50px !important;
        border-color: #32cd32 !important;
        color: #32cd32 !important;
        background: rgba(255,255,255,0.1);
    }

    /* Input redondeado tipo Windows Vista/7 */
    .frutiger-navbar .form-control {
        border-radius: 50px !important;
        background: rgba(255, 255, 255, 0.2) !important;
        border: 1px solid rgba(255, 255, 255, 0.4) !important;
        color: white !important;
    }

    .frutiger-navbar .form-control::placeholder {
        color: rgba(255, 255, 255, 0.6);
    }

    .frutiger-navbar .navbar-brand {
        font-weight: 800;
        text-shadow: 0 0 8px rgba(255, 255, 255, 0.5);
    }
</style>

<nav class="navbar navbar-expand-lg navbar-dark frutiger-navbar">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Jabulani Files</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="index.php">Equipos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="estadios.php">Estadios</a>
                </li>
            </ul>

            <ul class="navbar-nav d-flex flex-row align-items-center mb-2 mb-lg-0">
                <li class="nav-item">
                    <a href="login.html" class="btn btn-success me-2">Login</a>
                </li>
                <li class="nav-item">
                    <form class="d-flex m-0" role="search">
                        <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search" />
                        <button class="btn btn-outline-success" type="submit">Search</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>