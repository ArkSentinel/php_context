<style>
    /* Contenedor principal del Footer */
    .frutiger-footer {
        background: linear-gradient(0deg, 
            rgba(255, 0, 0, 0.9) 0%, 
            rgba(100, 0, 0, 0.95) 50%, 
            rgba(0, 0, 0, 1) 100%) !important;
        backdrop-filter: blur(15px);
        border-top: 3px solid rgba(255, 255, 255, 0.3);
        box-shadow: 0 -5px 20px rgba(0, 0, 0, 0.6);
        position: relative;
        overflow: hidden;
    }

    /* Reflejo de brillo superior (efecto burbuja) */
    .frutiger-footer::after {
        content: "";
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 40%;
        background: linear-gradient(180deg, rgba(255,255,255,0.15) 0%, rgba(255,255,255,0) 100%);
        pointer-events: none;
    }

    /* Títulos con brillo */
    .frutiger-footer h5 {
        color: #00d9ff !important; /* Celeste brillante característico */
        text-shadow: 0 0 10px rgba(0, 217, 255, 0.5);
        font-weight: 800;
        letter-spacing: 1px;
    }

    /* Enlaces y texto */
    .frutiger-footer a {
        transition: all 0.3s ease;
    }

    .frutiger-footer a:hover {
        color: #00d9ff !important;
        text-shadow: 0 0 8px white;
        transform: translateY(-2px);
        display: inline-block;
    }

    /* Iconos sociales con burbuja */
    .frutiger-footer .bi {
        font-size: 1.2rem;
        transition: 0.3s;
    }

    .frutiger-footer .bi:hover {
        filter: drop-shadow(0 0 5px #00d9ff);
    }

    /* Línea divisoria glossy */
    .frutiger-footer hr {
        border-top: 1px solid rgba(255, 255, 255, 0.2);
        box-shadow: 0 1px 2px rgba(0,0,0,0.5);
    }

    .text-primary-aero {
        color: #00d9ff !important;
    }
</style>

<footer class="frutiger-footer text-white pt-5 pb-4">
  <div class="container text-center text-md-start">
    <div class="row">
      
      <div class="col-md-3 col-lg-3 col-xl-3 mx-auto mt-3">
        <h5 class="text-uppercase mb-4 font-weight-bold">Nombre Empresa</h5>
        <p>Proporcionamos soluciones creativas para tus proyectos digitales. Enfocados en la innovación y el diseño moderno.</p>
      </div>

      <div class="col-md-2 col-lg-2 col-xl-2 mx-auto mt-3">
        <h5 class="text-uppercase mb-4 font-weight-bold">Servicios</h5>
        <p><a href="#" class="text-white text-decoration-none">Desarrollo Web</a></p>
        <p><a href="#" class="text-white text-decoration-none">Diseño UI/UX</a></p>
        <p><a href="#" class="text-white text-decoration-none">Marketing</a></p>
      </div>

      <div class="col-md-4 col-lg-3 col-xl-3 mx-auto mt-3">
        <h5 class="text-uppercase mb-4 font-weight-bold">Contacto</h5>
        <p><i class="bi bi-house-door-fill me-2 text-primary-aero"></i> Calle Falsa 123, Ciudad</p>
        <p><i class="bi bi-envelope-fill me-2 text-primary-aero"></i> info@ejemplo.com</p>
        <p><i class="bi bi-telephone-fill me-2 text-primary-aero"></i> +56 9 1234 5678</p>
      </div>

    </div>

    <hr class="mb-4">

    <div class="row align-items-center">
      <div class="col-md-7 col-lg-8">
        <p>© 2026 Todos los derechos reservados por:
          <a href="#" class="text-decoration-none">
            <strong class="text-primary-aero">Jabulani</strong>
          </a>
        </p>
      </div>

      <div class="col-md-5 col-lg-4">
        <div class="text-center text-md-end">
          <a href="#" class="text-white me-3"><i class="bi bi-facebook"></i></a>
          <a href="#" class="text-white me-3"><i class="bi bi-twitter-x"></i></a>
          <a href="#" class="text-white me-3"><i class="bi bi-instagram"></i></a>
        </div>
      </div>
    </div>
  </div>
</footer>