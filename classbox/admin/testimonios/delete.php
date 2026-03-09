<?php
session_start();
require_once __DIR__ . '/../../config/database.php';

$id = $_GET['id'] ?? null;

if ($id) {
    try {
        $stmt = $pdo->prepare("DELETE FROM testimonios WHERE id_testimonio = ?");
        $stmt->execute([$id]);
        header('Location: index.php?success=' . urlencode('Testimonio eliminado.'));
        exit;
    } catch (PDOException $e) {
        header('Location: index.php?error=' . urlencode($e->getMessage()));
        exit;
    }
}
header('Location: index.php');
exit;
?>