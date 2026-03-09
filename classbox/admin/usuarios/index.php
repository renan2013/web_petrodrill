<?php
session_start();
$page_title = 'Administrar Usuarios';
require_once(dirname(__FILE__) . '/../partials/header.php');
require_once(dirname(__FILE__) . '/../../config/database.php');

// Fetch all users
try {
    $stmt = $pdo->query("SELECT id_user, username, full_name FROM users ORDER BY username ASC");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Error de base de datos: ' . $e->getMessage());
}
?>

<div class="table-header">
    <a href="crear_usuario_form.php" class="btn-create">+ Crear Nuevo Usuario</a>
</div>

<table>
    <thead>
        <tr>
            <th>Nombre de Usuario</th>
            <th>Nombre Completo</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($users)): ?>
            <tr>
                <td colspan="3" style="text-align:center;">No se encontraron usuarios.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                    <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                    <td class="actions">
                        <a href="editar_usuario.php?id=<?php echo $user['id_user']; ?>">Editar</a>
                        <a href="eliminar_usuario.php?id=<?php echo $user['id_user']; ?>" class="delete" onclick="return confirm('¿Estás seguro de que quieres eliminar este usuario?');">Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php
require_once(dirname(__FILE__) . '/../partials/footer.php');
?>