<?php
// Incluir conexión a la base de datos y configuración
require_once __DIR__ . '/../classbox/config/database.php';

// Cargar la estructura del sitio
include 'partials/head_section.php';
include 'partials/header_nav.php'; 
include 'partials/main_start.php';
include 'partials/cuerpo_index.php'; // Aquí se cargan los productos de la BD
include 'partials/main_end.php';
include 'partials/footer.php';
include 'partials/footer_scripts.php';
?>