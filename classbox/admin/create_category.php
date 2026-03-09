<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../auth/check_auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_name = $_POST['category_name'] ?? '';

    if (!empty($category_name)) {
        try {
            // Check if category already exists
            $stmt_check = $pdo->prepare("SELECT id_category FROM categories WHERE name = ?");
            $stmt_check->execute([$category_name]);
            
            if ($stmt_check->fetch()) {
                // Optional: handle error for existing category
            } else {
                $stmt_insert = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
                $stmt_insert->execute([$category_name]);
            }
        } catch (PDOException $e) {
            // Optional: handle database error
            // For now, we just ignore and redirect
        }
    }
}

// Redirect back to the post creation page
header('Location: create.php');
exit;
?>