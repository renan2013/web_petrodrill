  <?php
require_once __DIR__ . '/../../classbox/config/database.php';

try {
    $stmt = $pdo->query("SELECT title, url FROM menus ORDER BY display_order ASC");
    $menuItems = $stmt->fetchAll();
    // DEBUG: Check if menuItems is empty
    if (empty($menuItems)) {
        echo "<!-- DEBUG: menuItems is empty. Check 'menus' table in database. -->";
    }
} catch (PDOException $e) {
    // En un entorno de producción, esto debería ser un registro de errores, no una salida pública.
    error_log("Error al cargar el menú: " . $e->getMessage());
    echo "<!-- DEBUG: Database Error loading menu: " . $e->getMessage() . " -->"; // DEBUG: Output database error
    $menuItems = []; // Asegura que $menuItems sea un array vacío en caso de error
}
?>
  <header id="header" class="header position-relative" >
    <div class="container-fluid container-xl position-relative" style="margin-top:8px;">

      <!-- Desktop Header -->
      <div class="d-none d-lg-flex align-items-center justify-content-between">
        <a href="/index.php" class="logo d-flex align-items-center">
          <img src="/web_site/assets/img/logo2.png" alt="" class="img-fluid" width="350" height="auto">
          <span style="color:yellow; margin-left: 15px; font-size: 1rem; margin-top: 5px;">Lun-Vie 8-5 pm - Sábado 8-1 pm (+51) 989878609</span>
        </a>
        
        <div class="d-flex align-items-center">
          <div class="social-links">
            <a href="https://wa.me/51989878609" class="whatsapp" title="Chatea con nosotros" style="font-size: 1.9rem; margin: 0 5px; color: #25D366;"><i class="bi bi-whatsapp"></i></a>
            <a href="https://youtube.com/@petrodrillperu?si=jz8SYFev-onIobNC" class="youtube" style="font-size: 1.7rem; margin: 0 5px;"><i class="bi bi-youtube"></i></a>
            <a href="https://www.tiktok.com/@petrodrillsacperu?_t=ZM-8wFWKezqChy&_r=1" class="tiktok" style="font-size: 1.7rem; margin: 0 5px;"><i class="bi bi-tiktok"></i></a>
            <a href="https://www.instagram.com/petrodrillsacperu?igsh=ZTh2MjgxaG1qeGtv" class="instagram" style="font-size: 1.7rem; margin: 0 5px;"><i class="bi bi-instagram"></i></a>
          </div>
        </div>
      </div>

      <!-- Mobile Header -->
      <div class="d-lg-none">
        <div class="row align-items-center">
          <div class="col-10">
            <a href="/index.php" class="logo" style="margin-top: 10px; display: inline-block;">
              <img src="/web_site/assets/img/logo2.png" alt="" class="img-fluid">
            </a>
          </div>
          <div class="col-2 text-end">
            <i class="mobile-nav-toggle bi bi-list"></i>
          </div>
        </div>
        <div class="text-center mt-3">
          <span style="color:yellow; font-size: 1rem;">Lun-Vie 8-5 pm - Sáb 8-1 pm (+51) 989878609</span>
        </div>
        <div class="social-links d-flex justify-content-center mt-3" style="margin-left: 30px;">
          <a href="https://wa.me/51989878609" class="whatsapp" title="Chatea con nosotros" style="font-size: 1.6rem; margin: 0 5px; color: #25D366;"><i class="bi bi-whatsapp"></i></a>
          <a href="https://youtube.com/@petrodrillperu?si=jz8SYFev-onIobNC" class="youtube" style="font-size: 1.6rem; margin: 0 5px;"><i class="bi bi-youtube"></i></a>
          <a href="https://www.tiktok.com/@petrodrillsacperu?_t=ZM-8wFWKezqChy&_r=1" class="tiktok" style="font-size: 1.6rem; margin: 0 5px;"><i class="bi bi-tiktok"></i></a>
          <a href="https://www.instagram.com/petrodrillsacperu?igsh=ZTh2MjgxaG1qeGtv" class="instagram" style="font-size: 1.6rem; margin: 0 5px;"><i class="bi bi-instagram"></i></a>
        </div>
      </div>

    </div>

    <div class="nav-wrap" >
      <div class="container d-flex justify-content-center position-relative">
        <nav id="navmenu" class="navmenu">
          <ul>
            <?php foreach ($menuItems as $item):
                $linkUrl = htmlspecialchars($item['url']);
                // Adjust links for the new project structure
                if ($linkUrl === 'index.html' || $linkUrl === 'index.php' || $linkUrl === '/index.php') {
                    $linkUrl = '/web_petrodrill/index.php';
                } else {
                    // Prepend the project path to other internal links
                    $linkUrl = '/web_petrodrill/web_site/' . $linkUrl;
                }
                if ($item['title'] == 'CONTACTO') {
                    $linkUrl = 'https://wa.me/51989878609';
                }
            ?>
                <li><a href="<?php echo $linkUrl; ?>"><?php echo htmlspecialchars($item['title']); ?></a></li>
            <?php endforeach; ?>
          </ul>
        </nav>
      </div>
    </div>

  </header>