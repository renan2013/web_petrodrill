<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../auth/check_auth.php';

if ($_SESSION['user_role'] !== 'superadmin') {
    die("Acceso denegado. Debes ser superadmin.");
}

try {
    // 1. Definir los módulos a registrar
    $modules = [
        ['galerias', 'Gestor de Galerías', 'Administrar álbumes de fotos y graduaciones.'],
        ['testimonios', 'Gestor de Testimonios', 'Administrar comentarios de estudiantes.']
    ];

    echo "<h1>Activando Módulos...</h1>";

    foreach ($modules as $m) {
        // Insertar módulo
        $stmt = $pdo->prepare("INSERT IGNORE INTO modules (name, display_name, description) VALUES (?, ?, ?)");
        $stmt->execute([$m[0], $m[1], $m[2]]);

        // Obtener ID
        $stmt_id = $pdo->prepare("SELECT id_module FROM modules WHERE name = ?");
        $stmt_id->execute([$m[0]]);
        $id_module = $stmt_id->fetchColumn();

        if ($id_module) {
            // Asignar a superadmin
            $pdo->exec("INSERT IGNORE INTO user_modules (id_user, id_module) SELECT id_user, $id_module FROM users WHERE role = 'superadmin'");
            echo "<p>Módulo <strong>{$m[1]}</strong>: ¡Activado con éxito!</p>";
        }
    }

    echo "<hr><a href='../index.php'>Volver al Dashboard</a>";

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>