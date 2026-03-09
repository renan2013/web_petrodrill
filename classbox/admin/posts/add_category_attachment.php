<?php
session_start();
require_once __DIR__ . '/../../config/database.php';

$category_id = $_POST['category_id'] ?? null;
$type = $_POST['type'] ?? null;
$error = '';

if (!$category_id || !$type) {
    header('Location: category_attachments.php?id_category=' . $category_id . '&error=' . urlencode('Datos incompletos.'));
    exit;
}

// Ensure upload directory exists
$upload_dir = __DIR__ . '/../../public/uploads/attachments/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// 1. Handle YouTube (Text value)
if ($type === 'youtube') {
    $value = $_POST['text_value'] ?? '';
    if (empty($value)) {
        $error = 'URL o ID de YouTube es obligatorio.';
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO attachments (id_category, type, value, file_name) VALUES (?, ?, ?, ?)");
            $stmt->execute([$category_id, $type, $value, 'Video YouTube']);
            header('Location: category_attachments.php?id_category=' . $category_id . '&success=' . urlencode('Video añadido exitosamente.'));
            exit;
        } catch (PDOException $e) {
            $error = 'Error de base de datos: ' . $e->getMessage();
        }
    }
} 
// 2. Handle File Uploads
else {
    $file_input = $_FILES['file_upload'] ?? null;

    if ($file_input && !empty($file_input['name'])) {
        $uploaded_count = 0;
        
        // Normalize to array
        $names = is_array($file_input['name']) ? $file_input['name'] : [$file_input['name']];
        $tmp_names = is_array($file_input['tmp_name']) ? $file_input['tmp_name'] : [$file_input['tmp_name']];
        $errors = is_array($file_input['error']) ? $file_input['error'] : [$file_input['error']];

        for ($i = 0; $i < count($names); $i++) {
            if ($errors[$i] === UPLOAD_ERR_OK) {
                $original_name = basename($names[$i]);
                $file_name = uniqid($type . '_', true) . '-' . $original_name;
                $target_file = $upload_dir . $file_name;

                if (move_uploaded_file($tmp_names[$i], $target_file)) {
                    try {
                        $stmt = $pdo->prepare("INSERT INTO attachments (id_category, type, value, file_name) VALUES (?, ?, ?, ?)");
                        $stmt->execute([$category_id, $type, $file_name, $original_name]);
                        $uploaded_count++;
                    } catch (PDOException $e) {
                        $error = 'Error de BD: ' . $e->getMessage();
                        break;
                    }
                } else {
                    $error = 'Error al mover el archivo.';
                }
            }
        }

        if ($uploaded_count > 0 && empty($error)) {
            header('Location: category_attachments.php?id_category=' . $category_id . '&success=' . urlencode("$uploaded_count archivo(s) subido(s) correctamente."));
            exit;
        }
    } else {
        $error = 'No se recibió ningún archivo.';
    }
}

if (!empty($error)) {
    header('Location: category_attachments.php?id_category=' . $category_id . '&error=' . urlencode($error));
    exit;
}
?>
