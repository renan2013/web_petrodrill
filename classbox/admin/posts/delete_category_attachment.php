<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../auth/check_auth.php';

$attachment_id = $_GET['id'] ?? null;
$category_id = $_GET['category_id'] ?? null;

if (!$attachment_id || !$category_id) {
    header('Location: create.php?error=missing_parameters');
    exit;
}

try {
    // 1. Obtener detalles del adjunto para borrar el archivo físico si es necesario
    $stmt = $pdo->prepare("SELECT type, value FROM attachments WHERE id_attachment = ?");
    $stmt->execute([$attachment_id]);
    $attachment = $stmt->fetch();

    if ($attachment) {
        // Si no es un video de YouTube, borramos el archivo del servidor
        if ($attachment['type'] !== 'youtube') {
            $file_path = __DIR__ . '/../../public/uploads/attachments/' . $attachment['value'];
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }

        // 2. Eliminar el registro de la base de datos
        $del_stmt = $pdo->prepare("DELETE FROM attachments WHERE id_attachment = ?");
        $del_stmt->execute([$attachment_id]);

        header('Location: category_attachments.php?id_category=' . $category_id . '&success=' . urlencode('Adjunto eliminado correctamente.'));
        exit;
    } else {
        header('Location: category_attachments.php?id_category=' . $category_id . '&error=' . urlencode('Adjunto no encontrado.'));
        exit;
    }

} catch (PDOException $e) {
    header('Location: category_attachments.php?id_category=' . $category_id . '&error=' . urlencode('Error al eliminar: ' . $e->getMessage()));
    exit;
}
?>
