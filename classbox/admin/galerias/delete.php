<?php
session_start();
require_once __DIR__ . '/../../config/database.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    try {
        // Al tener ON DELETE CASCADE, esto borrará también las fotos asociadas en 'graduaciones_fotos'
        $stmt = $pdo->prepare("DELETE FROM graduaciones WHERE id_graduacion = ?");
        $stmt->execute([$id]);
        
        header('Location: index.php?success=' . urlencode('Graduación eliminada con éxito.'));
        exit;
    } catch (PDOException $e) {
        header('Location: index.php?error=' . urlencode('Error al eliminar: ' . $e->getMessage()));
        exit;
    }
} else {
    header('Location: index.php');
    exit;
}
?>