<?php
session_start();
require_once __DIR__ . '/../../config/database.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$error = '';
$success = '';

// Obtener los datos actuales de la graduación
try {
    $stmt = $pdo->prepare("SELECT * FROM graduaciones WHERE id_graduacion = ?");
    $stmt->execute([$id]);
    $grad = $stmt->fetch();
    if (!$grad) {
        die("Graduación no encontrada.");
    }
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $synopsis = trim($_POST['synopsis'] ?? '');
    $video_url = trim($_POST['video_url'] ?? '');
    $main_image_path = $grad['main_image'];

    if (empty($title)) {
        $error = 'El título es obligatorio.';
    } else {
        // Handle New Image Upload
        if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = __DIR__ . '/../../public/uploads/images/';
            $file_name = uniqid('grad_cover_', true) . '-' . basename($_FILES['main_image']['name']);
            if (move_uploaded_file($_FILES['main_image']['tmp_name'], $upload_dir . $file_name)) {
                $main_image_path = $file_name;
            }
        }

        try {
            $stmt = $pdo->prepare("UPDATE graduaciones SET title = ?, synopsis = ?, main_image = ?, video_url = ? WHERE id_graduacion = ?");
            $stmt->execute([$title, $synopsis, $main_image_path, $video_url, $id]);
            header('Location: index.php?success=' . urlencode('Graduación actualizada con éxito.'));
            exit;
        } catch (PDOException $e) {
            $error = 'Error al actualizar: ' . $e->getMessage();
        }
    }
}

$page_title = 'Editar Graduación';
require_once __DIR__ . '/../partials/header.php';
?>

<div class="styled-form">
    <h3>Editar Graduación: <?php echo htmlspecialchars($grad['title']); ?></h3>
    <form action="edit.php?id=<?php echo $id; ?>" method="POST" enctype="multipart/form-data">
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="form-group">
            <label for="title">Título</label>
            <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($grad['title']); ?>" required class="form-control">
        </div>

        <div class="form-group">
            <label for="main_image">Imagen de Portada Actual</label>
            <?php if ($grad['main_image']): ?>
                <div class="mb-2">
                    <img src="../../public/uploads/images/<?php echo $grad['main_image']; ?>" width="150" alt="Portada">
                </div>
            <?php endif; ?>
            <input type="file" id="main_image" name="main_image" accept="image/*" class="form-control">
        </div>

        <div class="form-group">
            <label for="synopsis">Descripción</label>
            <textarea id="synopsis" name="synopsis" rows="3" class="form-control"><?php echo htmlspecialchars($grad['synopsis']); ?></textarea>
        </div>

        <div class="form-group">
            <label for="video_url">URL de YouTube</label>
            <input type="text" id="video_url" name="video_url" value="<?php echo htmlspecialchars($grad['video_url']); ?>" class="form-control">
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-submit">Guardar Cambios</button>
            <a href="index.php" class="btn-cancel">Cancelar</a>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>