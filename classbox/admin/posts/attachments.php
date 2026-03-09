<?php
session_start();
require_once __DIR__ . '/../../config/database.php';

$post_id = $_GET['post_id'] ?? null;
if (!$post_id) {
    header('Location: index.php');
    exit;
}

// Fetch post details and category name
$stmt = $pdo->prepare("SELECT p.title, c.name as category_name FROM posts p JOIN categories c ON p.id_category = c.id_category WHERE p.id_post = ?");
$stmt->execute([$post_id]);
$post = $stmt->fetch();
if (!$post) {
    header('Location: index.php');
    exit;
}

$page_title = 'Attachments for: ' . htmlspecialchars($post['title']);

// Fetch existing attachments
$attach_stmt = $pdo->prepare("SELECT id_attachment, type, value FROM attachments WHERE id_post = ?");
$attach_stmt->execute([$post_id]);
$attachments = $attach_stmt->fetchAll();

require_once __DIR__ . '/../partials/header.php';
?>

<div class="attachments-container">
    <h3>Existing Attachments</h3>
    <?php if (empty($attachments)): ?>
        <p>No attachments found for this post.</p>
    <?php else: ?>
        <ul>
            <?php foreach ($attachments as $att): ?>
                <li>
                    <div style="display:flex; align-items:center; gap:10px;">
                        <strong><?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $att['type']))); ?>:</strong>
                        <?php if (in_array($att['type'], ['gallery_image', 'slider_image'])): ?>
                            <img src="../../public/uploads/attachments/<?php echo $att['value']; ?>" style="height: 40px; width: 50px; object-fit: cover; border-radius: 4px;">
                        <?php endif; ?>
                        <span style="font-size: 0.8em; color: #666;"><?php echo htmlspecialchars($att['value']); ?></span>
                    </div>
                    <button type="button" class="btn-link" style="background:none; border:none; color:#dc3545; cursor:pointer; font-weight:bold;" onclick="confirmDelete(<?php echo $att['id_attachment']; ?>, <?php echo $post_id; ?>)">Eliminar</button>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <hr>

    <h3>Add New Attachment</h3>
    
    <!-- Form for Gallery Images (Multiple) - FIRST -->
    <?php 
    $cat_name = $post['category_name'];
    $show_gallery = stripos($cat_name, 'gallery') !== false || 
                    stripos($cat_name, 'Graduaciones') !== false || 
                    stripos($cat_name, 'Diplomado') !== false || 
                    stripos($cat_name, 'Galería') !== false;
    
    if ($show_gallery): ?>
    <div class="upload-form">
        <h4>Upload Gallery Images (Multiple)</h4>
        <form action="add_attachment.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
            <input type="hidden" name="type" value="gallery_image">
            <input type="file" name="file_upload[]" accept="image/*" multiple required>
            <button type="submit">Upload All Images</button>
        </form>
    </div>
    <?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const success = urlParams.get('success');
    const error = urlParams.get('error');
    if (success) Swal.fire({ icon: 'success', title: '¡Hecho!', text: success, timer: 3000, showConfirmButton: false });
    if (error) Swal.fire({ icon: 'error', title: 'Error', text: error });
});

function confirmDelete(id, postId) {
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
            window.location.href = `delete_attachment.php?id=${id}&post_id=${postId}`;
        }
    });
}
</script>

    <!-- Form for PDF -->
    <div class="upload-form">
        <h4>Upload PDF File</h4>
        <form action="add_attachment.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
            <input type="hidden" name="type" value="pdf">
            <input type="file" name="file_upload" accept=".pdf" required>
            <button type="submit">Upload PDF</button>
        </form>
    </div>

    <!-- Form for Slider Image -->
    <div class="upload-form">
        <h4>Upload Slider Image</h4>
        <form action="add_attachment.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
            <input type="hidden" name="type" value="slider_image">
            <input type="file" name="file_upload" accept="image/*" required>
            <button type="submit">Upload Slider Image</button>
        </form>
    </div>

    <!-- Form for YouTube Video -->
    <div class="upload-form">
        <h4>Add YouTube Video</h4>
        <form action="add_attachment.php" method="POST">
            <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
            <input type="hidden" name="type" value="youtube">
            <input type="text" name="text_value" placeholder="Enter YouTube Video URL or ID" required>
            <button type="submit">Add Video</button>
        </form>
    </div>
</div>

<style>
.attachments-container { background-color: #fff; padding: 30px; border-radius: 8px; max-width: 800px; }
.attachments-container ul { list-style-type: none; padding: 0; }
.attachments-container li { background-color: #f8f9fa; padding: 10px 15px; border-radius: 4px; margin-bottom: 10px; display: flex; justify-content: space-between; align-items: center; }
.attachments-container li span { word-break: break-all; }
.attachments-container a.delete { color: #dc3545; text-decoration: none; font-weight: bold; }
.upload-form { border: 1px solid #e9ecef; padding: 20px; border-radius: 8px; margin-top: 20px; }
.upload-form h4 { margin-top: 0; }
.upload-form form { display: flex; gap: 10px; }
.upload-form input[type="file"], .upload-form input[type="text"] { flex-grow: 1; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
.upload-form button { background-color: #007bff; color: white; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; }
</style>

<?php
require_once __DIR__ . '/../partials/footer.php';
?>