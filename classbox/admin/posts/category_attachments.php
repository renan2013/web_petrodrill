<?php
session_start();
require_once __DIR__ . '/../../config/database.php';

$category_id = $_GET['id_category'] ?? null;
if (!$category_id) {
    header('Location: create.php');
    exit;
}

// Fetch category details
$stmt = $pdo->prepare("SELECT name FROM categories WHERE id_category = ?");
$stmt->execute([$category_id]);
$category = $stmt->fetch();
if (!$category) {
    header('Location: create.php');
    exit;
}

$page_title = 'Adjuntos para la Categoría: ' . htmlspecialchars($category['name']);

// Fetch existing attachments for this category
$attach_stmt = $pdo->prepare("SELECT id_attachment, type, value, file_name FROM attachments WHERE id_category = ?");
$attach_stmt->execute([$category_id]);
$attachments = $attach_stmt->fetchAll();

require_once __DIR__ . '/../partials/header.php';
?>

<div class="attachments-container">
    <h3>Adjuntos Existentes</h3>
    <?php if (empty($attachments)): ?>
        <p>No se encontraron adjuntos para esta categoría.</p>
    <?php else: ?>
        <ul class="attachment-list">
            <?php foreach ($attachments as $att): ?>
                <li>
                    <div style="display:flex; align-items:center; gap:10px;">
                        <strong><?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $att['type']))); ?>:</strong>
                        <?php if (in_array($att['type'], ['gallery_image', 'slider_image'])): ?>
                            <img src="../../public/uploads/attachments/<?php echo $att['value']; ?>" style="height: 40px; width: 50px; object-fit: cover; border-radius: 4px;">
                        <?php endif; ?>
                        <span style="font-size: 0.8em; color: #666;"><?php echo htmlspecialchars($att['file_name'] ?? $att['value']); ?></span>
                    </div>
                    <button type="button" class="btn-delete" onclick="confirmDelete(<?php echo $att['id_attachment']; ?>, <?php echo $category_id; ?>)">Eliminar</button>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <hr>

    <h3>Añadir Nuevo Adjunto</h3>
    
    <!-- Form for Gallery Images (Multiple) -->
    <div class="upload-form">
        <h4>Subir Imágenes de Galería (Múltiple)</h4>
        <form action="add_category_attachment.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="category_id" value="<?php echo $category_id; ?>">
            <input type="hidden" name="type" value="gallery_image">
            <input type="file" name="file_upload[]" accept="image/*" multiple required>
            <button type="submit" class="btn-submit">Subir Imágenes</button>
        </form>
    </div>

    <!-- Form for PDF -->
    <div class="upload-form">
        <h4>Subir Archivo PDF</h4>
        <form action="add_category_attachment.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="category_id" value="<?php echo $category_id; ?>">
            <input type="hidden" name="type" value="pdf">
            <input type="file" name="file_upload" accept=".pdf" required>
            <button type="submit" class="btn-submit">Subir PDF</button>
        </form>
    </div>

    <!-- Form for Slider Image -->
    <div class="upload-form">
        <h4>Subir Imagen de Slider</h4>
        <form action="add_category_attachment.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="category_id" value="<?php echo $category_id; ?>">
            <input type="hidden" name="type" value="slider_image">
            <input type="file" name="file_upload" accept="image/*" required>
            <button type="submit" class="btn-submit">Subir Imagen</button>
        </form>
    </div>

    <!-- Form for YouTube Video -->
    <div class="upload-form">
        <h4>Añadir Video de YouTube</h4>
        <form action="add_category_attachment.php" method="POST">
            <input type="hidden" name="category_id" value="<?php echo $category_id; ?>">
            <input type="hidden" name="type" value="youtube">
            <input type="text" name="text_value" placeholder="Introduce la URL o ID del video" required class="form-control">
            <button type="submit" class="btn-submit">Añadir Video</button>
        </form>
    </div>

    <div style="margin-top: 20px;">
        <a href="create.php" class="btn-cancel">Volver a Categorías</a>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const success = urlParams.get('success');
    const error = urlParams.get('error');
    if (success) Swal.fire({ icon: 'success', title: '¡Hecho!', text: success, timer: 3000, showConfirmButton: false });
    if (error) Swal.fire({ icon: 'error', title: 'Error', text: error });
});

function confirmDelete(id, categoryId) {
    Swal.fire({
        title: '¿Eliminar adjunto?',
        text: 'Esta acción no se puede deshacer.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = `delete_category_attachment.php?id=${id}&category_id=${categoryId}`;
        }
    });
}
</script>

<style>
.attachments-container { background-color: #fff; padding: 30px; border-radius: 8px; max-width: 800px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
.attachment-list { list-style-type: none; padding: 0; }
.attachment-list li { background-color: #f8f9fa; padding: 10px 15px; border-radius: 4px; margin-bottom: 10px; display: flex; justify-content: space-between; align-items: center; border: 1px solid #eee; }
.attachment-list li span { word-break: break-all; }
.btn-delete { background-color: #dc3545; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer; font-size: 0.9em; }
.btn-delete:hover { background-color: #c82333; }
.upload-form { border: 1px solid #e9ecef; padding: 20px; border-radius: 8px; margin-top: 20px; }
.upload-form h4 { margin-top: 0; margin-bottom: 15px; }
.upload-form form { display: flex; gap: 10px; align-items: center; }
.upload-form .form-control { flex-grow: 1; }
.btn-submit { background-color: #007bff; color: white; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; white-space: nowrap; }
.btn-submit:hover { background-color: #0056b3; }
.btn-cancel { color: #6c757d; text-decoration: none; font-weight: 500; }
.btn-cancel:hover { text-decoration: underline; }
</style>

<?php
require_once __DIR__ . '/../partials/footer.php';
?>
