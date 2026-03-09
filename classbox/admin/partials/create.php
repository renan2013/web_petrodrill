<?php
require_once __DIR__ . '/../../config/database.php';

$page_title = 'Create Post';
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
    $main_image_path = '';

    if (empty($title) || empty($category_id)) {
        $error = 'Title and Category are required.';
    } else {
        // Handle file upload
        if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = __DIR__ . '/../../public/uploads/images/';
            $file_name = uniqid('post_', true) . '-' . basename($_FILES['main_image']['name']);
            $target_file = $upload_dir . $file_name;

            if (move_uploaded_file($_FILES['main_image']['tmp_name'], $target_file)) {
                $main_image_path = 'public/uploads/images/' . $file_name;
            } else {
                $error = 'Failed to upload main image.';
            }
        }

        if (empty($error)) {
            try {
                $stmt = $pdo->prepare(
                    "INSERT INTO posts (title, id_category, synopsis, content, main_image) VALUES (?, ?, ?, ?, ?)"
                );
                $stmt->execute([$title, $category_id, $synopsis, $content, $main_image_path]);
                
                header('Location: index.php');
                exit;
            } catch (PDOException $e) {
                $error = 'Database error: ' . $e->getMessage();
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
        <label for="category_id">Category</label>
        <select id="category_id" name="category_id" required>
            <option value="">-- Select a Category --</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?php echo $category['id_category']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
        <label for="title">Title</label>
        <input type="text" id="title" name="title" required>
    </div>

    <div class="form-group">
        <label for="synopsis">Synopsis</label>
        <textarea id="synopsis" name="synopsis" rows="3"></textarea>
        <small>A short summary of the post.</small>
    </div>

    <div class="form-group">
        <label for="content">Main Content</label>
        <textarea id="content" name="content" rows="10"></textarea>
        <small>The full content of the post. This may not be shown for all categories (e.g., Galleries).</small>
    </div>

    <div class="form-group">
        <label for="main_image">Main Image</label>
        <input type="file" id="main_image" name="main_image">
        <small>The primary image for the post header or thumbnail.</small>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn-submit">Create Post</button>
        <a href="index.php" class="btn-cancel">Cancel</a>
    </div>
</form>

<!-- A simple form to add new categories on the fly -->
<div class="category-adder">
    <h4>Add New Category</h4>
    <form action="create_category.php" method="POST">
        <input type="text" name="category_name" placeholder="New category name" required>
        <button type="submit">Add</button>
    </form>
</div>

<style>
/* Using styles from previous forms for consistency */
.styled-form { background-color: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); max-width: 800px; }
.form-group { margin-bottom: 20px; }
.form-group label { display: block; font-weight: 500; margin-bottom: 8px; }
.form-group input, .form-group textarea, .form-group select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
.form-group small { color: #666; font-size: 0.9em; }
.form-actions { margin-top: 30px; }
.btn-submit { background-color: #007bff; color: white; padding: 12px 20px; border: none; border-radius: 5px; cursor: pointer; }
.btn-cancel { color: #6c757d; text-decoration: none; margin-left: 15px; }
.error-message { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 10px; border-radius: 4px; margin-bottom: 20px; }
.category-adder { margin-top: 40px; padding: 20px; background-color: #f8f9fa; border: 1px solid #e9ecef; border-radius: 8px; max-width: 800px; }
.category-adder form { display: flex; }
.category-adder input { flex-grow: 1; padding: 10px; border: 1px solid #ddd; border-radius: 4px 0 0 4px; }
.category-adder button { padding: 10px 15px; border: 1px solid #ddd; border-left: none; background-color: #e9ecef; cursor: pointer; border-radius: 0 4px 4px 0; }
</style>

<!-- TinyMCE Script -->
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
  tinymce.init({
    selector: 'textarea#content',
    plugins: 'code table lists image link',
    toolbar: 'undo redo | blocks | bold italic | alignleft aligncenter alignright | indent outdent | bullist numlist | code | table | link image'
  });
</script>

<?php
require_once __DIR__ . '/../partials/footer.php';
?>