<?php
require_once(dirname(__FILE__) . '/../../config/database.php');

$user_id = $_GET['id'] ?? null;

if (!$user_id) {
    header('Location: index.php?error=' . urlencode('ID de usuario no proporcionado.'));
    exit;
}

try {
    $stmt = $pdo->prepare("DELETE FROM users WHERE id_user = ?");
    $stmt->execute([$user_id]);

    header('Location: index.php?success=' . urlencode('Usuario eliminado exitosamente.'));
    exit;
} catch (PDOException $e) {
    header('Location: index.php?error=' . urlencode('Error al eliminar el usuario: ' . $e->getMessage()));
    exit;
}
?>