<?php
session_start();
$page_title = 'Administrar Menús';
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../../config/database.php';

// Fetch all menu items from the database
$stmt = $pdo->query("SELECT id_menu, title, url, display_order FROM menus WHERE parent_id IS NULL ORDER BY display_order ASC");
$menus = $stmt->fetchAll();
?>

<div class="table-header">
    <a href="create.php" class="btn-create">+ Crear Nuevo Menú</a>
</div>

<table>
    <thead>
        <tr>
            <th>Orden</th>
            <th>Título</th>
            <th>URL</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($menus)): ?>
            <tr>
                <td colspan="4" style="text-align:center;">No se encontraron menús.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($menus as $menu): ?>
                <tr>
                    <td><?php echo htmlspecialchars($menu['display_order']); ?></td>
                    <td><?php echo htmlspecialchars($menu['title']); ?></td>
                    <td><?php echo htmlspecialchars($menu['url']); ?></td>
                    <td class="actions">
                        <a href="edit.php?id=<?php echo $menu['id_menu']; ?>">Editar</a>
                        <a href="delete.php?id=<?php echo $menu['id_menu']; ?>" class="delete" onclick="return confirm('¿Estás seguro de que quieres eliminar este elemento del menú?');">Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<style>
.table-header { text-align: right; margin-bottom: 20px; }
.btn-create { background-color: #28a745; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; }
.btn-create:hover { background-color: #218838; }
</style>

<?php
require_once __DIR__ . '/../partials/footer.php';
?>