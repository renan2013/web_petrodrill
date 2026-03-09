<?php
session_start();
require_once __DIR__ . '/../../config/database.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $synopsis = trim($_POST['synopsis'] ?? '');
    $video_url = trim($_POST['video_url'] ?? '');
    $main_image_path = '';

    if (empty($title)) {
        $error = 'El título de la graduación es obligatorio.';
    } else {
        // Handle Main Image Upload
        if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = __DIR__ . '/../../public/uploads/images/';
            $file_name = uniqid('grad_cover_', true) . '-' . basename($_FILES['main_image']['name']);
            $target_file = $upload_dir . $file_name;
            if (move_uploaded_file($_FILES['main_image']['tmp_name'], $target_file)) {
                $main_image_path = $file_name;
            }
        }

        try {
            // INSERTAR DIRECTAMENTE EN LA TABLA GRADUACIONES
            $stmt = $pdo->prepare("INSERT INTO graduaciones (title, synopsis, main_image, video_url, id_user) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$title, $synopsis, $main_image_path, $video_url, $_SESSION['user_id']]);
            $id_graduacion = $pdo->lastInsertId();

            header('Location: index.php?success=' . urlencode('Graduación creada con éxito. Ahora puedes añadir las fotos.'));
            exit;

        } catch (PDOException $e) {
            $error = 'Error de base de datos: ' . $e->getMessage();
        }
    }
}

$page_title = 'Crear Nueva Graduación';
require_once __DIR__ . '/../partials/header.php';
?>

<div class="styled-form">
    <h3>Nueva Graduación</h3>
    <form action="create.php" method="POST" enctype="multipart/form-data">
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="form-group">
            <label for="title">Título de la Graduación / Evento</label>
            <input type="text" id="title" name="title" placeholder="Ej: Graduación Técnicos 2025" required class="form-control">
        </div>

        <div class="form-group">
            <label for="main_image">Imagen Principal (Miniatura)</label>
            <input type="file" id="main_image" name="main_image" accept="image/*" class="form-control">
            <small>Esta imagen se usará como portada en el listado de graduaciones.</small>
        </div>

        <div class="form-group">
            <label for="synopsis">Descripción Corta</label>
            <textarea id="synopsis" name="synopsis" rows="3" class="form-control" placeholder="Breve resumen del evento..."></textarea>
        </div>

        <div class="form-group">
            <label for="video_url">URL de Video (YouTube)</label>
            <input type="text" id="video_url" name="video_url" placeholder="https://www.youtube.com/watch?v=..." class="form-control">
            <small>Copia y pega el enlace del video de la graduación.</small>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-submit">Crear Graduación y Continuar</button>
            <a href="index.php" class="btn-cancel">Cancelar</a>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>