<?php
session_start();
require_once __DIR__ . '/../../auth/check_auth.php';
require_once __DIR__ . '/../../config/database.php';

$page_title = 'Crear Nueva Publicación';
$error = '';

// Fetch categories for the dropdown
try {
    $category_stmt = $pdo->query("SELECT id_category, name FROM categories ORDER BY name ASC");
    $categories = $category_stmt->fetchAll();
} catch (PDOException $e) {
    die("Could not fetch categories: " . $e->getMessage());
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $category_id = $_POST['category_id'] ?? null;
    $synopsis = $_POST['synopsis'] ?? '';
    $content = $_POST['content'] ?? '';
    $orden = $_POST['orden'] ?? 0; // Captura el nuevo campo 'orden'
    $main_image_path = '';
    
    // Instructor fields
    $instructor_name = $_POST['instructor_name'] ?? '';
    $instructor_title = $_POST['instructor_title'] ?? '';
    $show_in_instructors = isset($_POST['show_in_instructors']) ? 1 : 0;
    $instructor_photo = '';

    if (empty($title) || empty($category_id)) {
        $error = 'El título y la categoría son obligatorios.';
    } else {
        // Handle file upload
        if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = __DIR__ . '/../../public/uploads/images/';
            $file_name = uniqid('post_', true) . '-' . basename($_FILES['main_image']['name']);
            $target_file = $upload_dir . $file_name;

            if (move_uploaded_file($_FILES['main_image']['tmp_name'], $target_file)) {
                $main_image_path = $file_name; // Guardar solo nombre
            } else {
                $error = 'Fallo al subir la imagen principal.';
            }
        }

        // Handle Instructor Photo Upload
        if (empty($error) && isset($_FILES['instructor_photo']) && $_FILES['instructor_photo']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = __DIR__ . '/../../public/uploads/images/';
            $file_name = uniqid('inst_', true) . '-' . basename($_FILES['instructor_photo']['name']);
            if (move_uploaded_file($_FILES['instructor_photo']['tmp_name'], $upload_dir . $file_name)) {
                $instructor_photo = $file_name;
            }
        }

        if (empty($error)) {
            try {
                $stmt = $pdo->prepare(
                    "INSERT INTO posts (title, id_category, synopsis, content, main_image, id_user, orden, instructor_name, instructor_title, instructor_photo, show_in_instructors) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
                );
                $stmt->execute([$title, $category_id, $synopsis, $content, $main_image_path, $_SESSION['user_id'], $orden, $instructor_name, $instructor_title, $instructor_photo, $show_in_instructors]);
                
                header('Location: index.php');
                exit;
            } catch (PDOException $e) {
                $error = 'Error de base de datos: ' . $e->getMessage();
            }
        }
    }
}

require_once __DIR__ . '/../partials/header.php';
?>

<form action="create.php" method="POST" enctype="multipart/form-data" class="styled-form">
    <?php if ($error): ?>
        <div class="error-message"><?php echo $error; ?></div>
    <?php endif; ?>

    <div class="form-group">
        <label for="category_id">Categoría</label>
        <select id="category_id" name="category_id" required class="form-control">
            <option value="">-- Seleccione la categoría --</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?php echo $category['id_category']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
        <label for="title">Título - Nombre - Cliente - Producto - Marca</label>
        <input type="text" id="title" name="title" required class="form-control">
    </div>

    <div class="form-group">
        <label for="synopsis">Sintesis - Testimonio - Resumen</label>
        <textarea id="synopsis" name="synopsis" rows="3" class="form-control"></textarea>
        <small>Una corta descripcción.</small>
    </div>

    <div class="form-group">
        <label for="content">Contenido Principal</label>
        <textarea id="content" name="content" rows="10" class="form-control"></textarea>
        <small>El contenido completo de la publicación. Esto puede no mostrarse para todas las categorías (por ejemplo, Galerías).</small>
    </div>

    <div class="form-group">
        <label for="main_image">Imagen Principal</label>
        <input type="file" id="main_image" name="main_image" class="form-control">
        <small>La imagen principal para el encabezado o miniatura de la publicación.</small>
    </div>

    <div class="form-group">
        <label for="orden">Orden de Visualización</label>
        <input type="number" id="orden" name="orden" value="0" class="form-control">
        <small>Número para ordenar las publicaciones (menor número = primero).</small>
    </div>

    <div class="instructor-section styled-form mt-4" style="border: 1px solid #ddd; background: #fdfdfd;">
        <h5>Información del Instructor (Opcional)</h5>
        <div class="form-group">
            <label for="instructor_name">Nombre del Instructor</label>
            <input type="text" id="instructor_name" name="instructor_name" class="form-control">
        </div>
        <div class="form-group">
            <label for="instructor_title">Título / Especialidad</label>
            <input type="text" id="instructor_title" name="instructor_title" class="form-control" placeholder="Ej: Máster en Diseño Web">
        </div>
        <div class="form-group">
            <label for="instructor_photo">Foto del Instructor</label>
            <input type="file" id="instructor_photo" name="instructor_photo" accept="image/*" class="form-control">
        </div>
        <div class="form-group">
            <label style="display: inline-flex; align-items: center; cursor: pointer;">
                <input type="checkbox" name="show_in_instructors" style="width: auto; margin-right: 10px;"> 
                <span>Mostrar en la sección de Instructores del sitio web</span>
            </label>
        </div>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn-submit">Crear Publicación</button>
        <a href="index.php" class="btn-cancel">Cancelar</a>
    </div>
</form>

<!-- Form to add new categories -->
<div class="category-adder styled-form">
    <h4>Añadir Nueva Categoría</h4>
    <form action="create_category.php" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <input type="text" name="category_name" placeholder="Nuevo nombre de categoría" required class="form-control mb-2">
            <label class="small fw-bold">Imagen de la Categoría (para portada):</label>
            <input type="file" name="category_image" accept="image/*" class="form-control mb-2">
            <button type="submit" class="btn-submit">Añadir</button>
        </div>
    </form>
</div>

<!-- List existing categories with edit/delete buttons -->
<div class="styled-form" style="margin-top: 20px;">
    <h4>Categorías Existentes</h4>
    <?php if (empty($categories)): ?>
        <p>No hay categorías creadas aún.</p>
    <?php else: ?>
        <ul class="category-list">
            <?php foreach ($categories as $category): ?>
                <li>
                    <span><?php echo htmlspecialchars($category['name']); ?></span>
                    <div class="category-actions">
                        <!-- <a href="category_attachments.php?id_category=<?php echo $category['id_category']; ?>" class="btn-attach"><i class="fa-solid fa-paperclip"></i> Adjuntos</a> -->
                        <button type="button" class="btn-edit" onclick="editCategory(<?php echo $category['id_category']; ?>, '<?php echo addslashes($category['name']); ?>')">Editar</button>
                        <button type="button" class="btn-delete" onclick="confirmDelete(<?php echo $category['id_category']; ?>, '<?php echo addslashes($category['name']); ?>')">Eliminar</button>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>

<!-- Hidden edit form -->
<form id="edit-category-form" action="edit_category.php" method="POST" enctype="multipart/form-data" style="display:none;">
    <input type="hidden" name="id_category" id="edit-id">
    <input type="text" name="category_name" id="edit-name">
    <input type="file" name="category_image" id="edit-image">
</form>

<script>
// Show alerts based on URL parameters
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const success = urlParams.get('success');
    const error = urlParams.get('error');

    if (success) {
        Swal.fire({ icon: 'success', title: '¡Éxito!', text: success, timer: 3000, showConfirmButton: false });
    }
    if (error) {
        Swal.fire({ icon: 'error', title: 'Atención', text: error });
    }
});

function editCategory(id, currentName) {
    Swal.fire({
        title: 'Editar Categoría',
        html: `
            <input id="swal-input1" class="swal2-input" value="${currentName}" placeholder="Nombre de categoría">
            <div class="mt-3">
                <label class="small fw-bold">Actualizar Imagen:</label>
                <input id="swal-input2" type="file" class="swal2-file" accept="image/*">
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Guardar',
        cancelButtonText: 'Cancelar',
        preConfirm: () => {
            const name = document.getElementById('swal-input1').value;
            const file = document.getElementById('swal-input2').files[0];
            if (!name || name.trim() === '') {
                Swal.showValidationMessage('¡Debes ingresar un nombre!');
                return false;
            }
            return { name: name.trim(), file: file };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('edit-id').value = id;
            document.getElementById('edit-name').value = result.value.name;
            if (result.value.file) {
                // Transfer file to hidden form
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(result.value.file);
                document.getElementById('edit-image').files = dataTransfer.files;
            }
            document.getElementById('edit-category-form').submit();
        }
    });
}

function confirmDelete(id, name) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: `Vas a eliminar la categoría "${name}". Esta acción no se puede deshacer.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = `delete_category.php?id=${id}`;
        }
    });
}
</script>

<style>
    .category-list { list-style: none; padding: 0; }
    .category-list li { display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px solid #eee; }
    .category-list li:last-child { border-bottom: none; }
    .category-actions { display: flex; gap: 8px; }
    .btn-delete { background-color: #dc3545; color: white; padding: 5px 10px; text-decoration: none; border-radius: 4px; font-size: 0.9em; }
    .btn-delete:hover { background-color: #c82333; }
    .btn-edit { background-color: #2D8FE2; color: white; padding: 5px 10px; border: none; border-radius: 4px; font-size: 0.9em; cursor: pointer; }
    .btn-edit:hover { background-color: #1A74D2; }
    .btn-attach { background-color: #17a2b8; color: white !important; padding: 5px 10px; text-decoration: none; border-radius: 4px; font-size: 0.9em; display: inline-flex; align-items: center; gap: 5px; }
    .btn-attach:hover { background-color: #138496; }
</style>

<!-- TinyMCE -->
<script src="<?php echo BASE_URL; ?>/admin/assets/js/tinymce/js/tinymce/tinymce.min.js"></script>
<script>
    tinymce.init({
      selector: 'textarea#content',
      plugins: 'code table lists image link',
      toolbar: 'undo redo | blocks | bold italic | alignleft aligncenter alignright | indent outdent | bullist numlist | code | table | link image',

      // Image Upload Configuration
      images_upload_url: 'image_uploader.php',
      automatic_uploads: true,
      file_picker_types: 'image',
      
      // Crucial for correct image display
      relative_urls: false,
      remove_script_host: false,
      convert_urls: false
    });
</script>

<?php
require_once __DIR__ . '/../partials/footer.php';
?>