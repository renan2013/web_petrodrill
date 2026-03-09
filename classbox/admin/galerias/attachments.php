<?php
session_start();
require_once __DIR__ . '/../../config/database.php';

$id_graduacion = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$error = '';
$success = '';

// Verificar que la graduación existe
try {
    $stmt = $pdo->prepare("SELECT title FROM graduaciones WHERE id_graduacion = ?");
    $stmt->execute([$id_graduacion]);
    $grad = $stmt->fetch();
    if (!$grad) {
        die("Graduación no encontrada.");
    }
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

// PROCESAR SUBIDAS Y ELIMINACIONES
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['type'] ?? '';
    
    if ($type === 'youtube') {
        $video_url = trim($_POST['video_url'] ?? '');
        if (!empty($video_url)) {
            $stmt = $pdo->prepare("INSERT INTO graduaciones_attachments (id_graduacion, type, value) VALUES (?, 'youtube', ?)");
            $stmt->execute([$id_graduacion, $video_url]);
            $success = "Video añadido correctamente.";
        }
    } elseif ($type === 'gallery_image') {
        $upload_dir = __DIR__ . '/../../public/uploads/images/';
        $total_files = count($_FILES['fotos']['name']);
        $uploaded_count = 0;

        for ($i = 0; $i < $total_files; $i++) {
            if ($_FILES['fotos']['error'][$i] === UPLOAD_ERR_OK) {
                $file_extension = pathinfo($_FILES['fotos']['name'][$i], PATHINFO_EXTENSION);
                $file_name = uniqid('grad_photo_', true) . '.' . $file_extension;
                if (move_uploaded_file($_FILES['fotos']['tmp_name'][$i], $upload_dir . $file_name)) {
                    $stmt = $pdo->prepare("INSERT INTO graduaciones_attachments (id_graduacion, type, value) VALUES (?, 'gallery_image', ?)");
                    $stmt->execute([$id_graduacion, $file_name]);
                    $uploaded_count++;
                }
            }
        }
        if ($uploaded_count > 0) $success = "$uploaded_count fotos subidas con éxito.";
    } elseif ($type === 'pdf') {
        if (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = __DIR__ . '/../../public/uploads/attachments/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
            $file_name = uniqid('grad_pdf_', true) . '-' . basename($_FILES['pdf_file']['name']);
            if (move_uploaded_file($_FILES['pdf_file']['tmp_name'], $upload_dir . $file_name)) {
                $stmt = $pdo->prepare("INSERT INTO graduaciones_attachments (id_graduacion, type, value) VALUES (?, 'pdf', ?)");
                $stmt->execute([$id_graduacion, $file_name]);
                $success = "PDF subido con éxito.";
            }
        }
    }
}

// Eliminar adjunto
if (isset($_GET['delete_id'])) {
    $id_att = (int)$_GET['delete_id'];
    try {
        $stmt = $pdo->prepare("SELECT type, value FROM graduaciones_attachments WHERE id_attachment = ?");
        $stmt->execute([$id_att]);
        $att = $stmt->fetch();
        if ($att) {
            if ($att['type'] !== 'youtube') {
                $folder = ($att['type'] === 'pdf') ? 'attachments' : 'images';
                $file_to_delete = __DIR__ . '/../../public/uploads/' . $folder . '/' . $att['value'];
                if (file_exists($file_to_delete)) unlink($file_to_delete);
            }
            $pdo->prepare("DELETE FROM graduaciones_attachments WHERE id_attachment = ?")->execute([$id_att]);
            header("Location: attachments.php?id=$id_graduacion&success=" . urlencode('Adjunto eliminado.'));
            exit;
        }
    } catch (PDOException $e) { $error = "Error al eliminar: " . $e->getMessage(); }
}

$page_title = 'Adjuntos de Graduación: ' . htmlspecialchars($grad['title']);
require_once __DIR__ . '/../partials/header.php';

// Obtener todos los adjuntos
$stmt_att = $pdo->prepare("SELECT * FROM graduaciones_attachments WHERE id_graduacion = ? ORDER BY id_attachment DESC");
$stmt_att->execute([$id_graduacion]);
$attachments = $stmt_att->fetchAll();
?>

<div class="content">
    <h3><i class="fa fa-paperclip"></i> Gestionar Adjuntos: <?php echo htmlspecialchars($grad['title']); ?></h3>
    <a href="index.php" class="btn-cancel mb-4" style="display:inline-block; margin-bottom:20px;"><i class="fa fa-arrow-left"></i> Volver al listado</a>

    <?php if ($success): ?><div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-danger"><?php echo $error; ?></div><?php endif; ?>

    <div class="row" style="display:flex; gap:30px; align-items:flex-start;">
        <!-- LISTADO DE ADJUNTOS EXISTENTES (COLUMNA IZQUIERDA) -->
        <div style="flex: 1;">
            <h4>Adjuntos Existentes</h4>
            <?php if (empty($attachments)): ?>
                <p class="text-muted">No hay adjuntos todavía.</p>
            <?php else: ?>
                <div class="attachment-list">
                    <?php foreach ($attachments as $att): ?>
                        <div class="att-item" style="display:flex; justify-content:space-between; align-items:center; background:#fff; padding:15px; border-radius:8px; margin-bottom:10px; border: 1px solid #eee;">
                            <div style="display:flex; align-items:center; gap:15px;">
                                <div class="icon" style="font-size: 1.5rem; color: #007bff;">
                                    <?php if ($att['type'] === 'pdf') echo '<i class="fa-solid fa-file-pdf text-danger"></i>'; 
                                          elseif ($att['type'] === 'youtube') echo '<i class="fa-brands fa-youtube text-danger"></i>';
                                          else echo '<i class="fa-solid fa-image text-primary"></i>'; ?>
                                </div>
                                <div>
                                    <strong style="display:block; text-transform:uppercase; font-size:0.8em;"><?php echo $att['type']; ?></strong>
                                    <span style="font-size:0.9em; color:#666;"><?php echo htmlspecialchars($att['value']); ?></span>
                                </div>
                            </div>
                            <a href="attachments.php?id=<?php echo $id_graduacion; ?>&delete_id=<?php echo $att['id_attachment']; ?>" 
                               class="text-danger" onclick="return confirm('¿Eliminar este adjunto?')"><i class="fa fa-trash"></i></a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- FORMULARIOS DE SUBIDA (COLUMNA DERECHA) -->
        <div style="width: 400px; background: #f4f6f9; padding: 25px; border-radius: 12px; border: 1px solid #ddd;">
            <h4 style="margin-top:0;">Añadir Nuevo</h4>
            
            <!-- Subir Fotos -->
            <div class="sub-form" style="margin-bottom:20px; padding-bottom:20px; border-bottom:1px solid #ccc;">
                <strong><i class="fa fa-images"></i> Subir Fotos (Múltiple)</strong>
                <form action="attachments.php?id=<?php echo $id_graduacion; ?>" method="POST" enctype="multipart/form-data" style="margin-top:10px;">
                    <input type="hidden" name="type" value="gallery_image">
                    <input type="file" name="fotos[]" multiple accept="image/*" class="form-control" required style="margin-bottom:10px;">
                    <button type="submit" class="btn-submit btn-sm" style="width:100%;">Subir Fotos</button>
                </form>
            </div>

            <!-- Subir Video -->
            <div class="sub-form" style="margin-bottom:20px; padding-bottom:20px; border-bottom:1px solid #ccc;">
                <strong><i class="fab fa-youtube"></i> Enlace de YouTube</strong>
                <form action="attachments.php?id=<?php echo $id_graduacion; ?>" method="POST" style="margin-top:10px;">
                    <input type="hidden" name="type" value="youtube">
                    <input type="text" name="video_url" class="form-control" placeholder="https://www.youtube.com/watch?v=..." required style="margin-bottom:10px;">
                    <button type="submit" class="btn-submit btn-sm" style="width:100%; background: #FF0000; color:white; border:none;">Añadir Video</button>
                </form>
            </div>

            <!-- Subir PDF -->
            <div class="sub-form">
                <strong><i class="fa fa-file-pdf"></i> Archivo PDF</strong>
                <form action="attachments.php?id=<?php echo $id_graduacion; ?>" method="POST" enctype="multipart/form-data" style="margin-top:10px;">
                    <input type="hidden" name="type" value="pdf">
                    <input type="file" name="pdf_file" accept=".pdf" class="form-control" required style="margin-bottom:10px;">
                    <button type="submit" class="btn-submit btn-sm" style="width:100%; background: #6c757d; color:white; border:none;">Subir PDF</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>