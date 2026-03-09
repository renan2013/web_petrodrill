  <?php
require_once __DIR__ . '/../../classbox/config/database.php';

try {
    $stmt = $pdo->query("SELECT title, url FROM menus ORDER BY display_order ASC");
    $menuItems = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Error al cargar el menú del footer: " . $e->getMessage());
    $menuItems = [];
}
?>
  <footer id="footer" class="footer dark-background">

    <div class="container footer-top">
      <div class="row gy-4">
        <div class="col-lg-6 col-md-6 footer-about">
          <a href="/index.php" class="logo d-flex align-items-center">
            <img src="/web_site/assets/img/logo2.png" alt="" class="img-fluid" style="margin-top: 15px;" width="350" height="auto">
          </a>
          <div class="footer-contact pt-3">
            <p>Lima, Perú</p>
            <p class="mt-3"><strong>Celular:</strong> <span>+51 989878609</span></p>
            
          </div>
          <div class="social-links d-flex mt-4">
             <a href="https://wa.me/51989878609" class="whatsapp"><i class="bi bi-whatsapp"></i></a>
            <a href="https://youtube.com/@petrodrillperu?si=jz8SYFev-onIobNC" class="youtube"><i class="bi bi-youtube"></i></a>
            <a href="https://www.tiktok.com/@petrodrillsacperu?_t=ZM-8wFWKezqChy&_r=1" class="tiktok"><i class="bi bi-tiktok"></i></a>
            <a href="https://www.instagram.com/petrodrillsacperu?igsh=ZTh2MjgxaG1qeGtv" class="instagram"><i class="bi bi-instagram"></i></a>
          </div>
        </div>

        


        <div class="col-lg-6 col-md-3 footer-links">
          <h4>Links</h4>
          <ul>
            <?php foreach ($menuItems as $item):
                $linkUrl = htmlspecialchars($item['url']);
                // Adjust 'index.html' to '/index.php' for the new root
                if ($linkUrl === 'index.html' || $linkUrl === 'index.php' || $linkUrl === '/index.php') {
                    $linkUrl = '/index.php';
                } else {
                    // Prepend /web_site/ to other internal links
                    $linkUrl = '/web_site/' . $linkUrl;
                }
                if ($item['title'] == 'CONTACTO') {
                    $linkUrl = 'https://wa.me/51989878609';
                }
                // Determine active class based on the adjusted URL
                $isActive = ($linkUrl === '/index.php') ? '' : '';
            ?>
                <li><a href="<?php echo $linkUrl; ?>" class="<?php echo $isActive; ?>"><?php echo htmlspecialchars($item['title']); ?></a></li>
            <?php endforeach; ?>
          </ul>
        </div>

       

      </div>
    </div>

    <div class="container copyright text-center mt-4">
      <img src="/web_site/assets/img/logo_classbox.svg" alt="" class="img-fluid" style="margin-top: 15px;" width="200" height="auto">
      
      <div class="credits">

        Designed and developed by <a href="https://renangalvan.net/">renangalvan.net - +506 87777849</a><br/>grupodivisoft 2025
      </div>
    </div>

  </footer>

  <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Preloader -->
  <div id="preloader"></div>

  <!-- Vendor JS Files -->
  <script src="/web_site/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="/web_site/assets/vendor/php-email-form/validate.js"></script>
  <script src="/web_site/assets/vendor/aos/aos.js"></script>
  <script src="/web_site/assets/vendor/swiper/swiper-bundle.min.js"></script>
  <script src="/web_site/assets/vendor/purecounter/purecounter_vanilla.js"></script>
  <script src="/web_site/assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="/web_site/assets/js/main.js"></script>

  <!-- Botón Flotante de WhatsApp -->
  <a href="https://wa.me/51989878609" class="whatsapp-flotante" target="_blank" title="Chatea con nosotros">
    <i class="bi bi-whatsapp"></i>
  </a>
  <style>
  .whatsapp-flotante {
    position: fixed;
    width: 60px;
    height: 60px;
    bottom: 40px;
    right: 40px;
    background-color: #25D366;
    color: #FFF;
    border-radius: 50px;
    text-align: center;
    font-size: 30px;
    box-shadow: 2px 2px 6px rgba(0,0,0,0.4);
    z-index: 1000;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background-color 0.3s;
  }

  .whatsapp-flotante:hover {
      background-color: #128C7E;
      color: #FFF;
  }
  </style>
  <!-- Fin Botón Flotante de WhatsApp -->

</body>

</html>