<?php
session_start();

// --- Authentication Check ---
if (!isset($_SESSION['user_id'])) {
    header('Location: /classbox/auth/login.php');
    exit;
}

require_once __DIR__ . '/../../config/database.php';

$matricula_id = $_GET['id'] ?? null;

if (!$matricula_id) {
    header('Location: index.php?error=' . urlencode('ID de matrícula no especificado.'));
    exit;
}

try {
    // 1. Obtener las rutas de los archivos asociados a la matrícula
    $stmt_select = $pdo->prepare("SELECT foto, documentos FROM formulario_matricula WHERE id_matricula = ?");
    $stmt_select->execute([$matricula_id]);
    $matricula = $stmt_select->fetch();

    if ($matricula) {
        // 2. Eliminar la foto si existe
        if (!empty($matricula['foto'])) {
            $foto_path = realpath(__DIR__ . '/../../../learner/' . $matricula['foto']);
            if ($foto_path && file_exists($foto_path)) {
                unlink($foto_path);
            }
        }

        // 3. Eliminar el documento PDF si existe
        if (!empty($matricula['documentos'])) {
            $documentos_path = realpath(__DIR__ . '/../../../learner/' . $matricula['documentos']);
            if ($documentos_path && file_exists($documentos_path)) {
                unlink($documentos_path);
            }
        }

        // 4. Eliminar el registro de la base de datos
        $stmt_delete = $pdo->prepare("DELETE FROM formulario_matricula WHERE id_matricula = ?");
        $stmt_delete->execute([$matricula_id]);

        header('Location: index.php?success=' . urlencode('Solicitud eliminada con éxito.'));
        exit;
    } else {
        header('Location: index.php?error=' . urlencode('Solicitud no encontrada.'));
        exit;
    }
} catch (PDOException $e) {
    header('Location: index.php?error=' . urlencode('Error al eliminar la solicitud: ' . $e->getMessage()));
    exit;
}
?>