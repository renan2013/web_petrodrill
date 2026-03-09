<?php
session_start();
require_once __DIR__ . '/../../config/database.php';

$page_title = 'Editar Usuario';
$error = '';
$success = '';

$user_id = $_GET['id'] ?? null;

if (!$user_id) {
    header('Location: index.php');
    exit;
}

// Fetch user data
try {
    $stmt = $pdo->prepare("SELECT id_user, username, full_name FROM users WHERE id_user = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        header('Location: index.php?error=' . urlencode('Usuario no encontrado.'));
        exit;
    }
} catch (PDOException $e) {
    die('Error de base de datos: ' . $e->getMessage());
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $full_name = $_POST['full_name'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($username)) {
        $error = 'El nombre de usuario es obligatorio.';
    } else {
        try {
            // Check if username already exists for another user
            $stmt_check = $pdo->prepare("SELECT id_user FROM users WHERE username = ? AND id_user != ?");
            $stmt_check->execute([$username, $user_id]);
            if ($stmt_check->fetch()) {
                $error = 'El nombre de usuario ya existe.';
            } else {
                $sql = "UPDATE users SET username = ?, full_name = ?";
                $params = [$username, $full_name];

                if (!empty($password)) {
                    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
                    $sql .= ", password = ?";
                    $params[] = $hashed_password;
                }

                $sql .= " WHERE id_user = ?";
                $params[] = $user_id;

                $stmt_update = $pdo->prepare($sql);
                $stmt_update->execute($params);

                $success = 'Usuario actualizado exitosamente.';
                // Optionally, redirect after success
                // header('Location: index.php?success=' . urlencode($success));
                // exit;
            }
        } catch (PDOException $e) {
            $error = 'Error de base de datos: ' . $e->getMessage();
        }
    }
}

require_once(dirname(__FILE__) . '/../partials/header.php');
?>

<form action="editar_usuario.php?id=<?php echo htmlspecialchars($user_id); ?>" method="POST" class="styled-form">
    <?php if ($error): ?>
        <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="error-message alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <div class="form-group">
        <label for="username">Nombre de Usuario</label>
        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required class="form-control">
        <small>El nombre de usuario único para el administrador.</small>
    </div>

    <div class="form-group">
        <label for="password">Nueva Contraseña (dejar en blanco para no cambiar)</label>
        <input type="password" id="password" name="password" class="form-control">
        <small>La nueva contraseña para el administrador. Déjalo en blanco si no quieres cambiarla.</small>
    </div>

    <div class="form-group">
        <label for="full_name">Nombre Completo</label>
        <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" class="form-control">
        <small>El nombre completo del administrador (opcional).</small>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn-submit">Actualizar Usuario</button>
        <a href="index.php" class="btn-cancel">Cancelar</a>
    </div>
</form>

<?php
require_once(dirname(__FILE__) . '/../partials/footer.php');
?>