<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../auth/check_auth.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $profesion = trim($_POST['profesion'] ?? '');
    $comentario = trim($_POST['comentario'] ?? '');
    $video_iframe = trim($_POST['video_iframe'] ?? '');
    $foto = '';

    if (empty($nombre) || empty($comentario)) {
        $error = 'Nombre y comentario son obligatorios.';
    } else {
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = __DIR__ . '/../../public/uploads/images/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
            $file_name = uniqid('test_', true) . '-' . basename($_FILES['foto']['name']);
            if (move_uploaded_file($_FILES['foto']['tmp_name'], $upload_dir . $file_name)) {
                $foto = $file_name;
            }
        }

        try {
            $stmt = $pdo->prepare("INSERT INTO testimonios (nombre, profesion, comentario, foto, video_iframe) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$nombre, $profesion, $comentario, $foto, $video_iframe]);
            header('Location: index.php?success=' . urlencode('Testimonio añadido con éxito.'));
            exit;
        } catch (PDOException $e) {
            $error = 'Error de base de datos: ' . $e->getMessage();
        }
    }
}

$page_title = 'Añadir Testimonio';
require_once __DIR__ . '/../partials/header.php';
?>

<div class="styled-form">
    <h3>Nuevo Testimonio</h3>
    <form action="create.php" method="POST" enctype="multipart/form-data">
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="form-group">
            <label for="nombre">Nombre del Estudiante</label>
            <input type="text" id="nombre" name="nombre" required class="form-control">
        </div>

        <div class="form-group">
            <label for="profesion">Profesión / Título</label>
            <input type="text" id="profesion" name="profesion" placeholder="Ej: Estudiante de Diseño" class="form-control">
        </div>

        <div class="form-group">
            <label for="comentario">Comentario / Testimonio</label>
            <textarea id="comentario" name="comentario" rows="5" required class="form-control"></textarea>
        </div>

        <div class="form-group">
            <label for="video_iframe">Código de Video de Google Drive (Opcional)</label>
            <textarea id="video_iframe" name="video_iframe" rows="3" class="form-control" placeholder="Pega aquí el código <iframe> de Drive..."></textarea>
            <small>Si añades un video, se mostrará en lugar de la foto en el sitio web.</small>
        </div>

        <div class="form-group">
            <label for="foto">Foto del Estudiante (Opcional)</label>
            <input type="file" id="foto" name="foto" accept="image/*" class="form-control">
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-submit">Guardar Testimonio</button>
            <a href="index.php" class="btn-cancel">Cancelar</a>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>