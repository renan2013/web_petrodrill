<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../auth/check_auth.php'; // Ensure user is logged in

$menu_id = $_GET['id'] ?? null;

if (!$menu_id) {
    // No ID provided, redirect to the list
    header('Location: index.php');
    exit;
}

try {
    // Prepare and execute the deletion
    $stmt = $pdo->prepare("DELETE FROM menus WHERE id_menu = ?");
    $stmt->execute([$menu_id]);

    // Redirect back to the menu list
    header('Location: index.php');
    exit;
} catch (PDOException $e) {
    // If there's a database error, you can handle it here.
    // For a production app, you might want to log the error.
    header('Location: index.php?error=' . urlencode('Error al eliminar el elemento del menú: ' . $e->getMessage()));
    exit;
}
?>