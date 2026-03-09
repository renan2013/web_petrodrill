<?php
session_start();
require_once __DIR__ . '/../../config/database.php';

function normalize_url($url, $base_url) {
    // If it's already a full external URL, return as is.
    if (preg_match('/^(http|https):\/\//i', $url)) {
        return $url;
    }

    // Remove BASE_URL prefix if present (e.g., if user copied full URL)
    if (strpos($url, $base_url) === 0) {
        $url = substr($url, strlen($base_url));
    }

    // Explicitly remove "web_site/" prefix from the URL.
    // This is done early to ensure it's gone before other processing.
    $url = str_replace('web_site/', '', $url);

    // Ensure single leading slash for root-relative URLs, or no leading slash for app-relative URLs.
    if (substr($url, 0, 1) === '/') {
        return '/' . ltrim($url, '/'); // Root-relative
    } else {
        return ltrim($url, '/'); // App-relative
    }
}

$page_title = 'Editar Menú';
$error = '';
$menu_id = $_GET['id'] ?? null;

if (!$menu_id) {
    header('Location: index.php');
    exit;
}

// Fetch the menu item and its direct submenus
try {
    // Fetch the main menu item
    $stmt = $pdo->prepare("SELECT * FROM menus WHERE id_menu = ?");
    $stmt->execute([$menu_id]);
    $menu = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$menu) {
        header('Location: index.php');
        exit;
    }

    // Fetch its submenus
    $submenu_stmt = $pdo->prepare("SELECT * FROM menus WHERE parent_id = ? ORDER BY display_order ASC");
    $submenu_stmt->execute([$menu_id]);
    $submenus = $submenu_stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error de base de datos: " . $e->getMessage());
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $url = normalize_url($_POST['url'] ?? '#', BASE_URL); // Default to '#' if it has submenus
    $display_order = $_POST['display_order'] ?? 0;


    $has_submenus = isset($_POST['has_submenus']);
    $submenus_data = $_POST['submenus'] ?? [];

    if (empty($title)) {
        $error = 'El título es obligatorio.';
    } else {
        try {
            $pdo->beginTransaction();

            // Update the main menu item
            $update_stmt = $pdo->prepare("UPDATE menus SET title = ?, url = ?, display_order = ? WHERE id_menu = ?");
            $update_stmt->execute([$title, $url, $display_order, $menu_id]);

            // First, delete all existing submenus for this parent menu
            $delete_submenus_stmt = $pdo->prepare("DELETE FROM menus WHERE parent_id = ?");
            $delete_submenus_stmt->execute([$menu_id]);

            // Now, re-insert the submitted submenus
            if ($has_submenus && !empty($submenus_data)) {
                $insert_submenu_stmt = $pdo->prepare("INSERT INTO menus (title, url, display_order, parent_id) VALUES (?, ?, ?, ?)");
                foreach ($submenus_data as $submenu) {
                    $submenu_title = $submenu['title'] ?? '';
                    $submenu_url = $submenu['url'] ?? '';
                    $submenu_order = $submenu['display_order'] ?? 0;

                    if (!empty($submenu_title) && !empty($submenu_url)) {
                        $insert_submenu_stmt->execute([$submenu_title, $submenu_url, $submenu_order, $menu_id]);
                    }
                }
            }

            $pdo->commit();
            header('Location: index.php');
            exit;

        } catch (PDOException $e) {
            $pdo->rollBack();
            $error = 'Error de base de datos: ' . $e->getMessage();
        }
    }
}

require_once __DIR__ . '/../partials/header.php';
?>

<form action="edit.php?id=<?php echo $menu_id; ?>" method="POST" class="styled-form">
    <?php if ($error): ?>
        <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <div class="form-group">
        <label for="title">Título</label>
        <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($menu['title']); ?>" required>
    </div>

    <div class="form-group">
        <label for="url">URL</label>
        <input type="text" id="url" name="url" value="<?php echo htmlspecialchars($menu['url']); ?>" required>
        <small>Para los menús padre, esta URL se usa solo si no tiene submenús. Usa '#' si es solo un contenedor.</small>
    </div>

    <div class="form-group">
        <label for="display_order">Orden de Visualización</label>
        <input type="number" id="display_order" name="display_order" value="<?php echo htmlspecialchars($menu['display_order']); ?>">
    </div>

    <div class="form-group">
        <input type="checkbox" id="has_submenus" name="has_submenus" <?php echo !empty($submenus) ? 'checked' : ''; ?>>
        <label for="has_submenus">Este menú tiene submenús</label>
    </div>

    <div id="submenu_fields" style="<?php echo empty($submenus) ? 'display: none;' : ''; ?>">
        <h3>Submenús</h3>
        <div id="submenus_container">
            <!-- Existing submenus will be populated here by JavaScript -->
        </div>
        <button type="button" id="add_submenu_btn" class="btn-submit" style="background-color: #28a745;">+ Añadir Submenú</button>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn-submit">Actualizar Menú</button>
        <a href="index.php" class="btn-cancel">Cancelar</a>
    </div>
</form>

<style>
/* Using the same styles from create.php for consistency */
.styled-form { background-color: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); max-width: 600px; }
.form-group { margin-bottom: 20px; }
.form-group label { display: block; font-weight: 500; margin-bottom: 8px; }
.form-group input[type="text"], .form-group input[type="number"] { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
.form-group small { color: #666; font-size: 0.9em; }
.form-actions { margin-top: 30px; }
.btn-submit { background-color: #007bff; color: white; padding: 12px 20px; border: none; border-radius: 5px; cursor: pointer; }
.btn-submit:hover { background-color: #0056b3; }
.btn-cancel { color: #6c757d; text-decoration: none; margin-left: 15px; }
.error-message { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 10px; border-radius: 4px; margin-bottom: 20px; }
.submenu-item { border: 1px solid #eee; padding: 15px; margin-bottom: 15px; border-radius: 5px; background-color: #f9f9f9; position: relative; }
.remove-submenu-btn { position: absolute; top: 10px; right: 10px; background-color: #dc3545; color: white; border: none; border-radius: 50%; width: 25px; height: 25px; font-size: 14px; cursor: pointer; line-height: 1; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const hasSubmenusCheckbox = document.getElementById('has_submenus');
    const submenuFieldsDiv = document.getElementById('submenu_fields');
    const addSubmenuBtn = document.getElementById('add_submenu_btn');
    const submenusContainer = document.getElementById('submenus_container');
    let submenuCount = 0;

    // JSON-encoded data for existing submenus
    const existingSubmenus = <?php echo json_encode($submenus); ?>;

    function toggleSubmenuFields() {
        submenuFieldsDiv.style.display = hasSubmenusCheckbox.checked ? 'block' : 'none';
    }

    function addSubmenuField(title = '', url = '', order = 0) {
        const submenuItem = document.createElement('div');
        submenuItem.classList.add('submenu-item');
        
        const uniqueId = submenuCount;
        submenuItem.innerHTML = `
            <button type="button" class="remove-submenu-btn">&times;</button>
            <div class="form-group">
                <label for="submenu_title_${uniqueId}">Título del Submenú</label>
                <input type="text" id="submenu_title_${uniqueId}" name="submenus[${uniqueId}][title]" value="${title}" required>
            </div>
            <div class="form-group">
                <label for="submenu_url_${uniqueId}">URL del Submenú</label>
                <input type="text" id="submenu_url_${uniqueId}" name="submenus[${uniqueId}][url]" value="${url}" required>
            </div>
            <div class="form-group">
                <label for="submenu_order_${uniqueId}">Orden de Visualización del Submenú</label>
                <input type="number" id="submenu_order_${uniqueId}" name="submenus[${uniqueId}][display_order]" value="${order}">
            </div>
        `;
        submenusContainer.appendChild(submenuItem);

        submenuItem.querySelector('.remove-submenu-btn').addEventListener('click', function() {
            submenuItem.remove();
        });

        submenuCount++;
    }

    // Populate form with existing submenus
    if (existingSubmenus.length > 0) {
        existingSubmenus.forEach(submenu => {
            addSubmenuField(submenu.title, submenu.url, submenu.display_order);
        });
    }

    hasSubmenusCheckbox.addEventListener('change', toggleSubmenuFields);
    addSubmenuBtn.addEventListener('click', () => addSubmenuField());

    // Initial state
    toggleSubmenuFields();
});
</script>

<?php
require_once __DIR__ . '/../partials/footer.php';
?>
