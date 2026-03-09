<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../auth/check_auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_name = $_POST['category_name'] ?? '';
    $category_image = '';

    if (!empty($category_name)) {
        try {
            // Handle image upload
            if (isset($_FILES['category_image']) && $_FILES['category_image']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = __DIR__ . '/../../public/uploads/images/';
                if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
                $file_name = uniqid('cat_', true) . '-' . basename($_FILES['category_image']['name']);
                if (move_uploaded_file($_FILES['category_image']['tmp_name'], $upload_dir . $file_name)) {
                    $category_image = $file_name;
                }
            }

            // Check if category already exists
            $stmt_check = $pdo->prepare("SELECT id_category FROM categories WHERE name = ?");
            $stmt_check->execute([$category_name]);
            
            if ($stmt_check->fetch()) {
                header('Location: create.php?error=' . urlencode('La categoría ya existe.'));
                exit;
            } else {
                $stmt_insert = $pdo->prepare("INSERT INTO categories (name, image) VALUES (?, ?)");
                $stmt_insert->execute([$category_name, $category_image]);
                header('Location: create.php?success=' . urlencode('Categoría añadida exitosamente.'));
                exit;
            }
        } catch (PDOException $e) {
            header('Location: create.php?error=' . urlencode('Error de base de datos al añadir categoría: ' . $e->getMessage()));
            exit;
        }
    }
}

// Redirect back to the post creation page if not a POST request or category name is empty
header('Location: create.php?error=' . urlencode('Nombre de categoría vacío o solicitud inválida.'));
exit;
?>