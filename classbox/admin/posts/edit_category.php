<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../auth/check_auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_category = $_POST['id_category'] ?? 0;
    $new_name = trim($_POST['category_name'] ?? '');
    $category_image = '';

    if ($id_category > 0 && !empty($new_name)) {
        try {
            // Handle image upload
            $img_sql = "";
            $params = [$new_name, $id_category];

            if (isset($_FILES['category_image']) && $_FILES['category_image']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = __DIR__ . '/../../public/uploads/images/';
                $file_name = uniqid('cat_', true) . '-' . basename($_FILES['category_image']['name']);
                if (move_uploaded_file($_FILES['category_image']['tmp_name'], $upload_dir . $file_name)) {
                    $img_sql = ", image = ?";
                    $params = [$new_name, $file_name, $id_category];
                }
            }

            $stmt = $pdo->prepare("UPDATE categories SET name = ? $img_sql WHERE id_category = ?");
            $stmt->execute($params);
            header('Location: create.php?success=' . urlencode('Categoría actualizada exitosamente.'));
            exit;
        } catch (PDOException $e) {
            header('Location: create.php?error=' . urlencode('Error al actualizar la categoría: ' . $e->getMessage()));
            exit;
        }
    }
}

header('Location: create.php?error=' . urlencode('Solicitud inválida para editar categoría.'));
exit;
?>