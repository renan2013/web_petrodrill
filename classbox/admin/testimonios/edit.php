<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../auth/check_auth.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: index.php');
    exit;
}

$error = '';

// Fetch current data
$stmt = $pdo->prepare("SELECT * FROM testimonios WHERE id_testimonio = ?");
$stmt->execute([$id]);
$testimonio = $stmt->fetch();

if (!$testimonio) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $profesion = trim($_POST['profesion'] ?? '');
    $comentario = trim($_POST['comentario'] ?? '');
    $video_iframe = trim($_POST['video_iframe'] ?? '');
    $foto = $testimonio['foto'];

    if (empty($nombre) || empty($comentario)) {
        $error = 'Nombre y comentario son obligatorios.';
    } else {
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = __DIR__ . '/../../public/uploads/images/';
            $file_name = uniqid('test_', true) . '-' . basename($_FILES['foto']['name']);
            if (move_uploaded_file($_FILES['foto']['tmp_name'], $upload_dir . $file_name)) {
                $foto = $file_name;
            }
        }

        try {
            $stmt = $pdo->prepare("UPDATE testimonios SET nombre = ?, profesion = ?, comentario = ?, foto = ?, video_iframe = ? WHERE id_testimonio = ?");
            $stmt->execute([$nombre, $profesion, $comentario, $foto, $video_iframe, $id]);
            header('Location: index.php?success=' . urlencode('Testimonio actualizado con éxito.'));
            exit;
        } catch (PDOException $e) {
            $error = 'Error de base de datos: ' . $e->getMessage();
        }
    }
}

$page_title = 'Editar Testimonio';
require_once __DIR__ . '/../partials/header.php';
?>

<div class="styled-form">
    <h3>Editar Testimonio</h3>
    <form action="edit.php?id=<?php echo $id; ?>" method="POST" enctype="multipart/form-data">
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="form-group">
            <label for="nombre">Nombre del Estudiante</label>
            <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($testimonio['nombre']); ?>" required class="form-control">
        </div>

        <div class="form-group">
            <label for="profesion">Profesión / Título</label>
            <input type="text" id="profesion" name="profesion" value="<?php echo htmlspecialchars($testimonio['profesion']); ?>" class="form-control">
        </div>

        <div class="form-group">
            <label for="comentario">Comentario / Testimonio</label>
            <textarea id="comentario" name="comentario" rows="5" required class="form-control"><?php echo htmlspecialchars($testimonio['comentario']); ?></textarea>
        </div>

        <div class="form-group">
            <label for="video_iframe">Código de Video de Google Drive (Opcional)</label>
            <textarea id="video_iframe" name="video_iframe" rows="3" class="form-control" placeholder="Pega aquí el código <iframe> de Drive..."><?php echo htmlspecialchars($testimonio['video_iframe'] ?? ''); ?></textarea>
            <small>Si añades un video, se mostrará en lugar de la foto en el sitio web.</small>
        </div>

        <div class="form-group">
            <label for="foto">Foto del Estudiante</label>
            <?php if ($testimonio['foto']): ?>
                <div class="mb-2"><img src="../../public/uploads/images/<?php echo htmlspecialchars($testimonio['foto']); ?>" height="80" style="border-radius:4px;"></div>
            <?php endif; ?>
            <input type="file" id="foto" name="foto" accept="image/*" class="form-control">
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-submit">Actualizar Testimonio</button>
            <a href="index.php" class="btn-cancel">Cancelar</a>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>