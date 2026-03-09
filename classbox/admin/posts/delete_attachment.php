<?php
session_start();

// --- Authentication Check ---
// Logic from auth/check_auth.php is now directly inside this script.
if (!isset($_SESSION['user_id'])) {
    // If not set, the user is not logged in. Redirect to the login page.
    header('Location: /classbox/auth/login.php');
    exit;
}

require_once __DIR__ . '/../../config/database.php';

$attachment_id = $_GET['id'] ?? null;
$post_id = $_GET['post_id'] ?? null; // For redirecting back

if (!$attachment_id || !$post_id) {
    // This condition should not be met, but we keep it as a safeguard.
    header('Location: index.php?error=missing_parameters');
    exit;
}

try {
    // Get the attachment details to find the file path
    $stmt_select = $pdo->prepare("SELECT type, value FROM attachments WHERE id_attachment = ?");
    $stmt_select->execute([$attachment_id]);
    $attachment = $stmt_select->fetch();

    if ($attachment) {
        // If it's a file-based attachment, delete the file
        if ($attachment['type'] !== 'youtube') {
            $file_path = realpath(__DIR__ . '/../../' . $attachment['value']);
            if ($file_path && file_exists($file_path)) {
                unlink($file_path);
            }
        }

        // Delete the record from the database
        $stmt_delete = $pdo->prepare("DELETE FROM attachments WHERE id_attachment = ?");
        $stmt_delete->execute([$attachment_id]);
    }

    // Redirect back to the attachments page
    header('Location: attachments.php?post_id=' . $post_id);
    exit;
} catch (PDOException $e) {
    die("Error: Could not delete the attachment. " . $e->getMessage());
}
?>