<?php
// manage_permissions.php - Superadmin Dashboard to Assign Modules

session_start();
$page_title = '<i class="fa-solid fa-shield-halved"></i> Gestionar Permisos';
require_once(dirname(__FILE__) . '/partials/header.php');

// --- Security Check: Superadmin Only ---
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'superadmin') {
    echo "<div class='alert alert-danger'>Acceso no autorizado.</div>";
    require_once(dirname(__FILE__) . '/partials/footer.php');
    exit;
}

// --- Database Connection ---
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// --- Form Processing ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id_to_update = $_POST['user_id'];
    $selected_modules = $_POST['modules'] ?? [];

    $conn->begin_transaction();
    try {
        $delete_stmt = $conn->prepare("DELETE FROM user_modules WHERE id_user = ?");
        $delete_stmt->bind_param('i', $user_id_to_update);
        $delete_stmt->execute();
        $delete_stmt->close();

        if (!empty($selected_modules)) {
            $insert_stmt = $conn->prepare("INSERT INTO user_modules (id_user, id_module) VALUES (?, ?)");
            foreach ($selected_modules as $module_id) {
                $insert_stmt->bind_param('ii', $user_id_to_update, $module_id);
                $insert_stmt->execute();
            }
            $insert_stmt->close();
        }
        $conn->commit();
        $success_message = "Permisos actualizados correctamente.";
    } catch (Exception $e) {
        $conn->rollback();
        $error_message = "Error al actualizar los permisos: " . $e->getMessage();
    }
}

// --- Data Fetching for Display ---
$admins = $conn->query("SELECT id_user, username, full_name FROM users WHERE role = 'admin'")->fetch_all(MYSQLI_ASSOC);
$modules = $conn->query("SELECT id_module, display_name FROM modules")->fetch_all(MYSQLI_ASSOC);

$permissions = [];
$perm_result = $conn->query("SELECT id_user, id_module FROM user_modules");
while ($row = $perm_result->fetch_assoc()) {
    $permissions[$row['id_user']][$row['id_module']] = true;
}

$conn->close();
?>

<?php if (isset($success_message)): ?>
    <div class="alert alert-success"><?php echo $success_message; ?></div>
<?php endif; ?>
<?php if (isset($error_message)): ?>
    <div class="alert alert-danger"><?php echo $error_message; ?></div>
<?php endif; ?>

<p>Asigna los módulos que cada administrador podrá ver y gestionar.</p>

<?php if (empty($admins)): ?>
    <div class="alert alert-info">No hay administradores para configurar.</div>
<?php else: ?>
    <?php foreach ($admins as $admin): ?>
        <div class="card mb-4" style="background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); margin-bottom: 20px;">
            <div class="card-header" style="border-bottom: 1px solid #e5e7eb; padding-bottom: 15px; margin-bottom: 15px;">
                <h4><?php echo htmlspecialchars($admin['full_name']); ?> <small>(@<?php echo htmlspecialchars($admin['username']); ?>)</small></h4>
            </div>
            <div class="card-body">
                <form action="manage_permissions.php" method="post">
                    <input type="hidden" name="user_id" value="<?php echo $admin['id_user']; ?>">
                    <div class="form-group">
                        <label><strong>Módulos Permitidos:</strong></label>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px; margin-top: 10px;">
                            <?php foreach ($modules as $module): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="modules[]" value="<?php echo $module['id_module']; ?>" id="module_<?php echo $admin['id_user']; ?>_<?php echo $module['id_module']; ?>"
                                        <?php if (isset($permissions[$admin['id_user']][$module['id_module']])) echo 'checked'; ?>>
                                    <label class="form-check-label" for="module_<?php echo $admin['id_user']; ?>_<?php echo $module['id_module']; ?>">
                                        <?php echo htmlspecialchars($module['display_name']); ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Guardar Permisos</button>
                    </div>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php
require_once(dirname(__FILE__) . '/partials/footer.php');
?>
