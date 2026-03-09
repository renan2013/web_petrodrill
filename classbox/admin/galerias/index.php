<?php
session_start();
$page_title = 'Gestor de Graduaciones';
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../../config/database.php';

// Listar de la nueva tabla independiente de graduaciones
try {
    $stmt = $pdo->prepare("
        SELECT g.id_graduacion, g.title, g.created_at, 
        (SELECT COUNT(*) FROM graduaciones_attachments WHERE id_graduacion = g.id_graduacion AND type = 'gallery_image') as photo_count
        FROM graduaciones g
        ORDER BY g.created_at DESC
    ");
    $stmt->execute();
    $graduaciones = $stmt->fetchAll();

} catch (PDOException $e) {
    echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
}
?>

<div class="table-header">
    <h3>Listado de Graduaciones</h3>
    <a href="create.php" class="btn-create">+ Crear Nueva Graduación</a>
</div>

<table>
    <thead>
        <tr>
            <th>Título</th>
            <th>Fecha</th>
            <th>Fotos</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($graduaciones)): ?>
            <tr>
                <td colspan="4" style="text-align:center;">No hay graduaciones creadas todavía.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($graduaciones as $grad): ?>
                <tr>
                    <td><?php echo htmlspecialchars($grad['title']); ?></td>
                    <td><?php echo date('d/m/Y', strtotime($grad['created_at'])); ?></td>
                    <td><span class="badge bg-primary"><?php echo $grad['photo_count']; ?> fotos</span></td>
                    <td class="actions">
                        <a href="attachments.php?id=<?php echo $grad['id_graduacion']; ?>" class="btn-attach"><i class="fa-solid fa-camera"></i> Gestionar Fotos</a>
                        <a href="edit.php?id=<?php echo $grad['id_graduacion']; ?>">Editar</a>
                        <button type="button" class="btn-link delete" style="background:none; border:none; color:#dc3545; cursor:pointer;" onclick="confirmDelete(<?php echo $grad['id_graduacion']; ?>, '<?php echo addslashes($grad['title']); ?>')">Eliminar</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<script>
function confirmDelete(id, title) {
    Swal.fire({
        title: '¿Eliminar graduación?',
        text: `Vas a borrar "${title}" y todas sus fotos. Esta acción no se puede deshacer.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = `delete.php?id=${id}`;
        }
    });
}
</script>

<style>
.badge { padding: 4px 8px; border-radius: 4px; font-size: 0.85em; background-color: #007bff; color: white; }
.btn-attach { background: #2D8FE2; color: white !important; padding: 6px 12px; border-radius: 4px; text-decoration: none; font-size: 0.9em; }
.btn-attach:hover { background: #1A74D2; }
</style>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>