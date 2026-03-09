<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../auth/check_auth.php';

if ($_SESSION['user_role'] !== 'superadmin') {
    die("Acceso denegado.");
}

try {
    // 1. Insertar el módulo
    $stmt = $pdo->prepare("INSERT IGNORE INTO modules (name, display_name, description) VALUES ('testimonios', 'Gestor de Testimonios', 'Administrar comentarios y testimonios de estudiantes.')");
    $stmt->execute();

    // 2. Obtener el ID
    $stmt = $pdo->query("SELECT id_module FROM modules WHERE name = 'testimonios'");
    $id_module = $stmt->fetchColumn();

    if ($id_module) {
        // 3. Asignar permisos
        $pdo->exec("INSERT IGNORE INTO user_modules (id_user, id_module) SELECT id_user, $id_module FROM users WHERE role = 'superadmin'");
        
        echo "<h1>¡Módulo de Testimonios instalado!</h1>";
        echo "<p>Ya puedes gestionarlos desde el menú lateral.</p>";
        echo "<a href='index.php'>Ir al Gestor de Testimonios</a>";
    }
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>