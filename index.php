<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Conexión a la base de datos
require_once __DIR__ . '/classbox/config/database.php';

include 'web_site/partials/head_section.php';
include 'web_site/partials/header_nav.php';
include 'web_site/partials/slider.php';
include 'web_site/partials/main_start.php';

echo '<!-- DEBUG: Antes de cuerpo_index.php -->';
include 'web_site/partials/cuerpo_index.php';
echo '<!-- DEBUG: Después de cuerpo_index.php -->';

include 'web_site/partials/main_end.php';
echo '<!-- DEBUG: Antes de footer_scripts.php -->';
include 'web_site/partials/footer_scripts.php';
echo '<!-- DEBUG: Después de footer_scripts.php -->';
?>