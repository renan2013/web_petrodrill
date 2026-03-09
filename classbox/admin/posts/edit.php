<?php
session_start();
require_once __DIR__ . '/../../config/database.php';

$page_title = 'Editar Publicación';
$error = '';
$success = '';
$post_id = $_GET['id'] ?? null;

if (!$post_id) {
    header('Location: index.php');
    exit;
}

// Fetch categories for the dropdown
$category_stmt = $pdo->query("SELECT id_category, name FROM categories ORDER BY name ASC");
$categories = $category_stmt->fetchAll();

// Fetch current post data
try {
    $stmt = $pdo->prepare("SELECT * FROM posts WHERE id_post = ?");
    $stmt->execute([$post_id]);
    $post = $stmt->fetch();
    if (!$post) {
        header('Location: index.php');
        exit;
    }
} catch (PDOException $e) {
    die("Error de base de datos: " . $e->getMessage());
}

// Handle form submission for updating
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $category_id = $_POST['category_id'] ?? null;
    $synopsis = $_POST['synopsis'] ?? '';
    $content = $_POST['content'] ?? '';
    $orden = $_POST['orden'] ?? 0;
    
    // Instructor fields
    $instructor_name = $_POST['instructor_name'] ?? '';
    $instructor_title = $_POST['instructor_title'] ?? '';
    $show_in_instructors = isset($_POST['show_in_instructors']) ? 1 : 0;

    if (empty($title) || empty($category_id)) {
        $error = 'El título y la categoría son obligatorios.';
    } else {
        $main_image_path = $post['main_image'];
        $instructor_photo = $post['instructor_photo'];

        $upload_dir = __DIR__ . '/../../public/uploads/images/';

        // Handle Main Image Update
        if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] === UPLOAD_ERR_OK) {
            $file_name = uniqid('post_', true) . '-' . basename($_FILES['main_image']['name']);
            if (move_uploaded_file($_FILES['main_image']['tmp_name'], $upload_dir . $file_name)) {
                $main_image_path = $file_name;
            }
        }

        // Handle Instructor Photo Update
        if (isset($_FILES['instructor_photo']) && $_FILES['instructor_photo']['error'] === UPLOAD_ERR_OK) {
            $file_name = uniqid('inst_', true) . '-' . basename($_FILES['instructor_photo']['name']);
            if (move_uploaded_file($_FILES['instructor_photo']['tmp_name'], $upload_dir . $file_name)) {
                $instructor_photo = $file_name;
            }
        }

        if (empty($error)) {
            try {
                $stmt = $pdo->prepare(
                    "UPDATE posts SET title = ?, id_category = ?, synopsis = ?, content = ?, main_image = ?, orden = ?, instructor_name = ?, instructor_title = ?, instructor_photo = ?, show_in_instructors = ? WHERE id_post = ?"
                );
                $stmt->execute([$title, $category_id, $synopsis, $content, $main_image_path, $orden, $instructor_name, $instructor_title, $instructor_photo, $show_in_instructors, $post_id]);
                
                header('Location: index.php?success=' . urlencode('Publicación actualizada correctamente.'));
                exit;
            } catch (PDOException $e) {
                $error = 'Error de base de datos: ' . $e->getMessage();
            }
        }
    }
}

require_once __DIR__ . '/../partials/header.php';
?>

<form action="edit.php?id=<?php echo $post_id; ?>" method="POST" enctype="multipart/form-data" class="styled-form">
    <?php if ($error): ?>
        <div class="error-message"><?php echo $error; ?></div>
    <?php endif; ?>

    <div class="form-group">
        <label for="category_id">Categoría</label>
        <select id="category_id" name="category_id" required class="form-control">
            <?php foreach ($categories as $category): ?>
                <option value="<?php echo $category['id_category']; ?>" <?php echo ($post['id_category'] == $category['id_category']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($category['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
        <label for="title">Título</label>
        <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" required class="form-control">
    </div>

    <div class="form-group">
        <label for="synopsis">Sintesis / Resumen</label>
        <textarea id="synopsis" name="synopsis" rows="3" class="form-control"><?php echo htmlspecialchars($post['synopsis']); ?></textarea>
    </div>

    <div class="form-group">
        <label for="content">Contenido Principal</label>
        <textarea id="content" name="content" rows="10" class="form-control"><?php echo htmlspecialchars($post['content']); ?></textarea>
    </div>

    <div class="form-group">
        <label for="main_image">Imagen Principal</label>
        <?php if ($post['main_image']): 
            $img_url = (strpos($post['main_image'], 'public/') !== false) ? '../../' . $post['main_image'] : '../../public/uploads/images/' . $post['main_image'];
        ?>
            <div class="mb-2"><img src="<?php echo $img_url; ?>" height="80" style="border-radius:4px;"></div>
        <?php endif; ?>
        <input type="file" id="main_image" name="main_image" accept="image/*" class="form-control">
    </div>

    <div class="form-group">
        <label for="orden">Orden de Visualización</label>
        <input type="number" id="orden" name="orden" value="<?php echo htmlspecialchars($post['orden'] ?? 0); ?>" class="form-control">
    </div>

    <div class="instructor-section styled-form mt-4" style="border: 1px solid #ddd; background: #fdfdfd; max-width: 100%;">
        <h5>Información del Instructor (Opcional)</h5>
        <div class="form-group">
            <label for="instructor_name">Nombre del Instructor</label>
            <input type="text" id="instructor_name" name="instructor_name" value="<?php echo htmlspecialchars($post['instructor_name'] ?? ''); ?>" class="form-control">
        </div>
        <div class="form-group">
            <label for="instructor_title">Título / Especialidad</label>
            <input type="text" id="instructor_title" name="instructor_title" value="<?php echo htmlspecialchars($post['instructor_title'] ?? ''); ?>" class="form-control">
        </div>
        <div class="form-group">
            <label for="instructor_photo">Foto del Instructor</label>
            <?php if ($post['instructor_photo']): ?>
                <div class="mb-2"><img src="../../public/uploads/images/<?php echo htmlspecialchars($post['instructor_photo']); ?>" height="80" style="border-radius:4px;"></div>
            <?php endif; ?>
            <input type="file" id="instructor_photo" name="instructor_photo" accept="image/*" class="form-control">
        </div>
        <div class="form-group">
            <label style="display: inline-flex; align-items: center; cursor: pointer;">
                <input type="checkbox" name="show_in_instructors" style="width: auto; margin-right: 10px;" <?php echo ($post['show_in_instructors']) ? 'checked' : ''; ?>> 
                <span>Mostrar en la sección de Instructores del sitio web</span>
            </label>
        </div>
    </div>

    <div class="form-actions mt-4">
        <button type="submit" class="btn-submit">Guardar Cambios</button>
        <a href="index.php" class="btn-cancel">Cancelar</a>
    </div>
</form>

<!-- TinyMCE -->
<script src="<?php echo BASE_URL; ?>/admin/assets/js/tinymce/js/tinymce/tinymce.min.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    tinymce.init({
      selector: 'textarea#content',
      plugins: 'code table lists image link',
      toolbar: 'undo redo | blocks | bold italic | alignleft aligncenter alignright | indent outdent | bullist numlist | code | table | link image',
      images_upload_url: 'image_uploader.php',
      automatic_uploads: true,
      file_picker_types: 'image',
      relative_urls: false,
      remove_script_host: false,
      convert_urls: false
    });
  });
</script>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>