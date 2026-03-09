<?php
session_start();
require_once __DIR__ . '/../../auth/check_auth.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/config.php'; // For BASE_URL

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_cliente_data = $_POST['id_cliente_data'] ?? null;
    $logo_path = ''; // Initialize logo_path

    // Fetch current logo path if updating an existing record
    if ($id_cliente_data) {
        try {
            $stmt_fetch_logo = $pdo->prepare("SELECT logo_path FROM datos_cliente WHERE id_cliente_data = ?");
            $stmt_fetch_logo->execute([$id_cliente_data]);
            $result_logo = $stmt_fetch_logo->fetch(PDO::FETCH_ASSOC);
            if ($result_logo) {
                $logo_path = $result_logo['logo_path'];
            }
        } catch (PDOException $e) {
            error_log("Error fetching current logo path: " . $e->getMessage());
            $_SESSION['error_message'] = "Error al obtener la ruta del logo actual.";
            header('Location: index.php');
            exit;
        }
    }

    $whatsapp_country_code = $_POST['whatsapp_country_code'] ?? '';
    $whatsapp_number = $_POST['whatsapp_number'] ?? '';
    $facebook_url = $_POST['facebook_url'] ?? '';
    $youtube_url = $_POST['youtube_url'] ?? '';
    $instagram_url = $_POST['instagram_url'] ?? '';
    $tiktok_url = $_POST['tiktok_url'] ?? '';
    $address = $_POST['address'] ?? '';
    $google_maps_url = $_POST['google_maps_url'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';

    $upload_dir = __DIR__ . '/../../public/uploads/client_data/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Handle logo upload
    if (isset($_FILES['logo_file']) && $_FILES['logo_file']['error'] === UPLOAD_ERR_OK) {
        $file_tmp_name = $_FILES['logo_file']['tmp_name'];
        $file_name = basename($_FILES['logo_file']['name']);
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'svg'];

        if (in_array($file_ext, $allowed_ext)) {
            $new_file_name = 'logo_' . uniqid() . '.' . $file_ext;
            $target_file = $upload_dir . $new_file_name;

            if (move_uploaded_file($file_tmp_name, $target_file)) {
                $logo_path = 'public/uploads/client_data/' . $new_file_name;
                // Optionally, delete old logo if it exists and is different
                // if (!empty($_POST['current_logo_path']) && $_POST['current_logo_path'] !== $logo_path) {
                //     $old_logo_full_path = __DIR__ . '/../../' . $_POST['current_logo_path'];
                //     if (file_exists($old_logo_full_path)) {
                //         unlink($old_logo_full_path);
                //     }
                // }
            } else {
                $_SESSION['error_message'] = "Error al subir el logo.";
                header('Location: index.php');
                exit;
            }
        } else {
            $_SESSION['error_message'] = "Tipo de archivo de logo no permitido. Solo JPG, JPEG, PNG, GIF, SVG.";
            header('Location: index.php');
            exit;
        }
    }

    try {
        if ($id_cliente_data) {
            // Update existing record
            $stmt = $pdo->prepare("
                UPDATE datos_cliente SET
                logo_path = ?,
                whatsapp_country_code = ?,
                whatsapp_number = ?,
                facebook_url = ?,
                youtube_url = ?,
                instagram_url = ?,
                tiktok_url = ?,
                address = ?,
                google_maps_url = ?,
                email = ?,
                phone = ?
                WHERE id_cliente_data = ?
            ");
            $stmt->execute([
                $logo_path,
                $whatsapp_country_code,
                $whatsapp_number,
                $facebook_url,
                $youtube_url,
                $instagram_url,
                $tiktok_url,
                $address,
                $google_maps_url,
                $email,
                $phone,
                $id_cliente_data
            ]);
            $_SESSION['success_message'] = "Datos del cliente actualizados exitosamente.";
            header('Location: index.php');
            exit;
        } else {
            // Insert new record
            $stmt = $pdo->prepare("
                INSERT INTO datos_cliente (
                    logo_path, whatsapp_country_code, whatsapp_number, facebook_url, youtube_url,
                    instagram_url, tiktok_url, address, google_maps_url, email, phone
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $logo_path,
                $whatsapp_country_code,
                $whatsapp_number,
                $facebook_url,
                $youtube_url,
                $instagram_url,
                $tiktok_url,
                $address,
                $google_maps_url,
                $email,
                $phone
            ]);
            $_SESSION['success_message'] = "Datos del cliente guardados exitosamente.";
            header('Location: index.php');
            exit;
        }
    } catch (PDOException $e) {
        error_log("Error saving client data: " . $e->getMessage());
        $_SESSION['error_message'] = "Error al guardar los datos del cliente: " . $e->getMessage();
        header('Location: index.php');
        exit;
    }
} else {
    header('Location: index.php');
    exit;
}
?>