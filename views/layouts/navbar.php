<?php
session_start();
$isLoggedIn = isset($_SESSION['user_id']);
?>
<nav class="navbar navbar-expand-lg navbar-dark shadow-sm" style="background: linear-gradient(180deg, rgba(255,255,255,0.1) 0%, rgba(0,0,0,0.8) 100%); backdrop-filter: blur(10px); border-bottom: 1px solid rgba(0, 251, 255, 0.3);">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="/equipos.php" style="text-shadow: 0 0 10px #00fbff;">
            <i class="bi bi-cpu-fill me-2"></i>JABULANI FILES
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <?php if ($isLoggedIn): ?>
                    <li class="nav-item">
                        <a class="nav-link text-uppercase small fw-bold" href="/admin/equipos.php">Equipos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-uppercase small fw-bold" href="/admin/estadios.php">Estadios</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-uppercase small fw-bold" href="/admin/uniformes.php">Uniformes</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link text-uppercase small fw-bold" href="/equipos.php">Equipos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-uppercase small fw-bold" href="/estadios.php">Estadios</a>
                    </li>
                <?php endif; ?>
            </ul>

            <ul class="navbar-nav d-flex flex-row align-items-center">
                <?php if ($isLoggedIn): ?>
                    <li class="nav-item">
                        <span class="navbar-text me-3" style="color: #00fbff;">
                            <i class="bi bi-person-circle"></i> <?= htmlspecialchars($_SESSION['user_email']) ?>
                        </span>
                    </li>
                    <li class="nav-item">
                        <a href="/logout.php" class="btn btn-sm px-3" 
                           style="background: linear-gradient(180deg, #ff4b2b 0%, #ff416c 100%); 
                                  border: 1px solid rgba(255,255,255,0.4); 
                                  color: white; 
                                  border-radius: 50px; 
                                  font-weight: bold;
                                  box-shadow: 0 4px 15px rgba(255, 65, 108, 0.4);">
                            <i class="bi bi-power me-1"></i> LOGOUT
                        </a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a href="/login.php" class="btn btn-sm px-3" 
                           style="background: linear-gradient(180deg, #11998e 0%, #38ef7d 100%); 
                                  border: 1px solid rgba(255,255,255,0.4); 
                                  color: white; 
                                  border-radius: 50px; 
                                  font-weight: bold;
                                  box-shadow: 0 4px 15px rgba(56, 239, 125, 0.4);">
                            <i class="bi bi-box-arrow-in-right me-1"></i> LOGIN
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>