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

$page_title = 'Crear Menú';
$error = '';

// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $url = normalize_url($_POST['url'] ?? '', BASE_URL);
    $display_order = $_POST['display_order'] ?? 0;


    $has_submenus = isset($_POST['has_submenus']);
    $submenus_data = $_POST['submenus'] ?? [];

    if (empty($title) || empty($url)) {
        $error = 'El título y la URL son obligatorios.';
    } else {
        try {
            // Start a transaction
            $pdo->beginTransaction();

            // Insert the main menu item (parent_id will be NULL for top-level menus created here)
            $stmt = $pdo->prepare("INSERT INTO menus (title, url, display_order, parent_id) VALUES (?, ?, ?, NULL)");
            $stmt->execute([$title, $url, $display_order]);
            $parent_id = $pdo->lastInsertId();

            // Insert submenus if any
            if ($has_submenus && !empty($submenus_data)) {
                $submenu_stmt = $pdo->prepare("INSERT INTO menus (title, url, display_order, parent_id) VALUES (?, ?, ?, ?)");
                foreach ($submenus_data as $submenu) {
                    $submenu_title = $submenu['title'] ?? '';
                    $submenu_url = $submenu['url'] ?? '';
                    $submenu_order = $submenu['display_order'] ?? 0;

                    if (!empty($submenu_title) && !empty($submenu_url)) {
                        $submenu_stmt->execute([$submenu_title, $submenu_url, $submenu_order, $parent_id]);
                    }
                }
            }

            $pdo->commit(); // Commit the transaction
            header('Location: index.php');
            exit;
        } catch (PDOException $e) {
            $pdo->rollBack(); // Rollback on error
            $error = 'Error de base de datos: ' . $e->getMessage();
        }
    }
}

require_once __DIR__ . '/../partials/header.php';
?>

<form action="create.php" method="POST" class="styled-form">
    <?php if ($error): ?>
        <div class="error-message"><?php echo $error; ?></div>
    <?php endif; ?>

    <div class="form-group">
        <label for="title">Título</label>
        <input type="text" id="title" name="title" required>
        <small>El texto que se mostrará en la navegación (ej. "Inicio", "Nosotros").</small>
    </div>

    <div class="form-group">
        <label for="url">URL</label>
        <input type="text" id="url" name="url" required>
        <small>El enlace de destino (ej. "/index.php", "/contacto.php").</small>
    </div>

    <div class="form-group">
        <label for="display_order">Orden de Visualización</label>
        <input type="number" id="display_order" name="display_order" value="0">
        <small>Un número menor aparecerá primero en el menú.</small>
    </div>

    <div class="form-group">
        <input type="checkbox" id="has_submenus" name="has_submenus">
        <label for="has_submenus">Este menú tendrá submenús</label>
    </div>

    <div id="submenu_fields" style="display: none;">
        <h3>Submenús</h3>
        <div id="submenus_container">
            <!-- Submenu fields will be added here by JavaScript -->
        </div>
        <button type="button" id="add_submenu_btn" class="btn-submit" style="background-color: #28a745;">Añadir Submenú</button>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn-submit">Crear Menú</button>
        <a href="index.php" class="btn-cancel">Cancelar</a>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const hasSubmenusCheckbox = document.getElementById('has_submenus');
    const submenuFieldsDiv = document.getElementById('submenu_fields');
    const addSubmenuBtn = document.getElementById('add_submenu_btn');
    const submenusContainer = document.getElementById('submenus_container');
    let submenuCount = 0;

    function toggleSubmenuFields() {
        if (hasSubmenusCheckbox.checked) {
            submenuFieldsDiv.style.display = 'block';
        } else {
            submenuFieldsDiv.style.display = 'none';
            submenusContainer.innerHTML = ''; // Clear submenus when unchecked
            submenuCount = 0;
        }
    }

    function addSubmenuField() {
        const submenuItem = document.createElement('div');
        submenuItem.classList.add('submenu-item');
        submenuItem.innerHTML = `
            <button type="button" class="remove-submenu-btn">&times;</button>
            <div class="form-group">
                <label for="submenu_title_${submenuCount}">Título del Submenú</label>
                <input type="text" id="submenu_title_${submenuCount}" name="submenus[${submenuCount}][title]" required>
            </div>
            <div class="form-group">
                <label for="submenu_url_${submenuCount}">URL del Submenú</label>
                <input type="text" id="submenu_url_${submenuCount}" name="submenus[${submenuCount}][url]" required>
            </div>
            <div class="form-group">
                <label for="submenu_order_${submenuCount}">Orden de Visualización del Submenú</label>
                <input type="number" id="submenu_order_${submenuCount}" name="submenus[${submenuCount}][display_order]" value="0">
            </div>
        `;
        submenusContainer.appendChild(submenuItem);

        submenuItem.querySelector('.remove-submenu-btn').addEventListener('click', function() {
            submenuItem.remove();
        });

        submenuCount++;
    }

    hasSubmenusCheckbox.addEventListener('change', toggleSubmenuFields);
    addSubmenuBtn.addEventListener('click', addSubmenuField);

    // Initial state
    toggleSubmenuFields();
});
</script>

<?php
require_once __DIR__ . '/../partials/footer.php';
?>