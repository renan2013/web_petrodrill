<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../auth/check_auth.php';

$post_id = $_GET['id'] ?? null;

if (!$post_id) {
    header('Location: index.php');
    exit;
}

try {
    // First, get the path of the main image to delete the file
    $stmt_select = $pdo->prepare("SELECT main_image FROM posts WHERE id_post = ?");
    $stmt_select->execute([$post_id]);
    $post = $stmt_select->fetch();

    if ($post && !empty($post['main_image'])) {
        $image_path = __DIR__ . '/../../' . $post['main_image'];
        if (file_exists($image_path)) {
            unlink($image_path);
        }
    }

    // Also, get all attachment files to delete them
    $stmt_files = $pdo->prepare("SELECT value FROM attachments WHERE id_post = ? AND type IN ('pdf', 'slider_image', 'gallery_image')");
    $stmt_files->execute([$post_id]);
    $files_to_delete = $stmt_files->fetchAll(PDO::FETCH_COLUMN);

    foreach ($files_to_delete as $file) {
        $file_path = __DIR__ . '/../../' . $file;
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }

    // Now, delete the post from the database.
    // Associated attachments will be deleted automatically by the database (ON DELETE CASCADE).
    $stmt_delete = $pdo->prepare("DELETE FROM posts WHERE id_post = ?");
    $stmt_delete->execute([$post_id]);

    header('Location: index.php');
    exit;
} catch (PDOException $e) {
    header('Location: index.php?error=' . urlencode('Error al eliminar la publicación: ' . $e->getMessage()));
    exit;
}
?>