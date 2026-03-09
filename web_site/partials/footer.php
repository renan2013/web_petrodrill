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
          <a href="/web_petrodrill/index.php" class="logo d-flex align-items-center">
            <img src="/web_site/assets/img/logo2.png" alt="Petrodrill" class="img-fluid" style="margin-top: 15px;" width="350" height="auto">
          </a>
          <div class="footer-contact pt-3">
            <p>Especialistas en fabricación de repuestos para la perforación.</p>
            <p>Lima, Perú</p>
            <p class="mt-3"><strong>WhatsApp:</strong> <span>(+51) 989878609</span></p>
            <p><strong>Email:</strong> <span>info@petrodrillperu.com</span></p>
          </div>
          <div class="social-links d-flex mt-4">
            <a href="https://wa.me/51989878609" class="whatsapp" title="WhatsApp"><i class="bi bi-whatsapp"></i></a>
            <a href="https://youtube.com/@petrodrillperu?si=jz8SYFev-onIobNC" class="youtube" title="YouTube"><i class="bi bi-youtube"></i></a>
            <a href="https://www.tiktok.com/@petrodrillsacperu?_t=ZM-8wFWKezqChy&_r=1" class="tiktok" title="TikTok"><i class="bi bi-tiktok"></i></a>
            <a href="https://www.instagram.com/petrodrillsacperu?igsh=ZTh2MjgxaG1qeGtv" class="instagram" title="Instagram"><i class="bi bi-instagram"></i></a>
          </div>
        </div>

        <div class="col-lg-6 col-md-3 footer-links">
          <h4>Enlaces Útiles</h4>
          <ul>
            <?php foreach ($menuItems as $item):
                $linkUrl = $item['url'];
                if (strpos($linkUrl, 'http') === 0) {
                    $linkUrl = htmlspecialchars($linkUrl);
                } elseif ($linkUrl === 'index.html' || $linkUrl === 'index.php' || $linkUrl === '/index.php') {
                    $linkUrl = '/web_petrodrill/index.php';
                } else {
                    $linkUrl = '/web_petrodrill/web_site/' . htmlspecialchars($linkUrl);
                }
                if ($item['title'] == 'CONTACTO' && strpos($linkUrl, 'http') !== 0) {
                    $linkUrl = 'https://wa.me/51989878609';
                }
            ?>
                <li><a href="<?php echo $linkUrl; ?>"><?php echo htmlspecialchars($item['title']); ?></a></li>
            <?php endforeach; ?>
          </ul>
        </div>

      </div>
    </div>

    <div class="container copyright text-center mt-4">
      <div class="credits">
        &copy; <?php echo date('Y'); ?> <strong>Petrodrill SAC</strong>. Todos los derechos reservados.<br>
        Designed and developed by <a href="https://renangalvan.net/">renangalvan.net</a>
      </div>
    </div>

</footer>