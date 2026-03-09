<?php
/**
 * Script de limpieza de base de datos para Petrodrill
 * Ejecuta este archivo en el navegador para vaciar las tablas antiguas.
 * Ejemplo: petrodrillperu.com/limpiar_bd.php
 */

require_once __DIR__ . '/classbox/config/database.php';

$tables = [
    'attachments',
    'posts',
    'categories',
    'menus',
    'testimonios'
];

echo "<h2>Iniciando limpieza de base de datos...</h2>";

try {
    // Desactivar revisión de llaves foráneas para poder vaciar tablas relacionadas
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");

    foreach ($tables as $table) {
        $pdo->exec("TRUNCATE TABLE `$table` ");
        echo "Vaciada tabla: <strong>$table</strong><br>";
    }

    // Reactivar revisión de llaves foráneas
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

    echo "<h3 style='color: green;'>¡Limpieza completada con éxito!</h3>";
    echo "<p>Ya puedes empezar a cargar los contenidos de Petrodrill desde el panel de administración.</p>";

} catch (PDOException $e) {
    echo "<h3 style='color: red;'>Error durante la limpieza:</h3>";
    echo $e->getMessage();
}
?>