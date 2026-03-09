<?php
require_once __DIR__ . '/../../config/database.php';

if (isset($_GET['id'])) {
    $category_id = $_GET['id'];

    try {
        // Check if there are posts in this category
        $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM posts WHERE id_category = ?");
        $stmt_check->execute([$category_id]);
        $post_count = $stmt_check->fetchColumn();

        if ($post_count > 0) {
            header('Location: create.php?error=' . urlencode("No se puede eliminar la categoría porque tiene $post_count publicaciones asociadas. Elimina las publicaciones primero."));
            exit;
        }

        // Delete the category itself
        $stmt_category = $pdo->prepare("DELETE FROM categories WHERE id_category = ?");
        $stmt_category->execute([$category_id]);

        header('Location: create.php?success=' . urlencode('Categoría eliminada exitosamente.'));
        exit;
    } catch (PDOException $e) {
        // Rollback the transaction on error
        $pdo->rollBack();
        header('Location: create.php?error=Database error: ' . urlencode($e->getMessage()));
        exit;
    }
} else {
    header('Location: create.php?error=No category ID provided.');
    exit;
}
?>