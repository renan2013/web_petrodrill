<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../auth/check_auth.php';

// Solo superadmin puede instalar módulos
if ($_SESSION['user_role'] !== 'superadmin') {
    die("Acceso denegado.");
}

try {
    // 1. Insertar el módulo si no existe
    $stmt = $pdo->prepare("INSERT IGNORE INTO modules (name, display_name, description) VALUES ('galerias', 'Gestor de Galerías', 'Administrar álbumes de fotos y graduaciones independientes.')");
    $stmt->execute();

    // 2. Obtener el ID del módulo
    $stmt = $pdo->query("SELECT id_module FROM modules WHERE name = 'galerias'");
    $id_module = $stmt->fetchColumn();

    if ($id_module) {
        // 3. Asignar el módulo a todos los superadmin
        $pdo->exec("INSERT IGNORE INTO user_modules (id_user, id_module) SELECT id_user, $id_module FROM users WHERE role = 'superadmin'");
        
        echo "<h1>¡Módulo de Galerías instalado con éxito!</h1>";
        echo "<p>Ya deberías ver el acceso en el menú lateral.</p>";
        echo "<a href='index.php'>Ir al Gestor de Galerías</a>";
    } else {
        echo "Error al registrar el módulo.";
    }

} catch (PDOException $e) {
    die("Error de base de datos: " . $e->getMessage());
}
?>